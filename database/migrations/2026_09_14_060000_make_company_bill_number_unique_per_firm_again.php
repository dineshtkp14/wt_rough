<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $duplicates = DB::table('company_bills')
            ->select('firm_id', 'bill_no')
            ->whereNotNull('firm_id')
            ->groupBy('firm_id', 'bill_no')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            $ids = DB::table('company_bills')
                ->where('firm_id', $duplicate->firm_id)
                ->where('bill_no', $duplicate->bill_no)
                ->orderBy('id')
                ->pluck('id');

            foreach ($ids->skip(1) as $id) {
                DB::table('company_bills')->where('id', $id)->update([
                    'bill_no' => $duplicate->bill_no . '-D' . $id,
                ]);
            }
        }

        $hasFirmForeignKey = $this->foreignKeyExists('company_bills', 'company_bills_firm_id_foreign');
        $hasSupplierForeignKey = $this->foreignKeyExists('company_bills', 'company_bills_supplier_id_foreign');
        $hasSupplierUnique = $this->indexExists('company_bills', 'company_bills_supplier_bill_no_unique');
        $hasFirmUnique = $this->indexExists('company_bills', 'company_bills_firm_bill_no_unique');

        Schema::table('company_bills', function (Blueprint $table) use ($hasFirmForeignKey, $hasSupplierForeignKey, $hasSupplierUnique, $hasFirmUnique) {
            // These indexes/foreign keys may already exist when a previous
            // deployment stopped after executing part of the DDL statement.
            if ($hasFirmForeignKey) $table->dropForeign(['firm_id']);
            if ($hasSupplierForeignKey) $table->dropForeign(['supplier_id']);
            if ($hasSupplierUnique) $table->dropUnique('company_bills_supplier_bill_no_unique');
            if ($hasFirmUnique) $table->dropUnique('company_bills_firm_bill_no_unique');
            $table->unique(['firm_id', 'bill_no'], 'company_bills_firm_bill_no_unique');
            $table->foreign('firm_id')->references('id')->on('vat_firms')->cascadeOnDelete();
            $table->foreign('supplier_id')->references('id')->on('vat_suppliers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('company_bills', function (Blueprint $table) {
            $table->dropUnique('company_bills_firm_bill_no_unique');
            $table->unique(['supplier_id', 'bill_no'], 'company_bills_supplier_bill_no_unique');
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        foreach (DB::select('SHOW INDEX FROM `' . str_replace('`', '``', $table) . '`') as $existingIndex) {
            if (($existingIndex->Key_name ?? null) === $index) return true;
        }

        return false;
    }

    private function foreignKeyExists(string $table, string $foreignKey): bool
    {
        $row = DB::selectOne('SHOW CREATE TABLE `' . str_replace('`', '``', $table) . '`');
        if (!$row) return false;

        return str_contains(implode(' ', array_values((array) $row)), 'CONSTRAINT `' . $foreignKey . '`');
    }
};
