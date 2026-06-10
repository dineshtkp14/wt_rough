<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\invoice;
use App\Models\SmsLog;
use App\Services\SmsService;
use App\Services\CustomerLedgerBalance;
use App\Helpers\InvoiceSmsHelper;
use Illuminate\Support\Facades\Log;

class SendInvoiceSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send-invoice {invoiceId? : The ID of the invoice to send SMS for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send SMS notification for invoice creation with customer details';

    private function customerTotalDueForMessage($customerid)
    {
        return (new CustomerLedgerBalance())->totalDue((int) $customerid);
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $invoiceId = $this->argument('invoiceId');

        if ($invoiceId) {
            $this->sendInvoiceSms($invoiceId);
        } else {
            $this->info('Usage: php artisan sms:send-invoice {invoiceId}');
            $this->info('Example: php artisan sms:send-invoice 123');
        }
    }

    /**
     * Send SMS for a specific invoice
     */
    private function sendInvoiceSms($invoiceId)
    {
        $invoice = invoice::with('customer')->find($invoiceId);

        if (!$invoice) {
            $this->error("Invoice #{$invoiceId} not found!");
            return;
        }

        $customer = $invoice->customer;
        if (!$customer || !$customer->phoneno) {
            $this->error("Invoice #{$invoiceId}: Customer has no phone number!");
            return;
        }

        $phone = SmsService::formatPhoneNumber($customer->phoneno);

        $totalDueAmount = $this->customerTotalDueForMessage($invoice->customerid);

        // Create message
        $message = 'Namaste ' . $customer->name
            . ', your invoice no ' . $invoice->id
            . ' has been created. Invoice Amount: Rs ' . number_format((float) $invoice->total, 2)
            . '. Your total due till today: Rs ' . number_format($totalDueAmount, 2)
            . '. Thank you!';

        $message = InvoiceSmsHelper::truncateMessage($message);

        try {
            $smsService = new SmsService();
            $response = $smsService->send($phone, $message);

            $smsLog = SmsLog::create([
                'invoice_id' => $invoice->id,
                'customer_id' => $invoice->customerid,
                'phone_number' => $phone,
                'message' => $message,
                'sms_type' => 'invoice_created',
                'status' => $response['success'] ? 'sent' : 'failed',
                'api_response' => json_encode($response),
            ]);

            if ($response['success']) {
                $smsLog->markAsSent(json_encode($response['data']));
                $this->info("✓ SMS sent successfully to {$customer->name} ({$phone})");
            } else {
                $this->error("✗ SMS failed to send: " . json_encode($response));
            }

        } catch (\Exception $e) {
            $this->error("✗ Error: " . $e->getMessage());
            Log::channel('sms')->error('SMS send error', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
