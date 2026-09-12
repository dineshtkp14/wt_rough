<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('company_bills', function (Blueprint $table) {
            $table->id(); $table->string('company_name',150); $table->string('company_vat_no',50)->nullable(); $table->string('company_pan_no',50)->nullable(); $table->string('company_address')->nullable(); $table->string('company_phone',30)->nullable(); $table->string('bill_no',50); $table->date('bill_date'); $table->string('payment_mode',50)->nullable(); $table->decimal('discount',20,2)->default(0); $table->text('notes')->nullable(); $table->string('added_by')->nullable(); $table->timestamps(); $table->unique('bill_no');
        });
        Schema::create('company_bill_items', function (Blueprint $table) { $table->id(); $table->foreignId('company_bill_id')->constrained()->cascadeOnDelete(); $table->string('item_name',200); $table->string('hs_code',50)->nullable(); $table->string('unit',30); $table->decimal('quantity',20,3); $table->decimal('rate',20,2); $table->boolean('is_taxable')->default(true); $table->timestamps(); });
    }
    public function down(): void { Schema::dropIfExists('company_bill_items'); Schema::dropIfExists('company_bills'); }
};
