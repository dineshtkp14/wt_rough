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
        Schema::create('company_ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('particulars');
            $table->string('voucher_type');
            $table->string('voucher_no')->nullable();
            $table->date('date');
            $table->float('debit',20,2)->nullable();
            $table->float('credit',20,2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_ledgers');
    }
};
