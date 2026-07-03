<?php

require 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Test SpellSMS API Connection
echo "=== Testing SpellSMS API ===\n\n";

// Configuration
$username = 'om_hari';
$apiKey = 'DE932FD6F0E9C395DCED38SXV07IEDCAF4';
$apiUrl = 'https://spellcpaas.com/api/smsapi';

// Test 1: Check API credentials
echo "1. Testing API Credentials:\n";
echo "   Username: $username\n";
echo "   API Key: " . substr($apiKey, 0, 8) . "...\n";
echo "   API URL: $apiUrl\n\n";

// Test 2: Simple API Test
echo "2. Testing API Endpoint:\n";

$testPayload = [
    'username' => $username,
    'key' => $apiKey,
    'campaign' => 'Default',
    'routeid' => 'SI_Alert',
    'type' => 'text',
    'contacts' => '9779999999999', // Test number
    'msg' => 'Test SMS from WT Shop',
    'responsetype' => 'json'
];

try {
    $response = Http::post($apiUrl, $testPayload);
    
    echo "   Status Code: " . $response->status() . "\n";
    echo "   Response:\n";
    echo json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    echo "\n\n";

    if ($response->successful()) {
        echo "✓ API connection SUCCESS\n";
    } else {
        echo "✗ API connection FAILED\n";
        echo "   Response Body: " . $response->body() . "\n";
    }

} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Test 3: Check API Key Validity
echo "\n3. Checking API Key Format:\n";
if (strlen($apiKey) === 32) {
    echo "   ✓ API Key format looks valid (32 chars)\n";
} else {
    echo "   ✗ API Key format issue (length: " . strlen($apiKey) . ")\n";
}

// Test 4: Verify Account Balance
echo "\n4. Check Your SpellSMS Account:\n";
echo "   - Login: https://spellcpaas.com/login\n";
echo "   - Username: om_hari\n";
echo "   - Check SMS Balance in Dashboard\n";
echo "   - Verify API Key in Developer API section\n";
echo "   - Check if API is enabled\n";

// Test 5: Format Test
echo "\n5. Phone Number Formatting Test:\n";
$testPhones = ['9841234567', '9879841234567', '98412345'];
foreach ($testPhones as $phone) {
    $formatted = preg_replace('/\D+/', '', $phone);
    if (strlen($formatted) === 10) {
        $formatted = '977' . $formatted;
    }
    echo "   Input: $phone → Formatted: $formatted\n";
}

echo "\n=== End Test ===\n";
