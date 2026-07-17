<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_vat_sales', function (Blueprint $table) {
            $table->string('customer_name')->nullable()->after('customer_id');
            $table->string('customer_vat_no', 100)->nullable()->after('customer_name');
        });

        DB::table('customer_vat_sales')
            ->join('customerinfos', 'customerinfos.id', '=', 'customer_vat_sales.customer_id')
            ->update([
                'customer_vat_sales.customer_name' => DB::raw('customerinfos.name'),
                'customer_vat_sales.customer_vat_no' => DB::raw('customerinfos.vat_no'),
            ]);

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE customer_vat_sales MODIFY customer_id BIGINT UNSIGNED NULL');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE customer_vat_sales MODIFY customer_id BIGINT UNSIGNED NOT NULL');
        }

        Schema::table('customer_vat_sales', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'customer_vat_no']);
        });
    }
};
