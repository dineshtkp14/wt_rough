<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vat_stock_movements', function (Blueprint $table) {
            $table->string('user_email', 150)->nullable()->after('notes');
            $table->decimal('quantity_before', 20, 3)->nullable()->after('user_email');
            $table->decimal('quantity_after', 20, 3)->nullable()->after('quantity_before');
        });
    }

    public function down(): void
    {
        Schema::table('vat_stock_movements', function (Blueprint $table) {
            $table->dropColumn(['user_email', 'quantity_before', 'quantity_after']);
        });
    }
};
