#!/bin/bash
# Run these commands on your LIVE HOST to fix SMS system

echo "=== Fixing SMS on Live Host ==="
echo ""

# Step 1: Clear config cache
echo "Step 1: Clearing config cache..."
php artisan config:clear

# Step 2: Verify SMS env values are loaded
echo "Step 2: Checking SMS environment..."
php artisan tinker << 'EOF'
$required = ['SMS_USERNAME', 'SMS_API_KEY', 'SMS_PASSWORD', 'SMS_CAMPAIGN', 'SMS_ROUTE_ID'];
foreach ($required as $key) {
    echo $key . ': ' . (env($key) ? 'SET' : 'MISSING') . PHP_EOL;
}
EOF

# Step 3: Run pending migrations
echo "Step 3: Running migrations..."
php artisan migrate --force

# Step 4: Clear application cache
echo "Step 4: Clearing application cache..."
php artisan cache:clear

# Step 5: Rebuild optimized cache
echo "Step 5: Optimizing..."
php artisan optimize

# Step 6: Test SMS
echo ""
echo "Step 6: Testing SMS..."
php artisan tinker << 'EOF'
use App\Services\SmsService;
$sms = new SmsService();
$result = $sms->send('9779860378262', 'Test SMS from live host');
dd($result);
EOF

echo ""
echo "Done!"
