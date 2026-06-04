<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\invoice;
use App\Models\SmsLog;

class CheckInvoiceSmsStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:status {invoiceId? : The ID of the invoice}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check SMS status and history for an invoice';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $invoiceId = $this->argument('invoiceId');

        if ($invoiceId) {
            $this->showInvoiceSmsStatus($invoiceId);
        } else {
            $this->showRecentInvoiceSmsStatus();
        }
    }

    /**
     * Show SMS status for a specific invoice
     */
    private function showInvoiceSmsStatus($invoiceId)
    {
        $invoice = invoice::with('customer', 'smsLogs')->find($invoiceId);

        if (!$invoice) {
            $this->error("Invoice #{$invoiceId} not found!");
            return;
        }

        $this->info("=== Invoice #{$invoiceId} SMS Status ===");
        $this->newLine();
        
        $this->info("Customer: " . ($invoice->customer->name ?? 'N/A'));
        $this->info("Phone: " . ($invoice->customer->phoneno ?? 'N/A'));
        $this->info("Total Amount: Rs " . number_format($invoice->total, 2));
        $this->newLine();

        if ($invoice->smsLogs->isEmpty()) {
            $this->warn("No SMS logs found for this invoice");
            return;
        }

        $this->info("SMS History:");
        $this->newLine();

        foreach ($invoice->smsLogs as $log) {
            $statusIcon = $log->status === 'sent' ? '✓' : '✗';
            $this->line("{$statusIcon} [{$log->sms_type}] - {$log->status} at " . ($log->sent_at ?? $log->created_at)->format('Y-m-d H:i:s'));
            $this->line("   To: {$log->phone_number}");
            $this->line("   Msg: " . substr($log->message, 0, 60) . "...");
            $this->newLine();
        }
    }

    /**
     * Show recent SMS sending status
     */
    private function showRecentInvoiceSmsStatus()
    {
        $this->info("=== Recent SMS Status ===");
        $this->newLine();

        $logs = SmsLog::orderBy('created_at', 'DESC')
            ->limit(10)
            ->get();

        if ($logs->isEmpty()) {
            $this->warn("No SMS logs found");
            return;
        }

        $headers = ['Invoice', 'Customer', 'Phone', 'Status', 'Sent At'];
        $rows = [];

        foreach ($logs as $log) {
            $rows[] = [
                $log->invoice_id ?? 'N/A',
                $log->invoice->customer->name ?? 'N/A',
                $log->phone_number,
                $log->status,
                $log->sent_at?->format('Y-m-d H:i:s') ?? 'Pending'
            ];
        }

        $this->table($headers, $rows);
    }
}
