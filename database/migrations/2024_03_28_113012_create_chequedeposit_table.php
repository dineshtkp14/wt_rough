<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chequedeposit', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->date('cheque_date');
            $table->float('amount', 20, 2);
            $table->text('notes')->nullable();
            $table->string('bank_name'); 
            $table->integer('customerid'); 
            $table->timestamps();
            $table->string('added_by')->nullable(); 

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chequedeposit');
    }
};
