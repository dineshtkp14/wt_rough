<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\invoice;
use App\Models\customerinfo;

echo "=== Invoice Check ===\n\n";

$invoices = invoice::orderBy('id', 'DESC')->limit(10)->get();
echo "Total invoices in database: " . invoice::count() . "\n";
echo "Recent invoices:\n";

foreach ($invoices as $inv) {
    $customer = customerinfo::find($inv->customerid);
    $custName = $customer ? $customer->name : 'Unknown';
    echo "  ID: {$inv->id}, Customer: {$custName}, Total: Rs {$inv->total}, Date: {$inv->created_at}\n";
}

echo "\n";
