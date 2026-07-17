<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_vat_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->string('bill_no', 100);
            $table->date('bill_date');
            $table->decimal('taxable_amount', 20, 2);
            $table->decimal('vat_rate', 5, 2)->default(13);
            $table->decimal('vat_amount', 20, 2);
            $table->decimal('total_amount', 20, 2);
            $table->text('notes')->nullable();
            $table->string('added_by')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'bill_no'], 'supplier_vat_bill_company_bill_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_vat_bills');
    }
};
