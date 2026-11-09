<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'nav_visibility')) {
            Schema::table('users', function (Blueprint $table) {
                $table->text('nav_visibility')->nullable()->after('remember_token');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'nav_visibility')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('nav_visibility');
            });
        }
    }
};
