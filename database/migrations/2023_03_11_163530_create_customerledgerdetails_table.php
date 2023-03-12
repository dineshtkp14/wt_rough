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
        Schema::create('customerledgerdetails', function (Blueprint $table) {
            $table->id();
          
            $table->bigInteger('customerid');
            $table->bigInteger('invoiceid');
            $table->string('particulars');
            $table->string('voucher_type');
            $table->float('debit',20,2)->nullable();
            $table->float('credit',20,2)->nullable();
            $table->string('invoicetype');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customerledgerdetails');
    }
};
