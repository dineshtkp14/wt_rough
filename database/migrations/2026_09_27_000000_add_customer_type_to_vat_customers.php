<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vat_customers', function (Blueprint $table) {
            // Existing rows remain valid and are classified as Customer by default.
            $table->string('customer_type', 20)->default('customer')->after('name')->index();
        });
    }

    public function down(): void
    {
        Schema::table('vat_customers', function (Blueprint $table) {
            $table->dropColumn('customer_type');
        });
    }
};
