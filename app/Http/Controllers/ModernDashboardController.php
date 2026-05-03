<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\item;
use App\Models\customerinfo;
use App\Models\company;
use App\Models\invoice;
use App\Models\CreditnotesInvoice;
use App\Models\Bank;
use App\Models\Expense;
use App\Models\customerledgerdetails;
use App\Models\salesitem;
use App\Support\NepaliDate;

class ModernDashboardController extends Controller
{
    public function index()
    {
        $breadcrumb = [
            'subtitle' => 'Modern Dashboard',
            'title' => 'Modern Dashboard',
            'link' => 'Modern Dashboard'
        ];

        $today = now()->toDateString();
        $month = now()->month;
        $year = now()->year;

        $stats = [
            'total_items'          => item::count(),
            'low_stock_items'      => item::where('showwarning', '>', 0)->where('quantity', '>=', 1)->where('check_remove_ofs', 0)->whereRaw('quantity <= showwarning')->count(),
            'out_of_stock_items'   => item::where('quantity', '=', 0)->where('check_remove_ofs', 0)->count(),
            'total_customers'      => customerinfo::count(),
            'total_companies'      => company::count(),
            'today_invoices'       => invoice::whereDate('inv_date', $today)->count(),
            'month_invoices'       => invoice::whereMonth('inv_date', $month)->whereYear('inv_date', $year)->count(),
            'today_credit_notes'   => CreditnotesInvoice::whereDate('inv_date', $today)->count(),
            'month_credit_notes'   => CreditnotesInvoice::whereMonth('inv_date', $month)->whereYear('inv_date', $year)->count(),
            'bank_balance'         => (float) Bank::sum('amount'),
            'month_expenses'       => (float) Expense::whereMonth('date', $month)->whereYear('date', $year)->sum('amount'),
            'pending_payments'     => max(0, (float) (customerledgerdetails::sum('debit') - customerledgerdetails::sum('credit'))),
        ];

        $startDate = now()->subDays(29)->startOfDay();
        $endDate   = now()->endOfDay();
        $dailySalesRaw = invoice::select(DB::raw('DATE(inv_date) as date'), DB::raw('SUM(total) as total'))
            ->whereBetween('inv_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $dailyLabels = [];
        $dailyData   = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dailyLabels[] = now()->subDays($i)->format('M d');
            $dailyData[]   = (float) ($dailySalesRaw[$date] ?? 0);
        }
        $dailySales = ['labels' => $dailyLabels, 'data' => $dailyData];

        $topItemsRaw = salesitem::select('itemid', DB::raw('SUM(quantity) as total_qty'))
            ->whereNotNull('itemid')
            ->where('date', '>=', now()->subDays(30))
            ->groupBy('itemid')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $itemIds   = $topItemsRaw->pluck('itemid')->filter()->toArray();
        $itemNames = item::whereIn('id', $itemIds)->pluck('itemsname', 'id');

        $topLabels = [];
        $topData   = [];
        foreach ($topItemsRaw as $row) {
            $topLabels[] = $itemNames[$row->itemid] ?? 'Unknown';
            $topData[]   = (float) $row->total_qty;
        }
        if (empty($topLabels)) {
            $topLabels = ['No Data'];
            $topData   = [0];
        }
        $topItems = ['labels' => $topLabels, 'data' => $topData];

        $paymentModesRaw = invoice::select('inv_type', DB::raw('COUNT(*) as count'))
            ->whereMonth('inv_date', $month)->whereYear('inv_date', $year)
            ->groupBy('inv_type')
            ->pluck('count', 'inv_type');

        $payLabels = [];
        $payData   = [];
        $modeMap   = ['cash' => 'Cash', 'credit' => 'Credit', 'bank' => 'Bank'];
        foreach ($modeMap as $key => $label) {
            if (isset($paymentModesRaw[$key])) {
                $payLabels[] = $label;
                $payData[]   = (int) $paymentModesRaw[$key];
            }
        }
        if (empty($payLabels)) {
            $payLabels = ['Cash', 'Credit', 'Bank'];
            $payData   = [0, 0, 0];
        }
        $paymentModes = ['labels' => $payLabels, 'data' => $payData];

        $inStock    = item::where('quantity', '>', 0)->where('check_remove_ofs', 0)->count();
        $lowStock   = item::where('showwarning', '>', 0)->where('quantity', '>=', 1)->where('check_remove_ofs', 0)->whereRaw('quantity <= showwarning')->count();
        $outOfStock = item::where('quantity', '=', 0)->where('check_remove_ofs', 0)->count();
        $stockStatus = ['labels' => ['In Stock', 'Low Stock', 'Out of Stock'], 'data' => [$inStock, $lowStock, $outOfStock]];

        $recentInvoicesRaw = invoice::join('customerinfos', 'invoices.customerid', '=', 'customerinfos.id')
            ->select('invoices.id', 'invoices.total as amount', 'invoices.inv_type as type', 'invoices.inv_date as date', 'customerinfos.name as customer')
            ->whereDate('invoices.inv_date', $today)
            ->orderByDesc('invoices.inv_date')
            ->orderByDesc('invoices.id')
            ->get();

        $recentInvoices = [];
        foreach ($recentInvoicesRaw as $inv) {
            $isPaid = ($inv->type === 'cash') || customerledgerdetails::where('invoiceid', $inv->id)->where('credit', '>', 0)->exists();
            $recentInvoices[] = [
                'invoice_id' => $inv->id,
                'id'       => 'INV-' . $inv->id,
                'customer' => $inv->customer,
                'amount'   => (float) $inv->amount,
                'type'     => ucfirst($inv->type),
                'date'     => NepaliDate::adToBsString($inv->date, 'en'),
                'status'   => $isPaid ? 'paid' : 'pending',
            ];
        }

        $recentPaymentsRaw = customerledgerdetails::join('customerinfos', 'customerledgerdetails.customerid', '=', 'customerinfos.id')
            ->select('customerinfos.name as customer', 'customerledgerdetails.credit as amount', 'customerledgerdetails.date', 'customerledgerdetails.id', 'customerledgerdetails.bank_deposit', 'customerledgerdetails.counter_deposit', 'customerledgerdetails.particulars', 'customerledgerdetails.voucher_type')
            ->where('customerledgerdetails.invoicetype', 'payment')
            ->whereDate('customerledgerdetails.date', $today)
            ->orderByDesc('customerledgerdetails.date')
            ->orderByDesc('customerledgerdetails.id')
            ->get();

        $recentPayments = [];
        foreach ($recentPaymentsRaw as $pay) {
            $mode = trim($pay->voucher_type ?? '');
            if (empty($mode)) {
                $mode = trim($pay->particulars ?? '');
            }
            if (empty($mode)) {
                $mode = 'Cash';
            }

            $recentPayments[] = [
                'payment_id' => $pay->id,
                'customer' => $pay->customer,
                'amount'   => (float) $pay->amount,
                'mode'     => $mode,
                'date'     => NepaliDate::adToBsString($pay->date, 'en'),
                'receipt'  => 'RCP-' . $pay->id,
            ];
        }

        $lowStockAlertsRaw = item::where('showwarning', '>', 0)
            ->where('quantity', '>=', 1)
            ->where('check_remove_ofs', 0)
            ->whereRaw('quantity <= showwarning')
            ->select('itemsname as item', 'quantity as current', 'showwarning as threshold', 'companyid')
            ->with('company')
            ->limit(5)
            ->get();

        $lowStockAlerts = [];
        foreach ($lowStockAlertsRaw as $alert) {
            $lowStockAlerts[] = [
                'item'      => $alert->item,
                'current'   => (float) $alert->current,
                'threshold' => (float) $alert->threshold,
                'company'   => $alert->company->name ?? 'Unknown',
            ];
        }

        return view('dashboard.moderndashboard', compact(
            'breadcrumb',
            'stats',
            'dailySales',
            'topItems',
            'paymentModes',
            'stockStatus',
            'recentInvoices',
            'recentPayments',
            'lowStockAlerts'
        ));
    }

    public function getInvoiceData(Request $req)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $invoiceId = $req->query('invoiceid');

        $invoice = invoice::with('customer')->find($invoiceId);
        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        $items = salesitem::where('invoiceid', $invoiceId)
            ->with('item')
            ->get()
            ->map(function($si) {
                return [
                    'item_id' => $si->itemid,
                    'item_name' => $si->item ? $si->item->itemsname : $si->unstockedname,
                    'mrp' => $si->mrp,
                    'quantity' => $si->quantity,
                    'unit' => $si->unit,
                    'price' => $si->price,
                    'subtotal' => $si->subtotal,
                ];
            });

        $isPaid = ($invoice->inv_type === 'cash') || customerledgerdetails::where('invoiceid', $invoiceId)->where('credit', '>', 0)->exists();

        return response()->json([
            'invoice_id' => $invoice->id,
            'invoice_no' => 'INV-' . $invoice->id,
            'type' => $invoice->inv_type,
            'date' => $invoice->inv_date,
            'nepali_date' => NepaliDate::adToBsString($invoice->inv_date, 'en'),
            'subtotal' => $invoice->subtotal,
            'discount' => $invoice->discount,
            'total' => $invoice->total,
            'notes' => $invoice->notes,
            'added_by' => $invoice->added_by,
            'status' => $isPaid ? 'paid' : 'pending',
            'customer' => [
                'id' => $invoice->customerid,
                'name' => $invoice->customer ? $invoice->customer->name : 'N/A',
                'address' => $invoice->customer ? $invoice->customer->address : 'N/A',
                'pan_no' => $invoice->customer ? $invoice->customer->pan_no : null,
                'contact_no' => $invoice->customer ? $invoice->customer->contact_no : null,
                'email' => $invoice->customer ? $invoice->customer->email : null,
            ],
            'items' => $items,
        ]);
    }
}
