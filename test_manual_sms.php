<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\SmsService;
use App\Models\SmsLog;
use App\Models\customerinfo;

echo "=== Manual SMS Test ===\n\n";

// Test with invoice 7834 and customer contact number
$customerPhone = '9860378262'; // From your screenshot
$invoiceId = 7834;
$customerName = 'DINESH BAJGAIN';

echo "Test Details:\n";
echo "  Customer: $customerName\n";
echo "  Phone: $customerPhone\n";
echo "  Invoice: $invoiceId\n\n";

// Format phone number
$phone = preg_replace('/\D+/', '', $customerPhone);
if (strlen($phone) === 10) {
    $phone = '977' . $phone;
}

echo "Formatted Phone: $phone\n\n";

// Create message
$message = "Namaste $customerName, your invoice no $invoiceId has been created. Invoice Amount: Rs 2,400.00. Your total due till today: Rs 15,600.00. Thank you!";

echo "Message: " . substr($message, 0, 60) . "...\n\n";

// Send SMS
echo "Sending SMS...\n";
try {
    $smsService = new SmsService();
    $result = $smsService->send($phone, $message);
    
    echo "API Response: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
    
    if ($result['success']) {
        echo "✓ SMS SENT SUCCESSFULLY!\n";
        echo "  Response ID: " . ($result['data'] ?? 'N/A') . "\n";
    } else {
        echo "✗ SMS FAILED\n";
        echo "  Status: " . $result['status'] . "\n";
        echo "  Error: " . ($result['error'] ?? json_encode($result['data'])) . "\n";
    }
    
} catch (\Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

echo "\n";
