<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankReconciliationAdjustment extends Model
{
    protected $fillable = ['bank_account_id', 'adjustment_date', 'description', 'amount'];
    protected $casts = ['adjustment_date' => 'date', 'amount' => 'decimal:2'];
}
