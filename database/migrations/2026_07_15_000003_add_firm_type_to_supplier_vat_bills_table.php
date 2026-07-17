<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_vat_bills', function (Blueprint $table) {
            $table->string('firm_type')->nullable()->after('company_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('supplier_vat_bills', function (Blueprint $table) {
            $table->dropIndex(['firm_type']);
            $table->dropColumn('firm_type');
        });
    }
};
