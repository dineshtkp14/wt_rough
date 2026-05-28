<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('temporary_invoices')) {
            Schema::create('temporary_invoices', function (Blueprint $table) {
                $table->id();
                $table->string('customer_name');
                $table->string('contact_number')->nullable();
                $table->string('customer_address')->nullable();
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->decimal('discount', 12, 2)->default(0);
                $table->decimal('total', 12, 2)->default(0);
                $table->date('invoice_date');
                $table->text('notes')->nullable();
                $table->string('invoice_number')->nullable();
                $table->string('added_by')->nullable();
                $table->timestamps();
            });

            return;
        }

        Schema::table('temporary_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('temporary_invoices', 'customer_address')) {
                $table->string('customer_address')->nullable()->after('contact_number');
            }

            if (!Schema::hasColumn('temporary_invoices', 'added_by')) {
                $table->string('added_by')->nullable()->after('invoice_number');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temporary_invoices');
    }
};
