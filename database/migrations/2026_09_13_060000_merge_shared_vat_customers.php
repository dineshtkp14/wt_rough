<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $groups = [];
        foreach (DB::table('vat_customers')->orderBy('id')->get() as $customer) {
            $key = strtolower(trim($customer->name).'|'.trim((string) $customer->pan_no).'|'.trim((string) $customer->phone).'|'.trim((string) $customer->address));
            $groups[$key][] = $customer;
        }

        foreach ($groups as $customers) {
            $master = $customers[0];
            foreach (array_slice($customers, 1) as $duplicate) {
                DB::table('vat_system_bills')->where('customer_id', $duplicate->id)->update(['customer_id' => $master->id]);
                DB::table('vat_customers')->where('id', $duplicate->id)->delete();
            }
        }

        DB::table('vat_customers')->update(['firm_id' => null]);
    }

    public function down(): void
    {
        // Shared customer records are intentionally not split again.
    }
};
