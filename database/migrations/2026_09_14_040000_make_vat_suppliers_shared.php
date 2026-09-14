<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $groups = DB::table('vat_suppliers')->select('name')->groupBy('name')->havingRaw('COUNT(*) > 1')->pluck('name');
        foreach ($groups as $name) {
            $suppliers = DB::table('vat_suppliers')->where('name', $name)->orderBy('id')->pluck('id');
            $keep = $suppliers->first();
            foreach ($suppliers->skip(1) as $duplicate) {
                DB::table('company_bills')->where('supplier_id', $duplicate)->update(['supplier_id' => $keep]);
                DB::table('vat_suppliers')->where('id', $duplicate)->delete();
            }
        }
        Schema::table('vat_suppliers', function (Blueprint $table) {
            $table->dropForeign(['firm_id']);
            $table->dropUnique('vat_suppliers_firm_id_name_unique');
            $table->unsignedBigInteger('firm_id')->nullable()->change();
            $table->unique('name', 'vat_suppliers_name_unique');
            $table->foreign('firm_id')->references('id')->on('vat_firms')->nullOnDelete();
        });
        DB::table('vat_suppliers')->update(['firm_id' => null]);
    }

    public function down(): void
    {
        Schema::table('vat_suppliers', function (Blueprint $table) {
            $table->dropUnique('vat_suppliers_name_unique');
            $table->unique(['firm_id', 'name']);
        });
    }
};
