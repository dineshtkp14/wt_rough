<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankStatementTransaction extends Model
{
    protected $fillable = ['bank_account_id', 'transaction_date', 'description', 'reference', 'amount', 'running_balance', 'status', 'matched_type', 'matched_id', 'imported_by'];
    protected $casts = ['transaction_date' => 'date', 'amount' => 'decimal:2', 'running_balance' => 'decimal:2'];
    public function account() { return $this->belongsTo(BankAccount::class, 'bank_account_id'); }
}
