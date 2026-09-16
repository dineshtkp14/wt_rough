<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('customerinfos', 'credit_list_note')) {
            Schema::table('customerinfos', function (Blueprint $table) {
                $table->text('credit_list_note')->nullable()->after('remarks');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('customerinfos', 'credit_list_note')) {
            Schema::table('customerinfos', function (Blueprint $table) {
                $table->dropColumn('credit_list_note');
            });
        }
    }
};
