<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('customerinfos')
            ->where(function ($query) {
                $query->whereNull('type')
                    ->orWhere('type', '')
                    ->orWhere('type', '-');
            })
            ->update(['type' => 'Customer']);
    }

    public function down(): void
    {
        // The original values were NULL, blank, or '-', so this data cleanup
        // is intentionally not reversed.
    }
};
