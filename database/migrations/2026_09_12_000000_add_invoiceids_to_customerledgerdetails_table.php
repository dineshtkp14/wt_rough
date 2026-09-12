<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customerledgerdetails', function (Blueprint $table) {
            $table->text('invoiceids')->nullable()->after('invoiceid');
        });
    }

    public function down(): void
    {
        Schema::table('customerledgerdetails', function (Blueprint $table) {
            $table->dropColumn('invoiceids');
        });
    }
};
