<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerVatSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'myfirm_id',
        'customer_id',
        'customer_name',
        'customer_vat_no',
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

    public function firm()
    {
        return $this->belongsTo(Myfirm::class, 'myfirm_id');
    }

    public function customer()
    {
        return $this->belongsTo(customerinfo::class, 'customer_id');
    }

    public function getCustomerDisplayNameAttribute(): string
    {
        return $this->customer_name ?: ($this->customer?->name ?? '');
    }

    public function getCustomerDisplayVatNoAttribute(): ?string
    {
        return $this->customer_vat_no ?: $this->customer?->vat_no;
    }

    public function items()
    {
        return $this->hasMany(CustomerVatSaleItem::class);
    }
}
