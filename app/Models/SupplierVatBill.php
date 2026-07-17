<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierVatBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'firm_type',
        'bill_no',
        'bill_date',
        'taxable_amount',
        'vat_rate',
        'vat_amount',
        'total_amount',
        'notes',
        'added_by',
    ];

    protected $casts = [
        'bill_date' => 'date',
        'taxable_amount' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(company::class, 'company_id');
    }

    public function items()
    {
        return $this->hasMany(SupplierVatBillItem::class);
    }
}
