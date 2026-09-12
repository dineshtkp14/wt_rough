<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Preserve existing bills while making every bill number unique.
        DB::table('vat_system_bills')->select('bill_no')->whereNotNull('bill_no')->distinct()->pluck('bill_no')->each(function ($billNo) {
            $ids = DB::table('vat_system_bills')->where('bill_no', $billNo)->orderBy('id')->pluck('id');
            foreach ($ids->skip(1) as $id) {
                DB::table('vat_system_bills')->where('id', $id)->update(['bill_no' => $billNo . '-D' . $id]);
            }
        });

        Schema::table('vat_system_bills', function (Blueprint $table) {
            $table->unique('bill_no', 'vat_system_bills_bill_no_unique');
        });
    }

    public function down(): void
    {
        Schema::table('vat_system_bills', function (Blueprint $table) {
            $table->dropUnique('vat_system_bills_bill_no_unique');
        });
    }
};
