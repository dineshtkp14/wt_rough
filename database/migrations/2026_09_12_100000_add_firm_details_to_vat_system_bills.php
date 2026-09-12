<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vat_system_bills', function (Blueprint $table) {
            $table->foreignId('firm_id')->nullable()->after('customer_id')->constrained('vat_firms')->nullOnDelete();
            $table->string('seller_pan_no', 50)->nullable()->after('seller_vat_no');
            $table->string('seller_address', 255)->nullable()->after('seller_phone');
        });
    }

    public function down(): void
    {
        Schema::table('vat_system_bills', function (Blueprint $table) {
            $table->dropForeign(['firm_id']);
            $table->dropColumn(['firm_id', 'seller_pan_no', 'seller_address']);
        });
    }
};
