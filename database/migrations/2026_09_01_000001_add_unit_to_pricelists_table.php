<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pricelists', function (Blueprint $table) {
            if (!Schema::hasColumn('pricelists', 'unit')) {
                $table->string('unit', 30)->nullable()->after('itemname');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pricelists', function (Blueprint $table) {
            if (Schema::hasColumn('pricelists', 'unit')) {
                $table->dropColumn('unit');
            }
        });
    }
};
