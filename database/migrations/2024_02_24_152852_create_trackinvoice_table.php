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
        Schema::create('trackinvoice', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bill_no');
            $table->bigInteger('customer_id');
            $table->bigInteger('initial_customer_id');

            $table->string('title');
            $table->text('updated_by');
            $table->text('notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trackinvoice');
    }
};
