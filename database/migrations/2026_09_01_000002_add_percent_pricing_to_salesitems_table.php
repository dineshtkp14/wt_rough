<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salesitems', function (Blueprint $table) {
            if (!Schema::hasColumn('salesitems', 'list_price')) {
                $table->decimal('list_price', 20, 2)->nullable()->after('price');
            }

            if (!Schema::hasColumn('salesitems', 'discount_percent')) {
                $table->decimal('discount_percent', 8, 2)->nullable()->after('list_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('salesitems', function (Blueprint $table) {
            if (Schema::hasColumn('salesitems', 'discount_percent')) {
                $table->dropColumn('discount_percent');
            }

            if (Schema::hasColumn('salesitems', 'list_price')) {
                $table->dropColumn('list_price');
            }
        });
    }
};
