<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['inv_date', 'inv_type'], 'invoices_dashboard_date_type_idx');
        });

        Schema::table('creditnotes_invoices', function (Blueprint $table) {
            $table->index('inv_date', 'creditnotes_invoices_dashboard_date_idx');
        });

        Schema::table('customerledgerdetails', function (Blueprint $table) {
            $table->index(['invoicetype', 'date'], 'customerledger_dashboard_type_date_idx');
            $table->index(['invoiceid', 'credit'], 'customerledger_dashboard_invoice_credit_idx');
        });

        Schema::table('salesitems', function (Blueprint $table) {
            $table->index(['itemid', 'date'], 'salesitems_dashboard_item_date_idx');
        });

        Schema::table('backup_invoices', function (Blueprint $table) {
            $table->index('created_at', 'backup_invoices_dashboard_created_idx');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index('date', 'expenses_dashboard_date_idx');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('invoices_dashboard_date_type_idx');
        });
        Schema::table('creditnotes_invoices', function (Blueprint $table) {
            $table->dropIndex('creditnotes_invoices_dashboard_date_idx');
        });
        Schema::table('customerledgerdetails', function (Blueprint $table) {
            $table->dropIndex('customerledger_dashboard_type_date_idx');
            $table->dropIndex('customerledger_dashboard_invoice_credit_idx');
        });
        Schema::table('salesitems', function (Blueprint $table) {
            $table->dropIndex('salesitems_dashboard_item_date_idx');
        });
        Schema::table('backup_invoices', function (Blueprint $table) {
            $table->dropIndex('backup_invoices_dashboard_created_idx');
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('expenses_dashboard_date_idx');
        });
    }
};
