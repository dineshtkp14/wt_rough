<?php

namespace App\Services;

use App\Helpers\InvoiceSmsHelper;
use App\Models\CreditnotesInvoice;
use App\Models\customerinfo;
use App\Models\customerledgerdetails;
use App\Models\invoice;
use App\Models\SmsLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerSmsNotifier
{
    public function invoiceDeleted($invoiceData, ?customerinfo $customer = null): ?array
    {
        $customer = $customer ?: customerinfo::find($invoiceData->customerid ?? null);
        $invoiceId = $invoiceData->id ?? $invoiceData->invoice_id ?? null;
        $amount = (float) ($invoiceData->total ?? 0);

        $message = 'Namaste ' . ($customer->name ?? 'Customer')
            . ', your invoice no ' . $invoiceId
            . ' of Rs ' . number_format($amount, 2)
            . ' has been deleted. Thank you!';

        return $this->sendToCustomer($customer, $message, 'invoice_deleted');
    }

    public function paymentCreated(customerledgerdetails $payment, ?customerinfo $customer = null): ?array
    {
        $customer = $customer ?: customerinfo::find($payment->customerid);
        $remainingDue = $this->customerTotalDue((int) $payment->customerid);

        $message = 'Namaste ' . ($customer->name ?? 'Customer')
            . ', payment received Rs ' . number_format((float) $payment->credit, 2)
            . '. Receipt no ' . $payment->id
            . '. Remaining due Rs ' . number_format($remainingDue, 2)
            . '. Thank you!';

        return $this->sendToCustomer($customer, $message, 'payment_received');
    }

    public function salesReturnCreated(CreditnotesInvoice $creditNote, ?customerinfo $customer = null): ?array
    {
        $customer = $customer ?: customerinfo::find($creditNote->customerid);
        $remainingDue = max(0, $this->customerTotalDue((int) $creditNote->customerid));

        $message = 'Namaste ' . ($customer->name ?? 'Customer')
            . ', sales return/credit note no ' . $creditNote->id
            . ' of Rs ' . number_format((float) $creditNote->total, 2)
            . ' has been created. Remaining due Rs ' . number_format($remainingDue, 2)
            . '. Thank you!';

        return $this->sendToCustomer($customer, $message, 'sales_return_created');
    }

    public function invoiceCreated(invoice $invoice, ?customerinfo $customer = null, ?string $message = null): ?array
    {
        if ($invoice->inv_type !== 'credit') {
            return null;
        }

        $customer = $customer ?: customerinfo::find($invoice->customerid);
        $message = $message ?: 'Namaste ' . ($customer->name ?? 'Customer')
            . ', your invoice no ' . $invoice->id
            . ' has been created. Invoice Amount: Rs ' . number_format((float) $invoice->total, 2)
            . '. Your total due till today: Rs ' . number_format(max(0, $this->customerTotalDue((int) $invoice->customerid)), 2)
            . '. Thank you!';

        return $this->sendToCustomer($customer, $message, 'invoice_created', $invoice->id);
    }

    public function customerTotalDue(int $customerId): float
    {
        $debitNotCash = (float) DB::table('customerledgerdetails')
            ->where('customerid', $customerId)
            ->where('invoicetype', '!=', 'cash')
            ->sum(DB::raw('COALESCE(debit, 0)'));

        $ledgerCredit = (float) DB::table('customerledgerdetails')
            ->where('customerid', $customerId)
            ->sum(DB::raw('COALESCE(credit, 0)'));

        $creditNoteCredit = (float) DB::table('creditnotes_customerledgerdetails')
            ->where('customerid', $customerId)
            ->sum(DB::raw('COALESCE(debit, credit, 0)'));

        return $debitNotCash - $ledgerCredit - $creditNoteCredit;
    }

    private function sendToCustomer(?customerinfo $customer, string $message, string $smsType, ?int $invoiceId = null): ?array
    {
        if (!$customer || !$customer->phoneno) {
            return null;
        }

        try {
            $phone = SmsService::formatPhoneNumber($customer->phoneno);
            $message = InvoiceSmsHelper::truncateMessage($message);
            $response = (new SmsService())->send($phone, $message);

            SmsLog::create([
                'invoice_id' => $invoiceId,
                'customer_id' => $customer->id,
                'phone_number' => $phone,
                'message' => $message,
                'sms_type' => $smsType,
                'status' => $response['success'] ? 'sent' : 'failed',
                'api_response' => json_encode($response),
                'sent_at' => $response['success'] ? now() : null,
            ]);

            Log::channel('sms')->info('Customer SMS notification processed', [
                'sms_type' => $smsType,
                'customer_id' => $customer->id,
                'phone' => $phone,
                'success' => $response['success'],
                'status' => $response['status'] ?? null,
            ]);

            $response['message'] = $message;
            $response['phone'] = $phone;

            return $response;
        } catch (\Throwable $e) {
            Log::channel('sms')->error('Customer SMS notification failed', [
                'sms_type' => $smsType,
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status' => null,
                'error' => $e->getMessage(),
                'message' => $message,
            ];
        }
    }
}
