<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_vat_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_vat_sale_id')->constrained('customer_vat_sales')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->string('item_name');
            $table->decimal('quantity', 20, 3);
            $table->string('unit', 30);
            $table->decimal('rate', 20, 2);
            $table->decimal('amount', 20, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_vat_sale_items');
    }
};
