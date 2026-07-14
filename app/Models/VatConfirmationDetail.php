<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VatConfirmationDetail extends Model
{
    protected $fillable = [
        'party_key', 'firm_type', 'from_date', 'to_date',
        'purchase_exempted', 'purchase_taxable',
        'purchase_return_exempted', 'purchase_return_taxable',
        'sales_exempted',
        'sales_return_exempted', 'sales_return_taxable',
        'opening_balance_amount', 'opening_balance_side',
        'closing_balance_amount', 'closing_balance_side', 'added_by',
    ];

    protected $casts = [
        'from_date' => 'date', 'to_date' => 'date',
        'purchase_exempted' => 'decimal:2', 'purchase_taxable' => 'decimal:2',
        'purchase_return_exempted' => 'decimal:2', 'purchase_return_taxable' => 'decimal:2',
        'sales_exempted' => 'decimal:2',
        'sales_return_exempted' => 'decimal:2', 'sales_return_taxable' => 'decimal:2',
        'opening_balance_amount' => 'decimal:2', 'closing_balance_amount' => 'decimal:2',
    ];
}
