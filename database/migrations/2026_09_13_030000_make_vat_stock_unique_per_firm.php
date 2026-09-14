<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vat_stocks', function (Blueprint $table) {
            $table->dropUnique('vat_stocks_item_name_unit_unique');
            $table->unique(['firm_id', 'item_name', 'unit'], 'vat_stocks_firm_item_unit_unique');
        });
    }

    public function down(): void
    {
        Schema::table('vat_stocks', function (Blueprint $table) {
            $table->dropUnique('vat_stocks_firm_item_unit_unique');
            $table->unique(['item_name', 'unit']);
        });
    }
};
