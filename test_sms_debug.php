<?php

echo "=== SpellSMS API Debugging ===\n\n";

$username = 'om_hari';
$apiKey = 'DE932FD6F0E9C395DCED38SXV07IEDCAF4';
$password = 'Nepal12345#'; // Try with password too
$apiUrl = 'https://spellcpaas.com/api/smsapi';

echo "1. Trying with API Key (current method):\n";

$payload1 = [
    'username' => $username,
    'key' => $apiKey,
    'campaign' => 'Default',
    'routeid' => 'SI_Alert',
    'type' => 'text',
    'contacts' => '9779841234567',
    'msg' => 'Test SMS',
    'responsetype' => 'json'
];

testAPI($apiUrl, $payload1);

echo "\n2. Trying with password instead of key:\n";

$payload2 = [
    'username' => $username,
    'password' => $password,
    'campaign' => 'Default',
    'routeid' => 'SI_Alert',
    'type' => 'text',
    'contacts' => '9779841234567',
    'msg' => 'Test SMS',
    'responsetype' => 'json'
];

testAPI($apiUrl, $payload2);

echo "\n3. Trying with both username/password (GET format test):\n";

$getUrl = $apiUrl . '?' . http_build_query($payload2);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $getUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: $httpCode\n";
echo "Response: $response\n";

function testAPI($url, $payload) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "   Status: $httpCode\n";
    echo "   Response: $response\n";
}

echo "\n=== IMPORTANT ===\n";
echo "Please verify in your SpellSMS account:\n";
echo "1. Login: https://spellcpaas.com/login\n";
echo "2. Check Dashboard > Developer API\n";
echo "3. Confirm:\n";
echo "   - Your exact API KEY (not password)\n";
echo "   - Is API enabled for your account?\n";
echo "   - Do you have SMS balance? (should be > 0)\n";
echo "4. Check if there's documentation on the correct API format\n";
