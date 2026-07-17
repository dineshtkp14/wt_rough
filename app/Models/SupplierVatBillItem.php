<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierVatBillItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_name',
        'quantity',
        'unit',
        'rate',
        'amount',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function bill()
    {
        return $this->belongsTo(SupplierVatBill::class, 'supplier_vat_bill_id');
    }
}
