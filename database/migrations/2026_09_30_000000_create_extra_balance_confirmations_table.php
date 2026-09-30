<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extra_balance_confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firm_id')->constrained('vat_firms')->restrictOnDelete();
            $table->string('party_name', 150);
            $table->string('party_vat_no', 50)->nullable();
            $table->string('party_address')->nullable();
            $table->string('party_phone', 30)->nullable();
            $table->string('date_bs', 20);
            $table->string('period_from_bs', 20)->nullable();
            $table->string('period_to_bs', 20)->nullable();
            $table->decimal('purchase_exempted', 20, 2)->default(0);
            $table->decimal('purchase_taxable', 20, 2)->default(0);
            $table->decimal('purchase_vat', 20, 2)->default(0);
            $table->decimal('purchase_return_exempted', 20, 2)->default(0);
            $table->decimal('purchase_return_taxable', 20, 2)->default(0);
            $table->decimal('purchase_return_vat', 20, 2)->default(0);
            $table->decimal('sales_exempted', 20, 2)->default(0);
            $table->decimal('sales_taxable', 20, 2)->default(0);
            $table->decimal('sales_vat', 20, 2)->default(0);
            $table->decimal('sales_return_exempted', 20, 2)->default(0);
            $table->decimal('sales_return_taxable', 20, 2)->default(0);
            $table->decimal('sales_return_vat', 20, 2)->default(0);
            $table->decimal('opening_balance', 20, 2)->default(0);
            $table->decimal('closing_balance', 20, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('extra_balance_confirmations'); }
};
