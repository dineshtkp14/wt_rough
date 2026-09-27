<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vat_extra_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firm_id')->nullable()->constrained('vat_firms')->nullOnDelete();
            $table->string('name', 150);
            $table->string('address')->nullable();
            $table->string('contact_name', 150)->nullable();
            $table->text('notes')->nullable();
            $table->string('added_by')->nullable();
            $table->timestamps();
            $table->index(['firm_id', 'name']);
        });

        Schema::table('vat_system_bills', function (Blueprint $table) {
            $table->foreignId('extra_customer_id')->nullable()->after('customer_id')->constrained('vat_extra_customers')->nullOnDelete();
        });

        // The existing customer_id remains untouched for all old invoices, but new
        // invoices may use the separate extra-customer record instead.
        DB::statement('ALTER TABLE `vat_system_bills` MODIFY `customer_id` BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `vat_system_bills` MODIFY `customer_id` BIGINT UNSIGNED NOT NULL');
        Schema::table('vat_system_bills', function (Blueprint $table) {
            $table->dropConstrainedForeignId('extra_customer_id');
        });
        Schema::dropIfExists('vat_extra_customers');
    }
};
