<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vat_system_bills', function (Blueprint $table) {
            $table->id(); $table->foreignId('customer_id')->constrained('vat_customers')->restrictOnDelete();
            $table->string('seller_name', 150); $table->string('seller_vat_no', 50)->nullable(); $table->string('seller_phone', 30)->nullable(); $table->string('seller_email', 150)->nullable();
            $table->string('bill_no', 50); $table->date('bill_date'); $table->string('payment_mode', 50)->nullable(); $table->decimal('discount', 20, 2)->default(0); $table->text('notes')->nullable(); $table->string('added_by')->nullable(); $table->timestamps();
        });
        Schema::create('vat_system_bill_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('vat_system_bill_id')->constrained('vat_system_bills')->cascadeOnDelete(); $table->string('item_name', 200); $table->string('hs_code', 50)->nullable(); $table->string('unit', 30); $table->decimal('quantity', 20, 3); $table->decimal('rate', 20, 2); $table->boolean('is_taxable')->default(false); $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('vat_system_bill_items'); Schema::dropIfExists('vat_system_bills'); }
};
