<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vat_system_bills', function (Blueprint $table) {
            $table->dropUnique('vat_system_bills_bill_no_unique');
            $table->unique(['firm_id', 'bill_no'], 'vat_system_bills_firm_bill_no_unique');
        });
    }

    public function down(): void
    {
        Schema::table('vat_system_bills', function (Blueprint $table) {
            $table->dropUnique('vat_system_bills_firm_bill_no_unique');
            $table->unique('bill_no', 'vat_system_bills_bill_no_unique');
        });
    }
};
