<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sms_logs') && Schema::hasColumn('sms_logs', 'api_response')) {
            DB::statement('ALTER TABLE sms_logs MODIFY api_response TEXT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sms_logs') && Schema::hasColumn('sms_logs', 'api_response')) {
            DB::statement('ALTER TABLE sms_logs MODIFY api_response VARCHAR(255) NULL');
        }
    }
};
