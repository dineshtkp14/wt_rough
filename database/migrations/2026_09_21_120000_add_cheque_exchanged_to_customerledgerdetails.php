<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customerledgerdetails', function (Blueprint $table) {
            $table->boolean('cheque_exchanged')->default(false)->after('cheque_exchange_date');
        });
    }

    public function down(): void
    {
        Schema::table('customerledgerdetails', function (Blueprint $table) {
            $table->dropColumn('cheque_exchanged');
        });
    }
};
