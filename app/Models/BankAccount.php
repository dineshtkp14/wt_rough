<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = ['name', 'bank_name', 'account_number', 'opening_balance', 'active'];
    protected $casts = ['opening_balance' => 'decimal:2', 'active' => 'boolean'];

    public function transactions() { return $this->hasMany(BankStatementTransaction::class); }
    public function adjustments() { return $this->hasMany(BankReconciliationAdjustment::class); }
}
