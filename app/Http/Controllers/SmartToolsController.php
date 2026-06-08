<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\BackupInvoice;
use App\Models\Bank;
use App\Models\company;
use App\Models\CreditnotesInvoice;
use App\Models\customerinfo;
use App\Models\customerledgerdetails;
use App\Models\Expense;
use App\Models\invoice;
use App\Models\item;
use App\Support\NepaliDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SmartToolsController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->query('q', ''));

        return view('smarttools.index', [
            'breadcrumb' => [
                'subtitle' => 'Smart Tools',
                'title' => 'Smart Tools',
                'link' => 'Smart Tools',
            ],
            'query' => $query,
            'searchResults' => $this->globalSearch($query),
            'dailySummary' => $this->dailySummary(),
            'stockPredictions' => $this->stockPredictions(),
            'auditLogs' => Schema::hasTable('audit_logs') ? AuditLog::latest()->limit(80)->get() : collect(),
        ]);
    }

    private function globalSearch(string $query): array
    {
        if (strlen($query) < 2) {
            return [];
        }

        $like = '%' . $query . '%';
        $isNumber = is_numeric($query);

        $customers = customerinfo::where('name', 'like', $like)
            ->orWhere('phoneno', 'like', $like)
            ->orWhere('address', 'like', $like)
            ->limit(8)
            ->get()
            ->map(fn ($row) => [
                'type' => 'Customer',
                'title' => $row->name,
                'detail' => trim(($row->phoneno ?? '') . ' ' . ($row->address ?? '')),
                'url' => route('customerinfos.edit', $row->id),
            ]);

        $items = item::where('itemsname', 'like', $like)
            ->orWhere('billno', 'like', $like)
            ->limit(8)
            ->get()
            ->map(fn ($row) => [
                'type' => 'Item',
                'title' => $row->itemsname,
                'detail' => 'Stock: ' . number_format((float) $row->quantity, 2) . ' ' . ($row->unit ?? '') . ' | MRP: Rs ' . number_format((float) $row->mrp, 2),
                'url' => route('items.edit', $row->id),
            ]);

        $companies = company::where('name', 'like', $like)
            ->orWhere('phoneno', 'like', $like)
            ->orWhere('address', 'like', $like)
            ->limit(6)
            ->get()
            ->map(fn ($row) => [
                'type' => 'Company',
                'title' => $row->name,
                'detail' => trim(($row->phoneno ?? '') . ' ' . ($row->address ?? '')),
                'url' => route('companys.edit', $row->id),
            ]);

        $invoices = invoice::leftJoin('customerinfos', 'invoices.customerid', '=', 'customerinfos.id')
            ->select('invoices.*', 'customerinfos.name as customer_name')
            ->where(function ($builder) use ($like, $isNumber, $query) {
                $builder->where('customerinfos.name', 'like', $like)
                    ->orWhere('invoices.inv_type', 'like', $like);

                if ($isNumber) {
                    $builder->orWhere('invoices.id', (int) $query);
                }
            })
            ->orderByDesc('invoices.id')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'type' => 'Invoice',
                'title' => 'INV-' . $row->id . ' | ' . ($row->customer_name ?? 'Unknown'),
                'detail' => ucfirst($row->inv_type ?? '-') . ' | Rs ' . number_format((float) $row->total, 2) . ' | ' . $this->dateLabel($row->inv_date),
                'url' => route('customer.billno', ['invoiceid' => $row->id]),
            ]);

        return $customers->concat($items)->concat($companies)->concat($invoices)->values()->all();
    }

    private function dailySummary(): array
    {
        $today = now()->toDateString();

        $invoiceStats = invoice::whereDate('inv_date', $today)
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total), 0) as total')
            ->first();

        $cashSales = invoice::whereDate('inv_date', $today)->where('inv_type', 'cash')->sum('total');
        $creditSales = invoice::whereDate('inv_date', $today)->where('inv_type', 'credit')->sum('total');
        $payments = customerledgerdetails::whereDate('date', $today)->where('invoicetype', 'payment')->sum('credit');
        $expenses = Expense::whereDate('date', $today)->sum('amount');
        $creditNotes = CreditnotesInvoice::whereDate('inv_date', $today)->sum('total');
        $bankDeposits = Bank::whereDate('date', $today)->sum('amount');
        $deletedInvoices = BackupInvoice::whereDate('created_at', $today)->count();

        return [
            'date' => $this->dateLabel($today),
            'invoice_count' => (int) ($invoiceStats->count ?? 0),
            'sales_total' => (float) ($invoiceStats->total ?? 0),
            'cash_sales' => (float) $cashSales,
            'credit_sales' => (float) $creditSales,
            'payments' => (float) $payments,
            'expenses' => (float) $expenses,
            'credit_notes' => (float) $creditNotes,
            'bank_deposits' => (float) $bankDeposits,
            'deleted_invoices' => (int) $deletedInvoices,
            'net_cash_hint' => (float) $cashSales + (float) $payments - (float) $expenses,
        ];
    }

    private function stockPredictions()
    {
        $since = now()->subDays(30)->toDateString();

        return item::query()
            ->leftJoin('salesitems', function ($join) use ($since) {
                $join->on('items.id', '=', 'salesitems.itemid')
                    ->where('salesitems.date', '>=', $since);
            })
            ->where('items.check_remove_ofs', 0)
            ->select('items.id', 'items.itemsname', 'items.quantity', 'items.unit', 'items.showwarning')
            ->selectRaw('COALESCE(SUM(salesitems.quantity), 0) as sold_30_days')
            ->groupBy('items.id', 'items.itemsname', 'items.quantity', 'items.unit', 'items.showwarning')
            ->orderByDesc(DB::raw('COALESCE(SUM(salesitems.quantity), 0)'))
            ->limit(40)
            ->get()
            ->map(function ($row) {
                $dailyAverage = ((float) $row->sold_30_days) / 30;
                $daysLeft = $dailyAverage > 0 ? ((float) $row->quantity) / $dailyAverage : null;

                return [
                    'item_id' => $row->id,
                    'item' => $row->itemsname,
                    'stock' => (float) $row->quantity,
                    'unit' => $row->unit,
                    'sold_30_days' => (float) $row->sold_30_days,
                    'daily_average' => $dailyAverage,
                    'days_left' => $daysLeft,
                    'status' => $daysLeft === null ? 'No recent sales' : ($daysLeft <= 7 ? 'Buy soon' : ($daysLeft <= 15 ? 'Watch' : 'OK')),
                    'reorder_qty' => $dailyAverage > 0 ? max(0, ceil(($dailyAverage * 15) - (float) $row->quantity)) : 0,
                ];
            })
            ->filter(fn ($row) => $row['sold_30_days'] > 0 || $row['stock'] <= 0)
            ->values();
    }

    private function dateLabel($date): string
    {
        if (!$date) {
            return '-';
        }

        return $date . ' / ' . NepaliDate::adToBsString($date, 'en');
    }
}
