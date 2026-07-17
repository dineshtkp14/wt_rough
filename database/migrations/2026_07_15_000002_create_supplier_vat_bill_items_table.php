<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_vat_bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_vat_bill_id')->constrained('supplier_vat_bills')->cascadeOnDelete();
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
        Schema::dropIfExists('supplier_vat_bill_items');
    }
};
