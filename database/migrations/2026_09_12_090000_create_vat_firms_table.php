<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vat_firms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('pan_no', 50)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('phone', 60)->nullable();
            $table->string('email', 150)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('vat_firms')->insert([
            ['name' => 'MALIKA AND NAWADURGA TRADERS', 'pan_no' => '302761801', 'address' => 'TIKAPUR, KAILALI', 'phone' => '9860378262, 9812656284', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'DURGA AND DINESH TRADERS', 'pan_no' => '601064191', 'address' => 'TIKAPUR, KAILALI', 'phone' => '9860378262, 9812656284', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('vat_firms');
    }
};
