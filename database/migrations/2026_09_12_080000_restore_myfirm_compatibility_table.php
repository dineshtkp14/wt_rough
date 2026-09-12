<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('myfirm')) {
            Schema::create('myfirm', function (Blueprint $table) {
                $table->id();
                $table->string('firm_name');
                $table->string('nick_name');
                $table->string('notes')->nullable();
                $table->string('added_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('myfirm');
    }
};
