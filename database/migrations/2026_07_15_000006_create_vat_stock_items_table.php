<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vat_stock_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('myfirm_id')->constrained('myfirm')->restrictOnDelete();
            $table->string('item_name');
            $table->string('unit', 30)->default('pcs');
            $table->decimal('quantity', 20, 3)->default(0);
            $table->decimal('cost_price', 20, 2)->default(0);
            $table->decimal('sale_rate', 20, 2)->default(0);
            $table->decimal('warning_quantity', 20, 3)->default(0);
            $table->text('notes')->nullable();
            $table->string('added_by')->nullable();
            $table->timestamps();

            $table->unique(['myfirm_id', 'item_name'], 'vat_stock_firm_item_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vat_stock_items');
    }
};
