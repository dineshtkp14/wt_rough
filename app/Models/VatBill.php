<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VatBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'date',
        'bill_no',
        'amount_without_tax',
        'firm_type',
        'added_by',
    ];

    protected $casts = [
        'date' => 'date',
        'amount_without_tax' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(invoice::class, 'invoice_id');
    }
}
