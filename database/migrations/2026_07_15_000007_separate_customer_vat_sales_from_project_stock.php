<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_vat_sale_items', function (Blueprint $table) {
            $table->foreignId('vat_stock_item_id')
                ->nullable()
                ->after('item_id')
                ->constrained('vat_stock_items')
                ->restrictOnDelete();
        });

        DB::statement('ALTER TABLE customer_vat_sale_items MODIFY item_id BIGINT UNSIGNED NULL');

        $legacyLines = DB::table('customer_vat_sale_items as lines')
            ->join('customer_vat_sales as sales', 'sales.id', '=', 'lines.customer_vat_sale_id')
            ->leftJoin('items', 'items.id', '=', 'lines.item_id')
            ->whereNotNull('lines.item_id')
            ->get([
                'lines.id',
                'lines.item_id',
                'lines.item_name',
                'lines.quantity',
                'lines.unit',
                'lines.rate',
                'sales.myfirm_id',
            ]);

        foreach ($legacyLines as $line) {
            $vatStockId = DB::table('vat_stock_items')
                ->where('myfirm_id', $line->myfirm_id)
                ->where('item_name', $line->item_name)
                ->value('id');

            if (! $vatStockId) {
                $vatStockId = DB::table('vat_stock_items')->insertGetId([
                    'myfirm_id' => $line->myfirm_id,
                    'item_name' => $line->item_name,
                    'unit' => $line->unit ?: 'pcs',
                    'quantity' => 0,
                    'cost_price' => 0,
                    'sale_rate' => $line->rate,
                    'warning_quantity' => 0,
                    'notes' => 'Migrated from an earlier customer VAT sale.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('customer_vat_sale_items')->where('id', $line->id)->update([
                'vat_stock_item_id' => $vatStockId,
            ]);

            DB::table('items')->where('id', $line->item_id)->increment('quantity', $line->quantity);
        }
    }

    public function down(): void
    {
        Schema::table('customer_vat_sale_items', function (Blueprint $table) {
            $table->dropForeign(['vat_stock_item_id']);
            $table->dropColumn('vat_stock_item_id');
        });
    }
};
