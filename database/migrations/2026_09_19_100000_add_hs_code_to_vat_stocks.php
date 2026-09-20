<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vat_stocks', function (Blueprint $table) {
            $table->string('hs_code', 50)->nullable()->after('item_name');
        });
    }

    public function down(): void
    {
        Schema::table('vat_stocks', function (Blueprint $table) {
            $table->dropColumn('hs_code');
        });
    }
};
