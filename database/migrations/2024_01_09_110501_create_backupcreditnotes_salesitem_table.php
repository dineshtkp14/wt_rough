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
        Schema::create('backupcreditnotes_salesitem', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('invoiceid');
            $table->date('date')->nullable();;
           
            $table->bigInteger('itemid')->nullable();
           // $table->string('itemname')->nullable();

            $table->string('unstockedname')->nullable();;
            $table->float('quantity',20,2);
            $table->float('price',20,2);
            $table->float('subtotal',20,2);
            $table->string('unit')->nullable();

            $table->timestamps();
            $table->string('added_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backupcreditnotes_salesitem');
    }
};
