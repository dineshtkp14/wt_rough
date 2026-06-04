<?php

echo "=== SpellSMS Account Verification ===\n\n";

$username = 'om_hari';
$apiKey = 'DE932FD6F0E9C395DCEDEDC1158BCAF4';
$password = 'om_hari_2026';
$apiUrl = 'https://spellcpaas.com/api/smsapi';

// Test 1: Method 1 - API Key
echo "TEST 1: Using API Key Method\n";
echo "==============================\n";

$payload1 = [
    'username' => $username,
    'key' => $apiKey,
    'campaign' => 'Default',
    'routeid' => 'SI_Alert',
    'type' => 'text',
    'contacts' => '9779841234567',
    'msg' => 'Test SMS from WT Shop - API Key Method',
    'responsetype' => 'json'
];

echo "Payload: " . json_encode($payload1, JSON_PRETTY_PRINT) . "\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response1 = curl_exec($ch);
$httpCode1 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: $httpCode1\n";
echo "Response: $response1\n\n";

// Test 2: Method 2 - Username + Password
echo "TEST 2: Using Username + Password Method\n";
echo "==========================================\n";

$payload2 = [
    'username' => $username,
    'password' => $password,
    'campaign' => 'Default',
    'routeid' => 'SI_Alert',
    'type' => 'text',
    'contacts' => '9779841234567',
    'msg' => 'Test SMS from WT Shop - Password Method',
    'responsetype' => 'json'
];

echo "Payload: " . json_encode($payload2, JSON_PRETTY_PRINT) . "\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload2);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response2 = curl_exec($ch);
$httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: $httpCode2\n";
echo "Response: $response2\n\n";

// Analysis
echo "=== ANALYSIS ===\n";
echo "Both methods failed? Common reasons:\n";
echo "1. ❌ SMS Balance = 0 (need to add credit)\n";
echo "2. ❌ API not enabled in account settings\n";
echo "3. ❌ API credentials incorrect\n";
echo "4. ❌ Account verification pending\n\n";

echo "NEXT STEPS:\n";
echo "1. Login to https://spellcpaas.com/login\n";
echo "2. Go to Dashboard\n";
echo "3. Check SMS Balance (should be > 0)\n";
echo "4. Go to Settings > API\n";
echo "5. Verify API is ENABLED\n";
echo "6. Add SMS credit if balance is 0\n";
echo "7. Try the test again\n";
