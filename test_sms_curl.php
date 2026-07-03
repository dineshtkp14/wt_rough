<?php

echo "=== Testing SpellSMS API with cURL ===\n\n";

$username = 'om_hari';
$apiKey = 'DE932FD6F0E9C395DCED38SXV07IEDCAF4';
$apiUrl = 'https://spellcpaas.com/api/smsapi';

// Test payload
$data = [
    'username' => $username,
    'key' => $apiKey,
    'campaign' => 'Default',
    'routeid' => 'SI_Alert',
    'type' => 'text',
    'contacts' => '9779841234567', // Test number (Nepal)
    'msg' => 'Test SMS from WT Shop',
    'responsetype' => 'json'
];

echo "Testing API Request:\n";
echo "Username: $username\n";
echo "API Key: " . substr($apiKey, 0, 8) . "...\n";
echo "API URL: $apiUrl\n";
echo "Test Phone: 9779841234567\n\n";

// Create cURL request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For testing only
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // For testing only

echo "Sending API request...\n\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

curl_close($ch);

// Display results
echo "HTTP Status: $httpCode\n";
echo "Response:\n";
echo "---\n";
echo $response;
echo "\n---\n\n";

if ($curlError) {
    echo "cURL Error: $curlError\n";
} else if ($httpCode == 200) {
    echo "✓ Request sent successfully!\n";
    
    $jsonResponse = json_decode($response, true);
    if ($jsonResponse) {
        echo "\nJSON Response:\n";
        print_r($jsonResponse);
    }
} else {
    echo "✗ Request failed with status code: $httpCode\n";
}

echo "\n\n=== Troubleshooting Steps ===\n";
echo "1. Login to https://spellcpaas.com/login\n";
echo "   - Username: om_hari\n";
echo "   - Password: Nepal12345#\n\n";
echo "2. Check:\n";
echo "   - SMS Balance (should be > 0)\n";
echo "   - API Key in Dashboard → Developer API\n";
echo "   - Verify the API key matches: DE932FD6F0E9C395DCED38SXV07IEDCAF4\n";
echo "   - Check if SMS sending is enabled\n\n";
echo "3. Common Issues:\n";
echo "   - Invalid API Key\n";
echo "   - Zero SMS Balance\n";
echo "   - API not enabled\n";
echo "   - Invalid phone number format\n";
echo "   - Network/Firewall issues\n";
