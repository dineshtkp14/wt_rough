<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('vat_stocks', function(Blueprint $table){$table->id();$table->string('item_name',200);$table->string('unit',30);$table->decimal('quantity',20,3)->default(0);$table->decimal('purchase_rate',20,2)->default(0);$table->decimal('sale_rate',20,2)->default(0);$table->decimal('reorder_level',20,3)->default(0);$table->text('notes')->nullable();$table->timestamps();$table->unique(['item_name','unit']);});
        Schema::create('vat_stock_movements', function(Blueprint $table){$table->id();$table->foreignId('vat_stock_id')->constrained('vat_stocks')->cascadeOnDelete();$table->string('movement_type',20);$table->decimal('quantity',20,3);$table->decimal('rate',20,2)->default(0);$table->string('reference')->nullable();$table->text('notes')->nullable();$table->timestamps();});
    }
    public function down(): void { Schema::dropIfExists('vat_stock_movements');Schema::dropIfExists('vat_stocks'); }
};
