<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\customerledgerdetails;
use App\Models\customerinfo;
use Illuminate\Support\Facades\DB;

$customerId = 1;
$totalDueAmount = customerledgerdetails::where('customerid', $customerId)
    ->sum(DB::raw('COALESCE(debit, 0) - COALESCE(credit, 0)'));

$customer = customerinfo::find($customerId);
echo "Customer: {$customer->name}\n";
echo "Total Due (from ledger): Rs " . number_format($totalDueAmount, 2) . "\n";
