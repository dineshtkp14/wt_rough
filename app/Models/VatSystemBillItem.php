<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VatSystemBillItem extends Model
{
    protected $table = 'vat_system_bill_items';
    protected $fillable = ['item_name', 'hs_code', 'unit', 'quantity', 'rate', 'is_taxable'];
    protected $casts = ['quantity' => 'decimal:3', 'rate' => 'decimal:2', 'is_taxable' => 'boolean'];
}
