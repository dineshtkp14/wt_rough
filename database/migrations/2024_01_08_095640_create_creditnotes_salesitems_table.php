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
        Schema::create('creditnotes_salesitems', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('invoiceid');
           
            $table->bigInteger('itemid')->nullable();

            $table->string('unstockedname')->nullable();
            $table->float('quantity',20,2);
            $table->float('price',20,2);
            $table->date('date');
            $table->float('subtotal',20,2);
            $table->timestamps();
            $table->string('unit')->nullable();

            $table->string('added_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creditnotes_salesitems');
    }
};
