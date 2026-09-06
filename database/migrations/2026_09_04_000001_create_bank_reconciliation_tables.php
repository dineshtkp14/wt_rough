<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('bank_statement_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()->cascadeOnDelete();
            $table->date('transaction_date');
            $table->string('description')->nullable();
            $table->string('reference')->nullable();
            $table->decimal('amount', 15, 2); // credits positive, withdrawals negative
            $table->decimal('running_balance', 15, 2)->nullable();
            $table->string('status')->default('unmatched');
            $table->string('matched_type')->nullable();
            $table->unsignedBigInteger('matched_id')->nullable();
            $table->foreignId('imported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['bank_account_id', 'transaction_date'], 'bst_account_date_idx');
            $table->index(['matched_type', 'matched_id'], 'bst_match_idx');
        });

        Schema::create('bank_reconciliation_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()->cascadeOnDelete();
            $table->date('adjustment_date');
            $table->string('description');
            $table->decimal('amount', 15, 2); // positive for money in, negative for money out
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_reconciliation_adjustments');
        Schema::dropIfExists('bank_statement_transactions');
        Schema::dropIfExists('bank_accounts');
    }
};
