<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $duplicates = DB::table('company_bills')
            ->select('firm_id', 'supplier_id', 'bill_no')
            ->whereNotNull('firm_id')->whereNotNull('supplier_id')
            ->groupBy('firm_id', 'supplier_id', 'bill_no')
            ->havingRaw('COUNT(*) > 1')->get();

        foreach ($duplicates as $duplicate) {
            $ids = DB::table('company_bills')->where('firm_id', $duplicate->firm_id)
                ->where('supplier_id', $duplicate->supplier_id)->where('bill_no', $duplicate->bill_no)
                ->orderBy('id')->pluck('id');
            foreach ($ids->skip(1) as $id) {
                DB::table('company_bills')->where('id', $id)->update(['bill_no' => $duplicate->bill_no . '-D' . $id]);
            }
        }

        Schema::table('company_bills', function (Blueprint $table) {
            $table->dropForeign(['firm_id']);
            $table->dropForeign(['supplier_id']);
            $table->dropUnique('company_bills_firm_bill_no_unique');
            $table->unique(['firm_id', 'supplier_id', 'bill_no'], 'company_bills_firm_supplier_bill_no_unique');
            $table->foreign('firm_id')->references('id')->on('vat_firms')->cascadeOnDelete();
            $table->foreign('supplier_id')->references('id')->on('vat_suppliers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('company_bills', function (Blueprint $table) {
            $table->dropUnique('company_bills_firm_supplier_bill_no_unique');
            $table->unique(['firm_id', 'bill_no'], 'company_bills_firm_bill_no_unique');
        });
    }
};
