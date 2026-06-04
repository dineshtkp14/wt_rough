<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== SMS Logs Table Check ===\n\n";

// Check if table exists
if (Schema::hasTable('sms_logs')) {
    echo "✓ sms_logs table EXISTS\n\n";
    
    // Get table columns
    $columns = DB::connection()->getSchemaBuilder()->getColumnListing('sms_logs');
    echo "Columns:\n";
    foreach ($columns as $col) {
        echo "  - $col\n";
    }
    
    // Check row count
    $count = DB::table('sms_logs')->count();
    echo "\nTotal SMS Logs: $count\n";
    
    // Show recent logs
    if ($count > 0) {
        echo "\nRecent logs:\n";
        $logs = DB::table('sms_logs')->latest()->limit(5)->get();
        foreach ($logs as $log) {
            echo "  ID: {$log->id}, Invoice: {$log->invoice_id}, Status: {$log->status}, Created: {$log->created_at}\n";
        }
    }
    
} else {
    echo "✗ sms_logs table DOES NOT EXIST\n";
    echo "Need to run: php artisan migrate\n";
}

echo "\n";
