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
            $table->string('billno')->nullable();
            $table->string('distributorname');
            $table->date('date');
            $table->string('itemsname');
           

            $table->float('quantity',20,2);
            $table->string('unit')->nullable();;

            $table->float('costprice',20,2);
            $table->float('mrp',20,2);

            //lastlyadded
            $table->bigInteger('companyid');

            $table->string('wholesale_price')->nullable();;
            $table->string('com_Retail_price')->nullable();;
            $table->string('com_wholesale_price')->nullable();;
            $table->float('total',20,2);
            $table->float('showwarning',20,2);
            $table->string('notes')->nullable();
            $table->string('added_by')->nullable();
            $table->string('firm_name')->nullable();

            $table->string('check_remove_ofs')->default(0);


          



          
         
           
           
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
