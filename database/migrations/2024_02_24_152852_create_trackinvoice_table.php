<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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

        // Make created_at and updated_at columns not nullable
        Schema::table('trackinvoice', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP'))->change();
            $table->timestamp('updated_at')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->change();
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
