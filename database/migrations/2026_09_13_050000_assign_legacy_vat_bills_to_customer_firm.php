<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            UPDATE vat_system_bills AS bills
            INNER JOIN vat_customers AS customers ON customers.id = bills.customer_id
            SET bills.firm_id = customers.firm_id
            WHERE bills.firm_id IS NULL
              AND customers.firm_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        // Existing firm assignments are retained when rolling back.
    }
};
