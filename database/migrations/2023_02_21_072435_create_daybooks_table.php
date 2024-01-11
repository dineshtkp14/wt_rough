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
        Schema::create('daybooks', function (Blueprint $table) {
            $table->id();
           
            $table->string('name');
            $table->string('address');
            $table->string('contact');
            $table->float('amount',20,2);
            $table->date('date');
            $table->string('modeofpay');
            $table->string('remarks')->nullable();
            $table->string('added_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daybooks');
    }
};
