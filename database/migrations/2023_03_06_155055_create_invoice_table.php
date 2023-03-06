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
        Schema::create('invoice', function (Blueprint $table) {
            $table->id();
          
            $table->bigInteger('customerid');
           
            $table->float('paidamount',20,2);
            $table->float('dueamount',20,2);
            $table->float('subtotal',20,2);
            $table->float('discount',20,2);
            $table->float('total',20,2);
            $table->float('totalamount',20,2);
            $table->string('notes');





            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice');
    }
};
