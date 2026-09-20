<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customerledgerdetails', function (Blueprint $table) {
            $table->boolean('is_cheque')->default(false)->after('notes');
            $table->string('cheque_bank', 150)->nullable()->after('is_cheque');
            $table->string('cheque_no', 50)->nullable()->after('cheque_bank');
            $table->date('cheque_exchange_date')->nullable()->after('cheque_no');
        });
    }

    public function down(): void
    {
        Schema::table('customerledgerdetails', function (Blueprint $table) {
            $table->dropColumn(['is_cheque', 'cheque_bank', 'cheque_no', 'cheque_exchange_date']);
        });
    }
};
