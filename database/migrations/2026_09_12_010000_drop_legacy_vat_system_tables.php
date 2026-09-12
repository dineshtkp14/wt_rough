<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ([
            'customer_vat_sale_items',
            'customer_vat_sales',
            'supplier_vat_bill_items',
            'supplier_vat_bills',
            'vat_confirmation_details',
            'vat_bills',
            'vat_stock_items',
            'myfirm',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // The VAT system is being rebuilt from scratch; old table structures
        // and data are intentionally not restored by rollback.
    }
};
