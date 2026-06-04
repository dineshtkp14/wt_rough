#!/bin/bash
# Run these commands on your LIVE HOST to fix SMS system

echo "=== Fixing SMS on Live Host ==="
echo ""

# Step 1: Clear config cache
echo "Step 1: Clearing config cache..."
php artisan config:clear
php artisan config:cache

# Step 2: Clear application cache
echo "Step 2: Clearing application cache..."
php artisan cache:clear

# Step 3: Restart queues if using them
echo "Step 3: Optimizing..."
php artisan optimize

# Step 4: Test SMS
echo ""
echo "Step 4: Testing SMS..."
php artisan tinker << 'EOF'
use App\Services\SmsService;
$sms = new SmsService();
$result = $sms->send('9779860378262', 'Test SMS from live host');
dd($result);
EOF

echo ""
echo "Done!"
