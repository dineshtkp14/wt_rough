<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_date',
        'customer_name',
        'customer_address',
        'contact_number',
        'subtotal',
        'discount',
        'total',
        'notes',
        'invoice_number',
        'added_by',
    ];

    public function items()
    {
        return $this->hasMany(TemporaryInvoiceItem::class);
    }

    public function getRouteKeyName()
    {
        return 'invoice_number';
    }
}
