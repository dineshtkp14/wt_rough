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
        Schema::create('deletedbill', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('invoiceid');
            $table->bigInteger('itemid')->nullable();
            $table->bigInteger('quantity');
            // Add other columns as needed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deletedbill');
    }
};
