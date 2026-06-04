<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\SmsService;
use App\Models\SmsLog;
use App\Models\invoice;
use App\Models\customerinfo;
use App\Helpers\InvoiceSmsHelper;
use Illuminate\Support\Facades\Log;

echo "=== Test SmsLog Creation (Simulating Controller) ===\n\n";

// Get invoice 7775
$invoice = invoice::find(7775);
if (!$invoice) {
    echo "Invoice 7834 not found!\n";
    exit;
}

$customer = customerinfo::find($invoice->customerid);
echo "Invoice: {$invoice->id}\n";
echo "Customer: {$customer->name}\n";
echo "Phone: {$customer->phoneno}\n\n";

$phone = preg_replace('/\D+/', '', ($customer->phoneno ? $customer->phoneno : ''));
if (strlen($phone) === 10) {
    $phone = '977' . $phone;
}

$totalDueAmount = invoice::where('customerid', $invoice->customerid)
    ->where('inv_type', 'credit')
    ->where('created_at', '<=', now())
    ->sum('total');

$invoiceMessage = 'Namaste ' . ($customer->name ? $customer->name : 'Customer')
    . ', your invoice no ' . $invoice->id
    . ' has been created. Invoice Amount: Rs ' . number_format((float) $invoice->total, 2)
    . '. Your total due till today: Rs ' . number_format($totalDueAmount, 2)
    . '. Thank you!';

$invoiceMessage = InvoiceSmsHelper::truncateMessage($invoiceMessage);

echo "Message: " . substr($invoiceMessage, 0, 80) . "...\n\n";

// Test the try-catch block exactly as in controller
if ($phone && $customer) {
    try {
        echo "Creating SmsService...\n";
        $smsService = new SmsService();
        
        echo "Calling send()...\n";
        $smsResponse = $smsService->send($phone, $invoiceMessage);
        echo "Send response: " . json_encode($smsResponse) . "\n\n";
        
        echo "Creating SmsLog record...\n";
        $smsLog = SmsLog::create([
            'invoice_id' => $invoice->id,
            'customer_id' => $invoice->customerid,
            'phone_number' => $phone,
            'message' => $invoiceMessage,
            'sms_type' => 'invoice_created',
            'status' => $smsResponse['success'] ? 'sent' : 'failed',
            'api_response' => json_encode($smsResponse),
        ]);
        
        echo "✓ SmsLog created with ID: {$smsLog->id}\n\n";
        
        if ($smsResponse['success']) {
            echo "Marking as sent...\n";
            $smsLog->markAsSent(json_encode(isset($smsResponse['data']) ? $smsResponse['data'] : 'null'));
            
            echo "Logging info...\n";
            Log::channel('sms')->info('Invoice SMS auto-sent', [
                'invoice_id' => $invoice->id,
                'customer' => $customer->name,
                'phone' => $phone
            ]);
            
            echo "✓ SMS marked as sent\n";
        }
        
    } catch (\Exception $e) {
        echo "✗ EXCEPTION CAUGHT:\n";
        echo "  Message: " . $e->getMessage() . "\n";
        echo "  File: " . $e->getFile() . "\n";
        echo "  Line: " . $e->getLine() . "\n";
        echo "  Trace:\n";
        foreach (explode("\n", $e->getTraceAsString()) as $line) {
            if (trim($line)) echo "    $line\n";
        }
        
        Log::channel('sms')->error('Failed to auto-send invoice SMS', [
            'invoice_id' => $invoice->id,
            'customer' => $customer->name,
            'error' => $e->getMessage()
        ]);
    }
}

echo "\n";

// Verify SMS log was created
$recentLogs = SmsLog::where('invoice_id', $invoice->id)->get();
echo "SMS Logs for Invoice {$invoice->id}: " . count($recentLogs) . "\n";
foreach ($recentLogs as $log) {
    echo "  - ID: {$log->id}, Status: {$log->status}, Created: {$log->created_at}\n";
}

echo "\n";
