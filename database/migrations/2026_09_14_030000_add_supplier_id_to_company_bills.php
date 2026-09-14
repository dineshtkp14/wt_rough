<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('company_bills', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('firm_id')->constrained('vat_suppliers')->nullOnDelete();
        });
        foreach (DB::table('company_bills')->get(['id', 'firm_id', 'company_name']) as $bill) {
            $supplierId = DB::table('vat_suppliers')->where('firm_id', $bill->firm_id)->where('name', $bill->company_name)->value('id');
            if ($supplierId) DB::table('company_bills')->where('id', $bill->id)->update(['supplier_id' => $supplierId]);
        }
    }

    public function down(): void
    {
        Schema::table('company_bills', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supplier_id');
        });
    }
};
