<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vat_customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('address')->nullable();
            $table->string('pan_no', 50)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('notes')->nullable();
            $table->string('added_by')->nullable();
            $table->timestamps();
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vat_customers');
    }
};
