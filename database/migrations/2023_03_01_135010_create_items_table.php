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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
              $table->string('billno');
            $table->string('distributorname');
            $table->date('date');
            $table->string('itemsname');
           

            $table->float('quantity',20,2);
            $table->float('dlp',20,2);
            $table->float('mrp',20,2);
            $table->float('total',20,2);
            $table->float('finaltotal',20,2);



          
         
           
           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
