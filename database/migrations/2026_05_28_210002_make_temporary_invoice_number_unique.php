<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('temporary_invoices') || !Schema::hasColumn('temporary_invoices', 'invoice_number')) {
            return;
        }

        DB::statement("
            UPDATE temporary_invoices
            SET invoice_number = CONCAT('temp-', id)
            WHERE invoice_number IS NULL OR invoice_number = ''
        ");

        $indexes = collect(DB::select("SHOW INDEX FROM temporary_invoices WHERE Column_name = 'invoice_number'"));

        if ($indexes->where('Key_name', 'temporary_invoices_invoice_number_unique')->isEmpty()) {
            DB::statement('ALTER TABLE temporary_invoices ADD UNIQUE temporary_invoices_invoice_number_unique (invoice_number)');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('temporary_invoices')) {
            return;
        }

        $indexes = collect(DB::select("SHOW INDEX FROM temporary_invoices WHERE Key_name = 'temporary_invoices_invoice_number_unique'"));

        if ($indexes->isNotEmpty()) {
            DB::statement('ALTER TABLE temporary_invoices DROP INDEX temporary_invoices_invoice_number_unique');
        }
    }
};
