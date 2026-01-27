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
        Schema::table('customerledgerdetails', function (Blueprint $table) {
            //foronlyaddingcreditlimitdayscolumn
            $table->unsignedInteger('credit_limit_days')
            ->nullable()
            ->after('notes');
      
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customerledgerdetails', function (Blueprint $table) {
            
            $table->dropColumn('credit_limit_days');
        });
    }
};
