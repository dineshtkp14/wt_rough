<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== SMS System Diagnostic ===\n\n";

// Test 1: Database Connection
echo "TEST 1: Database Connection\n";
echo "============================\n";
try {
    DB::connection()->getPdo();
    echo "✓ Database connected successfully\n\n";
} catch (\Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n\n";
    die("Cannot continue without database connection.\n");
}

// Test 2: Check if sms_logs table exists
echo "TEST 2: SMS Logs Table\n";
echo "======================\n";
try {
    $tables = DB::select("SHOW TABLES LIKE 'sms_logs'");
    if (count($tables) > 0) {
        echo "✓ sms_logs table exists\n";
        
        // Get table structure
        $columns = DB::select("DESCRIBE sms_logs");
        echo "  Columns: " . count($columns) . "\n";
        echo "  ";
        foreach ($columns as $col) {
            echo $col->Field . " ";
        }
        echo "\n\n";
    } else {
        echo "✗ sms_logs table DOES NOT EXIST\n";
        echo "  You need to run: php artisan migrate\n\n";
    }
} catch (\Exception $e) {
    echo "✗ Error checking table: " . $e->getMessage() . "\n\n";
}

// Test 3: Check SmsService
echo "TEST 3: SmsService Configuration\n";
echo "=================================\n";
try {
    $smsService = new \App\Services\SmsService();
    echo "✓ SmsService instantiated successfully\n\n";
} catch (\Exception $e) {
    echo "✗ SmsService error: " . $e->getMessage() . "\n\n";
}

// Test 4: Check SMS Config
echo "TEST 4: SMS Configuration\n";
echo "==========================\n";
$smsConfig = config('services.sms');
if ($smsConfig) {
    echo "✓ SMS config found\n";
    echo "  Username: " . ($smsConfig['username'] ? '✓ Set' : '✗ Missing') . "\n";
    echo "  API Key: " . ($smsConfig['api_key'] ? '✓ Set (length: ' . strlen($smsConfig['api_key']) . ')' : '✗ Missing') . "\n";
    echo "  Campaign: " . ($smsConfig['campaign'] ? '✓ ' . $smsConfig['campaign'] : '✗ Missing') . "\n";
    echo "  Route ID: " . ($smsConfig['route_id'] ? '✓ ' . $smsConfig['route_id'] : '✗ Missing') . "\n\n";
} else {
    echo "✗ SMS config not found\n\n";
}

// Test 5: Check Environment Variables
echo "TEST 5: Environment Variables\n";
echo "===============================\n";
echo "SMS_USERNAME: " . (env('SMS_USERNAME') ? '✓ ' . env('SMS_USERNAME') : '✗ Missing') . "\n";
echo "SMS_API_KEY: " . (env('SMS_API_KEY') ? '✓ Set (length: ' . strlen(env('SMS_API_KEY')) . ')' : '✗ Missing') . "\n";
echo "SMS_CAMPAIGN: " . (env('SMS_CAMPAIGN') ? '✓ ' . env('SMS_CAMPAIGN') : '✗ Missing') . "\n";
echo "SMS_ROUTE_ID: " . (env('SMS_ROUTE_ID') ? '✓ ' . env('SMS_ROUTE_ID') : '✗ Missing') . "\n\n";

// Test 6: Check Log Channel
echo "TEST 6: SMS Log Channel\n";
echo "=======================\n";
$logChannels = config('logging.channels');
if (isset($logChannels['sms'])) {
    echo "✓ SMS log channel configured\n";
    echo "  Path: " . ($logChannels['sms']['path'] ?? 'N/A') . "\n\n";
} else {
    echo "✗ SMS log channel NOT configured\n\n";
}

// Test 7: API Test
echo "TEST 7: API Connectivity\n";
echo "=========================\n";
try {
    $smsService = new \App\Services\SmsService();
    $result = $smsService->send('9779841234567', 'Test from diagnostic');
    
    if ($result['success']) {
        echo "✓ API test successful\n";
        echo "  Response: " . json_encode($result['data']) . "\n\n";
    } else {
        echo "✗ API test failed\n";
        echo "  Status: " . $result['status'] . "\n";
        echo "  Error: " . json_encode($result['data'] ?? $result['error']) . "\n\n";
    }
} catch (\Exception $e) {
    echo "✗ API test error: " . $e->getMessage() . "\n\n";
}

// Final Summary
echo "=== SUMMARY ===\n";
echo "If all tests pass (✓), your SMS system is ready!\n";
echo "If any tests fail (✗), you need to:\n";
echo "  1. Run: php artisan migrate\n";
echo "  2. Check .env file for SMS credentials\n";
echo "  3. Run: php artisan config:clear\n";
