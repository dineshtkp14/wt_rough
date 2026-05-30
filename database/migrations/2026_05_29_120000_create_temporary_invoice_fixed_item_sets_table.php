<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('temporary_invoice_fixed_item_sets')) {
            Schema::create('temporary_invoice_fixed_item_sets', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('temporary_invoice_fixed_item_set_items')) {
            Schema::create('temporary_invoice_fixed_item_set_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('temporary_invoice_fixed_item_set_id');
                $table->string('item_name');
                $table->decimal('quantity', 12, 2)->default(0);
                $table->string('unit')->nullable();
                $table->decimal('price', 12, 2)->default(0);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->foreign(
                    'temporary_invoice_fixed_item_set_id',
                    'tmp_inv_fixed_items_set_fk'
                )->references('id')->on('temporary_invoice_fixed_item_sets')->cascadeOnDelete();
            });
        }

        $setId = DB::table('temporary_invoice_fixed_item_sets')
            ->where('code', 'bcm300c')
            ->value('id');

        if (!$setId) {
            $now = now();
            $setId = DB::table('temporary_invoice_fixed_item_sets')->insertGetId([
                'code' => 'bcm300c',
                'name' => '0.5hp bharu bcm300c',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $items = [
                ['0.5hp bharu bcm300c', 1, 'Pcs', 7500],
                ['Tee 1.25"', 1, 'Pcs', 180],
                ['Nipple 1.25*6"', 1, 'Pcs', 100],
                ['Nipple 3 feet', 1, 'Pcs', 350],
                ['Nipple 1*6"', 2, 'Pcs', 80],
                ['Nipple 3/4"', 1, 'Pc', 70],
                ['Elbow 3.4*1"', 1, 'Pc', 120],
                ['Ghantiwall 1"', 1, 'Pc', 400],
                ['Seal tape', 1, 'Pc', 160],
                ['Black tape', 1, 'Pc', 25],
                ['Mseal', 1, 'Pc', 70],
            ];

            foreach ($items as $index => $item) {
                DB::table('temporary_invoice_fixed_item_set_items')->insert([
                    'temporary_invoice_fixed_item_set_id' => $setId,
                    'item_name' => $item[0],
                    'quantity' => $item[1],
                    'unit' => $item[2],
                    'price' => $item[3],
                    'sort_order' => $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('temporary_invoice_fixed_item_set_items');
        Schema::dropIfExists('temporary_invoice_fixed_item_sets');
    }
};
