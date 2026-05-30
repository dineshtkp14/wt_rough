<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryInvoiceFixedItemSetItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'temporary_invoice_fixed_item_set_id',
        'item_name',
        'quantity',
        'unit',
        'price',
        'sort_order',
    ];

    public function fixedItemSet()
    {
        return $this->belongsTo(TemporaryInvoiceFixedItemSet::class, 'temporary_invoice_fixed_item_set_id');
    }
}
