<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vat_customers', function (Blueprint $table) {
            $table->foreignId('firm_id')->nullable()->after('id')->constrained('vat_firms')->nullOnDelete();
            $table->index('firm_id');
        });

        $defaultFirmId = DB::table('vat_firms')->orderBy('id')->value('id');
        foreach (DB::table('vat_customers')->orderBy('id')->get() as $customer) {
            $firmIds = DB::table('vat_system_bills')->where('customer_id', $customer->id)->whereNotNull('firm_id')->distinct()->pluck('firm_id')->values();
            $primaryFirmId = $firmIds->shift() ?? $defaultFirmId;
            if (!$primaryFirmId) continue;

            DB::table('vat_customers')->where('id', $customer->id)->update(['firm_id' => $primaryFirmId]);
            foreach ($firmIds as $firmId) {
                $copy = (array) $customer;
                unset($copy['id']);
                $copy['firm_id'] = $firmId;
                $newCustomerId = DB::table('vat_customers')->insertGetId($copy);
                DB::table('vat_system_bills')->where('customer_id', $customer->id)->where('firm_id', $firmId)->update(['customer_id' => $newCustomerId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('vat_customers', function (Blueprint $table) {
            $table->dropForeign(['firm_id']);
            $table->dropIndex(['firm_id']);
            $table->dropColumn('firm_id');
        });
    }
};
