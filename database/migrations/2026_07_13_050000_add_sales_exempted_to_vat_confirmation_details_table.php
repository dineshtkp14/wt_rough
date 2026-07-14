<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vat_confirmation_details') && !Schema::hasColumn('vat_confirmation_details', 'sales_exempted')) {
            Schema::table('vat_confirmation_details', function (Blueprint $table) {
                $table->decimal('sales_exempted', 20, 2)->default(0)->after('purchase_return_taxable');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('vat_confirmation_details') && Schema::hasColumn('vat_confirmation_details', 'sales_exempted')) {
            Schema::table('vat_confirmation_details', function (Blueprint $table) {
                $table->dropColumn('sales_exempted');
            });
        }
    }
};
