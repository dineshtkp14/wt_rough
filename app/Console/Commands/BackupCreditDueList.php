<?php

namespace App\Console\Commands;

use App\Models\customerinfo;
use App\Models\customerledgerdetails;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class BackupCreditDueList extends Command
{
    protected $signature = 'backup:credit-due-list {--mail : Email the backup files after saving them} {--to= : Email address to receive the backup}';

    protected $description = 'Save the all customer credit due list backup as CSV and PDF.';

    public function handle(): int
    {
        $rows = $this->creditRows();
        $backupDir = 'backups/credit-list';
        Storage::makeDirectory($backupDir);

        $timestamp = now()->format('Y-m-d_H-i-s');
        $csvPath = "{$backupDir}/credit_due_list_{$timestamp}.csv";
        $pdfPath = "{$backupDir}/credit_due_list_{$timestamp}.pdf";

        Storage::put($csvPath, $this->toCsv($rows));
        Storage::put($pdfPath, $this->toPdf($rows));

        $csvFullPath = storage_path('app/' . $csvPath);
        $pdfFullPath = storage_path('app/' . $pdfPath);

        $this->info('Credit due list backup saved.');
        $this->line('CSV: ' . $csvFullPath);
        $this->line('PDF: ' . $pdfFullPath);

        if ($this->option('mail')) {
            $this->sendMail($csvFullPath, $pdfFullPath, $rows->count());
        }

        return self::SUCCESS;
    }

    private function sendMail(string $csvFullPath, string $pdfFullPath, int $rowCount): void
    {
        $to = $this->option('to') ?: env('CREDIT_DUE_BACKUP_EMAIL');

        if (empty($to)) {
            $this->error('Email not sent. Set CREDIT_DUE_BACKUP_EMAIL in .env or pass --to=email@example.com.');
            return;
        }

        Mail::raw(
            "Daily credit due list backup is attached.\n\nRows: {$rowCount}\nGenerated: " . now()->format('Y-m-d H:i:s'),
            function ($message) use ($to, $csvFullPath, $pdfFullPath) {
                $message->to($to)
                    ->subject('Daily Credit Due List Backup - ' . now()->format('Y-m-d'))
                    ->attach($pdfFullPath)
                    ->attach($csvFullPath);
            }
        );

        $this->info('Credit due list backup emailed to: ' . $to);
    }

    private function creditRows()
    {
        $rows = customerledgerdetails::select(
            'customerid',
            DB::raw('MAX(date) as latest_date'),
            DB::raw('SUM(debit) AS total_debit'),
            DB::raw('COALESCE(SUM(credit), 0) AS total_credit'),
            DB::raw('COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) AS debit_credit_difference')
        )
            ->where(function ($query) {
                $query->where('invoicetype', 'credit')
                    ->orWhere('invoicetype', 'payment');
            })
            ->groupBy('customerid')
            ->orderByDesc('debit_credit_difference')
            ->get()
            ->filter(function ($row) {
                return (float) $row->debit_credit_difference !== 0.0;
            })
            ->values();

        $customerIds = $rows->pluck('customerid')->filter()->unique();
        $customers = customerinfo::whereIn('id', $customerIds)
            ->get(['id', 'name', 'phoneno', 'address'])
            ->keyBy('id');

        return $rows->map(function ($row) use ($customers) {
            $customer = $customers->get($row->customerid);
            $row->cname = $customer->name ?? '';
            $row->cphoneno = $customer->phoneno ?? '';
            $row->address = $customer->address ?? '';

            return $row;
        });
    }

    private function toCsv($rows): string
    {
        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, [
            'SN',
            'Customer ID',
            'Customer Name',
            'Phone',
            'Address',
            'Total Debit',
            'Total Credit',
            'Due Amount',
            'Latest Date',
        ]);

        foreach ($rows as $index => $row) {
            fputcsv($handle, [
                $index + 1,
                $row->customerid,
                $row->cname,
                $row->cphoneno,
                $row->address,
                $row->total_debit,
                $row->total_credit,
                $row->debit_credit_difference,
                $row->latest_date,
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }

    private function toPdf($rows): string
    {
        $paginator = new LengthAwarePaginator(
            $rows,
            $rows->count(),
            max(1, $rows->count()),
            1,
            ['path' => url('/accl')]
        );

        $totalDebitCreditDifference = $rows
            ->filter(function ($row) {
                return $row->debit_credit_difference >= 0;
            })
            ->sum('debit_credit_difference');

        $totalDebitCreditDifferencewhole = $totalDebitCreditDifference;

        $totalNegativeDebitCreditDifference = $rows
            ->filter(function ($row) {
                return $row->debit_credit_difference < 0;
            })
            ->sum('debit_credit_difference');

        $html = view('allsalesdetails.allcustomercreditlistpdf', [
            'all' => $paginator,
            'totalDebitCreditDifference' => $totalDebitCreditDifference,
            'totalDebitCreditDifferencewhole' => $totalDebitCreditDifferencewhole,
            'totalNegativeDebitCreditDifference' => $totalNegativeDebitCreditDifference,
        ])->render();

        return Pdf::setOptions(['dpi' => 150, 'defaultFont' => 'dejavu serif'])
            ->loadHtml($html)
            ->output();
    }
}
