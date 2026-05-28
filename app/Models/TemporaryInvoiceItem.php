<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'temporary_invoice_id',
        'item_name',
        'unstocked_item',
        'quantity',
        'unit',
        'price',
        'subtotal',
    ];

    public function temporaryInvoice()
    {
        return $this->belongsTo(TemporaryInvoice::class);
    }
}
