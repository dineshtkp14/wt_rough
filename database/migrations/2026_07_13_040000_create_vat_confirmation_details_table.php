<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vat_confirmation_details', function (Blueprint $table) {
            $table->id();
            $table->string('party_key', 191);
            $table->string('firm_type');
            $table->date('from_date');
            $table->date('to_date');
            $table->decimal('purchase_exempted', 20, 2)->default(0);
            $table->decimal('purchase_taxable', 20, 2)->default(0);
            $table->decimal('purchase_return_exempted', 20, 2)->default(0);
            $table->decimal('purchase_return_taxable', 20, 2)->default(0);
            $table->decimal('sales_return_exempted', 20, 2)->default(0);
            $table->decimal('sales_return_taxable', 20, 2)->default(0);
            $table->decimal('opening_balance_amount', 20, 2)->nullable();
            $table->string('opening_balance_side', 10)->nullable();
            $table->decimal('closing_balance_amount', 20, 2)->nullable();
            $table->string('closing_balance_side', 10)->nullable();
            $table->string('added_by')->nullable();
            $table->timestamps();
            $table->unique(['party_key', 'firm_type', 'from_date', 'to_date'], 'vat_confirmation_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vat_confirmation_details');
    }
};
