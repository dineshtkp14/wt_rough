<?php

use App\Support\FiscalNumber;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('fiscal_invoice_no', 24)->nullable()->after('id');
        });

        Schema::table('customerledgerdetails', function (Blueprint $table) {
            $table->string('fiscal_receipt_no', 28)->nullable()->after('id');
        });

        $invoiceSequences = [];
        DB::table('invoices')->orderBy('inv_date')->orderBy('id')->each(function ($invoice) use (&$invoiceSequences) {
            $fiscalYear = FiscalNumber::fiscalYearForDate($invoice->inv_date);
            $sequence = ($invoiceSequences[$fiscalYear] ?? 0) + 1;
            $invoiceSequences[$fiscalYear] = $sequence;

            DB::table('invoices')->where('id', $invoice->id)->update([
                'fiscal_invoice_no' => FiscalNumber::format($fiscalYear, $sequence),
            ]);
        });

        $receiptSequences = [];
        DB::table('customerledgerdetails')
            ->where('invoicetype', 'payment')
            ->orderBy('date')
            ->orderBy('id')
            ->each(function ($receipt) use (&$receiptSequences) {
                $fiscalYear = FiscalNumber::fiscalYearForDate($receipt->date);
                $sequence = ($receiptSequences[$fiscalYear] ?? 0) + 1;
                $receiptSequences[$fiscalYear] = $sequence;

                DB::table('customerledgerdetails')->where('id', $receipt->id)->update([
                    'fiscal_receipt_no' => FiscalNumber::format($fiscalYear, $sequence, 'CR'),
                ]);
            });

        Schema::table('invoices', function (Blueprint $table) {
            $table->unique('fiscal_invoice_no');
        });

        Schema::table('customerledgerdetails', function (Blueprint $table) {
            $table->unique('fiscal_receipt_no');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropUnique(['fiscal_invoice_no']);
            $table->dropColumn('fiscal_invoice_no');
        });

        Schema::table('customerledgerdetails', function (Blueprint $table) {
            $table->dropUnique(['fiscal_receipt_no']);
            $table->dropColumn('fiscal_receipt_no');
        });
    }
};
