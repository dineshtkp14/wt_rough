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
        Schema::create('backup_invoices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customerid');
            $table->bigInteger('invoice_id');
            $table->float('subtotal',20,2);
            $table->float('discount',20,2);
            $table->float('total',20,2);
            $table->string('inv_type')->nullable();;
            $table->date('inv_date')->nullable();

            $table->text('notes')->nullable();;
            $table->timestamps();
            $table->string('added_by')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_invoices');
    }
};
