<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('temporary_invoice_items')) {
            Schema::create('temporary_invoice_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('temporary_invoice_id')->constrained('temporary_invoices')->cascadeOnDelete();
                $table->string('item_name');
                $table->string('unstocked_item')->nullable();
                $table->decimal('quantity', 12, 2)->default(0);
                $table->string('unit')->nullable();
                $table->decimal('price', 12, 2)->default(0);
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('temporary_invoice_items');
    }
};
