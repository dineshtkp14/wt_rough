<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('customerinfos', 'vat_no')) {
            DB::table('customerinfos')->update(['vat_no' => null]);
        }

        if (Schema::hasColumn('companies', 'vat_no')) {
            DB::table('companies')->update(['vat_no' => null]);
        }
    }

    public function down(): void
    {
        // Previously stored VAT numbers are intentionally not restored.
    }
};
