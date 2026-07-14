<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('vat_bills', 'party_name')) {
            Schema::table('vat_bills', function (Blueprint $table) {
                $table->string('party_name')->nullable()->after('firm_type');
                $table->string('party_address')->nullable()->after('party_name');
                $table->string('party_vat_no', 50)->nullable()->after('party_address');
                $table->string('party_phone', 50)->nullable()->after('party_vat_no');
            });
        }

        DB::statement('UPDATE vat_bills vb
            INNER JOIN invoices i ON i.id = vb.invoice_id
            INNER JOIN customerinfos c ON c.id = i.customerid
            SET vb.party_name = COALESCE(vb.party_name, c.name),
                vb.party_address = COALESCE(vb.party_address, c.address),
                vb.party_vat_no = COALESCE(vb.party_vat_no, c.vat_no),
                vb.party_phone = COALESCE(vb.party_phone, c.phoneno)');
    }

    public function down(): void
    {
        Schema::table('vat_bills', function (Blueprint $table) {
            $table->dropColumn(['party_name', 'party_address', 'party_vat_no', 'party_phone']);
        });
    }
};
