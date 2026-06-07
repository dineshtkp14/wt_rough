<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use App\Models\item;
use App\Models\customerinfo;
use App\Models\company;
use App\Models\invoice;
use App\Models\CreditnotesInvoice;
use App\Models\CreditnotesSalesitem;
use App\Models\Bank;
use App\Models\Expense;
use App\Models\customerledgerdetails;
use App\Models\salesitem;
use App\Models\BackupInvoice;
use App\Models\BackupSalesItem;
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

        $topSellingItems = salesitem::query()
            ->leftJoin('items', 'salesitems.itemid', '=', 'items.id')
            ->select('salesitems.itemid')
            ->selectRaw("COALESCE(items.itemsname, salesitems.unstockedname, 'Unknown') as item_name")
            ->selectRaw('SUM(salesitems.quantity) as total_qty')
            ->selectRaw('SUM(salesitems.subtotal) as total_amount')
            ->selectRaw('COUNT(DISTINCT salesitems.invoiceid) as invoice_count')
            ->selectRaw('MAX(salesitems.date) as last_sale_date')
            ->groupBy('salesitems.itemid', DB::raw("COALESCE(items.itemsname, salesitems.unstockedname, 'Unknown')"))
            ->orderByDesc('total_qty')
            ->limit(100)
            ->get()
            ->map(function ($row) {
                return [
                    'item_id' => $row->itemid,
                    'item_name' => $row->item_name,
                    'total_qty' => (float) $row->total_qty,
                    'total_amount' => (float) $row->total_amount,
                    'invoice_count' => (int) $row->invoice_count,
                    'last_sale_date' => $row->last_sale_date ? NepaliDate::adToBsString($row->last_sale_date, 'en') : '-',
                ];
            });

        $topLabels = [];
        $topData   = [];
        foreach ($topSellingItems->take(5) as $row) {
            $topLabels[] = $row['item_name'];
            $topData[]   = $row['total_qty'];
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
            ->orderByDesc('invoices.inv_date')
            ->orderByDesc('invoices.id')
            ->limit(8)
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
                'is_today' => $inv->date && date('Y-m-d', strtotime($inv->date)) === $today,
                'status'   => $isPaid ? 'paid' : 'pending',
            ];
        }

        $recentPaymentsRaw = customerledgerdetails::join('customerinfos', 'customerledgerdetails.customerid', '=', 'customerinfos.id')
            ->select('customerinfos.name as customer', 'customerledgerdetails.credit as amount', 'customerledgerdetails.date', 'customerledgerdetails.id', 'customerledgerdetails.bank_deposit', 'customerledgerdetails.counter_deposit', 'customerledgerdetails.particulars', 'customerledgerdetails.voucher_type')
            ->where('customerledgerdetails.invoicetype', 'payment')
            ->orderByDesc('customerledgerdetails.date')
            ->orderByDesc('customerledgerdetails.id')
            ->limit(8)
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
                'is_today' => $pay->date && date('Y-m-d', strtotime($pay->date)) === $today,
                'receipt'  => 'RCP-' . $pay->id,
            ];
        }

        $recentCreditNotesRaw = CreditnotesInvoice::leftJoin('customerinfos', 'creditnotes_invoices.customerid', '=', 'customerinfos.id')
            ->select('creditnotes_invoices.id', 'creditnotes_invoices.total as amount', 'creditnotes_invoices.inv_date as date', 'customerinfos.name as customer')
            ->orderByDesc('creditnotes_invoices.inv_date')
            ->orderByDesc('creditnotes_invoices.id')
            ->limit(8)
            ->get();

        $recentCreditNotes = [];
        foreach ($recentCreditNotesRaw as $note) {
            $recentCreditNotes[] = [
                'credit_note_id' => $note->id,
                'id' => 'CN-' . $note->id,
                'customer' => $note->customer ?? 'N/A',
                'amount' => (float) $note->amount,
                'date' => $note->date ? NepaliDate::adToBsString($note->date, 'en') : '-',
                'is_today' => $note->date && date('Y-m-d', strtotime($note->date)) === $today,
            ];
        }

        $recentDeletedInvoicesRaw = BackupInvoice::leftJoin('customerinfos', 'backup_invoices.customerid', '=', 'customerinfos.id')
            ->select('backup_invoices.id', 'backup_invoices.invoice_id', 'backup_invoices.total as amount', 'backup_invoices.inv_type as type', 'backup_invoices.inv_date as date', 'backup_invoices.created_at', 'customerinfos.name as customer')
            ->orderByDesc('backup_invoices.created_at')
            ->orderByDesc('backup_invoices.id')
            ->limit(8)
            ->get();

        $recentDeletedInvoices = [];
        foreach ($recentDeletedInvoicesRaw as $deleted) {
            $recentDeletedInvoices[] = [
                'backup_id' => $deleted->id,
                'invoice_id' => $deleted->invoice_id,
                'id' => 'INV-' . $deleted->invoice_id,
                'customer' => $deleted->customer ?? 'N/A',
                'amount' => (float) $deleted->amount,
                'type' => ucfirst($deleted->type ?? '-'),
                'date' => $deleted->date ? NepaliDate::adToBsString($deleted->date, 'en') : '-',
                'deleted_at' => $deleted->created_at ? \Carbon\Carbon::parse($deleted->created_at)->format('Y-m-d H:i') : '-',
                'is_today' => $deleted->date && date('Y-m-d', strtotime($deleted->date)) === $today,
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
            'recentCreditNotes',
            'recentDeletedInvoices',
            'lowStockAlerts',
            'topSellingItems'
        ));
    }

    public function getInvoiceData(Request $req)
    {
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
                'phoneno' => $invoice->customer ? $invoice->customer->phoneno : null,
                'email' => $invoice->customer ? $invoice->customer->email : null,
            ],
            'items' => $items,
        ]);
    }

    public function getCreditNoteData(Request $req)
    {
        $invoiceId = $req->query('invoiceid');

        $invoice = CreditnotesInvoice::find($invoiceId);
        if (!$invoice) {
            return response()->json(['error' => 'Credit note not found'], 404);
        }

        $customer = customerinfo::find($invoice->customerid);
        $items = CreditnotesSalesitem::where('invoiceid', $invoiceId)
            ->get()
            ->map(function ($si) {
                $itemInfo = item::where('id', $si->itemid)->select('id', 'itemsname', 'mrp', 'unit')->first();

                return [
                    'item_id' => $itemInfo ? $itemInfo->id : ($si->itemid ?? '-'),
                    'item_name' => $itemInfo ? $itemInfo->itemsname : ($si->unstockedname ?? ''),
                    'mrp' => $itemInfo ? $itemInfo->mrp : ($si->mrp ?? null),
                    'quantity' => $si->quantity,
                    'unit' => $itemInfo ? $itemInfo->unit : ($si->unit ?? null),
                    'price' => $si->price,
                    'subtotal' => $si->subtotal,
                ];
            });

        return response()->json([
            'invoice_id' => $invoice->id,
            'invoice_no' => 'CN-' . $invoice->id,
            'type' => 'Credit Note',
            'date' => $invoice->inv_date,
            'nepali_date' => $invoice->inv_date ? NepaliDate::adToBsString($invoice->inv_date, 'en') : null,
            'subtotal' => $invoice->subtotal,
            'discount' => $invoice->discount,
            'total' => $invoice->total,
            'notes' => $invoice->notes,
            'added_by' => $invoice->added_by,
            'customer' => [
                'id' => $invoice->customerid,
                'name' => $customer ? $customer->name : 'N/A',
                'address' => $customer ? $customer->address : 'N/A',
                'pan_no' => $customer ? $customer->pan_no : null,
                'phoneno' => $customer ? $customer->phoneno : null,
                'email' => $customer ? $customer->email : null,
            ],
            'items' => $items,
        ]);
    }

    public function getDeletedInvoiceData(Request $req)
    {
        $invoiceId = $req->query('invoiceid');

        $invoice = BackupInvoice::where('invoice_id', $invoiceId)->latest('id')->first();
        if (!$invoice) {
            return response()->json(['error' => 'Deleted invoice not found'], 404);
        }

        $customer = customerinfo::find($invoice->customerid);
        $items = BackupSalesItem::where('invoiceid', $invoiceId)
            ->get()
            ->map(function ($si) {
                $itemInfo = item::where('id', $si->itemid)->select('id', 'itemsname', 'mrp', 'unit')->first();

                return [
                    'item_id' => $itemInfo ? $itemInfo->id : ($si->itemid ?? '-'),
                    'item_name' => $itemInfo ? $itemInfo->itemsname : ($si->unstockedname ?? ''),
                    'mrp' => $itemInfo ? $itemInfo->mrp : null,
                    'quantity' => $si->quantity,
                    'unit' => $itemInfo ? $itemInfo->unit : ($si->unit ?? null),
                    'price' => $si->price,
                    'subtotal' => $si->subtotal,
                ];
            });

        return response()->json([
            'invoice_id' => $invoice->invoice_id,
            'invoice_no' => 'INV-' . $invoice->invoice_id,
            'type' => $invoice->inv_type,
            'date' => $invoice->inv_date,
            'nepali_date' => $invoice->inv_date ? NepaliDate::adToBsString($invoice->inv_date, 'en') : null,
            'deleted_at' => $invoice->created_at ? \Carbon\Carbon::parse($invoice->created_at)->format('Y-m-d H:i') : null,
            'subtotal' => $invoice->subtotal,
            'discount' => $invoice->discount,
            'total' => $invoice->total,
            'notes' => $invoice->notes,
            'added_by' => $invoice->added_by,
            'customer' => [
                'id' => $invoice->customerid,
                'name' => $customer ? $customer->name : 'N/A',
                'address' => $customer ? $customer->address : 'N/A',
                'pan_no' => $customer ? $customer->pan_no : null,
                'phoneno' => $customer ? $customer->phoneno : null,
                'email' => $customer ? $customer->email : null,
            ],
            'items' => $items,
        ]);
    }

    public function getPaymentData(Request $req)
    {
        $paymentId = $req->query('paymentid');

        $payment = customerledgerdetails::with('customer')
            ->where('id', $paymentId)
            ->where('invoicetype', 'payment')
            ->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        // Determine payment mode
        $mode = trim($payment->voucher_type ?? '');
        if (empty($mode)) {
            $mode = trim($payment->particulars ?? '');
        }
        if (empty($mode)) {
            $mode = 'Cash';
        }

        return response()->json([
            'payment_id' => $payment->id,
            'receipt_no' => 'RCP-' . $payment->id,
            'amount' => $payment->credit,
            'date' => $payment->date,
            'nepali_date' => NepaliDate::adToBsString($payment->date, 'en'),
            'mode' => $mode,
            'bank_deposit' => $payment->bank_deposit,
            'counter_deposit' => $payment->counter_deposit,
            'particulars' => $payment->particulars,
            'voucher_type' => $payment->voucher_type,
            'customer' => [
                'id' => $payment->customerid,
                'name' => $payment->customer ? $payment->customer->name : 'N/A',
                'address' => $payment->customer ? $payment->customer->address : 'N/A',
                'phoneno' => $payment->customer ? $payment->customer->phoneno : null,
                'email' => $payment->customer ? $payment->customer->email : null,
            ],
        ]);
    }

    public function printAllTodayInvoices(Request $req)
    {
        $today = now()->toDateString();
        $nepaliToday = NepaliDate::adToBsString($today, 'en');

        // Fetch all invoices for today with customer and items
        $invoices = invoice::with(['customer', 'salesitems.item'])
            ->whereDate('inv_date', $today)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function($inv) {
                $isPaid = ($inv->inv_type === 'cash') || customerledgerdetails::where('invoiceid', $inv->id)->where('credit', '>', 0)->exists();
                return [
                    'id' => $inv->id,
                    'invoice_no' => 'INV-' . $inv->id,
                    'type' => $inv->inv_type,
                    'date' => $inv->inv_date,
                    'nepali_date' => NepaliDate::adToBsString($inv->inv_date, 'en'),
                    'subtotal' => $inv->subtotal,
                    'discount' => $inv->discount,
                    'total' => $inv->total,
                    'notes' => $inv->notes,
                    'added_by' => $inv->added_by,
                    'status' => $isPaid ? 'paid' : 'pending',
                    'customer' => [
                        'id' => $inv->customerid,
                        'name' => $inv->customer ? $inv->customer->name : 'N/A',
                        'address' => $inv->customer ? $inv->customer->address : 'N/A',
                        'pan_no' => $inv->customer ? $inv->customer->pan_no : null,
                        'phoneno' => $inv->customer ? $inv->customer->phoneno : null,
                        'email' => $inv->customer ? $inv->customer->email : null,
                    ],
                    'items' => $inv->salesitems->map(function($si) {
                        return [
                            'item_id' => $si->itemid,
                            'item_name' => $si->item ? $si->item->itemsname : $si->unstockedname,
                            'quantity' => $si->quantity,
                            'unit' => $si->unit,
                            'price' => $si->price,
                            'subtotal' => $si->subtotal,
                        ];
                    }),
                ];
            });

        // Calculate totals
        $totalInvoices = $invoices->count();
        $totalAmount = $invoices->sum('total');
        $totalPaid = $invoices->where('status', 'paid')->sum('total');
        $totalPending = $invoices->where('status', 'pending')->sum('total');

        $pdf = FacadePdf::setOptions([
            'dpi' => 150,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
            'chroot' => public_path(),
            'enable_font_subsetting' => false,
        ])
        ->loadView('dashboard.print_all_invoices_today', [
            'invoices' => $invoices,
            'today' => $today,
            'nepaliToday' => $nepaliToday,
            'totalInvoices' => $totalInvoices,
            'totalAmount' => $totalAmount,
            'totalPaid' => $totalPaid,
            'totalPending' => $totalPending,
        ])
        ->setPaper('A5', 'portrait');

        return $pdf->stream('all_invoices_' . $today . '.pdf');
    }

    public function printAllTodayPayments(Request $req)
    {
        $today = now()->toDateString();
        $nepaliToday = NepaliDate::adToBsString($today, 'en');

        // Fetch all payments for today with customer
        $payments = customerledgerdetails::with('customer')
            ->whereDate('date', $today)
            ->where('invoicetype', 'payment')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function($pay) {
                // Determine payment mode
                $mode = trim($pay->voucher_type ?? '');
                if (empty($mode)) {
                    $mode = trim($pay->particulars ?? '');
                }
                if (empty($mode)) {
                    $mode = 'Cash';
                }

                return [
                    'id' => $pay->id,
                    'receipt_no' => 'RCP-' . $pay->id,
                    'amount' => $pay->credit,
                    'date' => $pay->date,
                    'nepali_date' => NepaliDate::adToBsString($pay->date, 'en'),
                    'mode' => $mode,
                    'bank_deposit' => $pay->bank_deposit,
                    'counter_deposit' => $pay->counter_deposit,
                    'particulars' => $pay->particulars,
                    'customer' => [
                        'id' => $pay->customerid,
                        'name' => $pay->customer ? $pay->customer->name : 'N/A',
                        'address' => $pay->customer ? $pay->customer->address : 'N/A',
                        'phoneno' => $pay->customer ? $pay->customer->phoneno : null,
                        'email' => $pay->customer ? $pay->customer->email : null,
                    ],
                ];
            });

        // Calculate totals
        $totalPayments = $payments->count();
        $totalAmount = $payments->sum('amount');

        $pdf = FacadePdf::setOptions([
            'dpi' => 150,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
            'chroot' => public_path(),
            'enable_font_subsetting' => false,
        ])
        ->loadView('dashboard.print_all_payments_today', [
            'payments' => $payments,
            'today' => $today,
            'nepaliToday' => $nepaliToday,
            'totalPayments' => $totalPayments,
            'totalAmount' => $totalAmount,
        ])
        ->setPaper('A5', 'portrait');

        return $pdf->stream('all_payments_' . $today . '.pdf');
    }

    public function checkToday()
    {
        $breadcrumb = [
            'subtitle' => 'Check Today',
            'title' => 'Today\'s Transactions',
            'link' => 'Check Today'
        ];

        $today = now()->toDateString();

        // Get today's invoices
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

        // Get today's payments
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

        return view('dashboard.checktoday', compact('breadcrumb', 'recentInvoices', 'recentPayments'));
    }
}
