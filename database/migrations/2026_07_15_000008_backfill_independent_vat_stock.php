<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $firmIds = DB::table('myfirm')
            ->whereIn('nick_name', ['Durga', 'malika'])
            ->pluck('id', 'nick_name');

        $purchases = DB::table('supplier_vat_bill_items as lines')
            ->join('supplier_vat_bills as bills', 'bills.id', '=', 'lines.supplier_vat_bill_id')
            ->orderBy('lines.id')
            ->get([
                'bills.firm_type',
                'lines.item_name',
                'lines.quantity',
                'lines.unit',
                'lines.rate',
            ])
            ->groupBy(function ($line) use ($firmIds) {
                $nickName = str_contains(mb_strtolower($line->firm_type), 'malika') ? 'malika' : 'Durga';

                return $firmIds[$nickName].'|'.mb_strtolower(trim($line->item_name));
            });

        foreach ($purchases as $lines) {
            $lastLine = $lines->last();
            $nickName = str_contains(mb_strtolower($lastLine->firm_type), 'malika') ? 'malika' : 'Durga';
            $firmId = (int) $firmIds[$nickName];
            $itemName = trim($lastLine->item_name);

            $stockItem = DB::table('vat_stock_items')
                ->where('myfirm_id', $firmId)
                ->where('item_name', $itemName)
                ->first();

            if (! $stockItem) {
                $stockId = DB::table('vat_stock_items')->insertGetId([
                    'myfirm_id' => $firmId,
                    'item_name' => $itemName,
                    'unit' => $lastLine->unit ?: 'pcs',
                    'quantity' => 0,
                    'cost_price' => $lastLine->rate,
                    'sale_rate' => $lastLine->rate,
                    'warning_quantity' => 0,
                    'notes' => 'Created from supplier VAT bill history.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $stockId = $stockItem->id;
            }

            $purchasedQuantity = (float) $lines->sum('quantity');
            $soldQuantity = (float) DB::table('customer_vat_sale_items')
                ->where('vat_stock_item_id', $stockId)
                ->sum('quantity');

            DB::table('vat_stock_items')->where('id', $stockId)->update([
                'unit' => $lastLine->unit ?: 'pcs',
                'quantity' => max(0, round($purchasedQuantity - $soldQuantity, 3)),
                'cost_price' => $lastLine->rate,
                'sale_rate' => $stockItem?->sale_rate ?: $lastLine->rate,
                'updated_at' => now(),
            ]);
        }

        Schema::table('customer_vat_sale_items', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropColumn('item_id');
        });
    }

    public function down(): void
    {
        Schema::table('customer_vat_sale_items', function (Blueprint $table) {
            $table->foreignId('item_id')->nullable()->after('customer_vat_sale_id')->constrained('items')->restrictOnDelete();
        });
    }
};
