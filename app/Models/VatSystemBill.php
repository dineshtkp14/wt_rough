<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VatSystemBill extends Model
{
    protected $table = 'vat_system_bills';
    protected $fillable = ['customer_id', 'extra_customer_id', 'firm_id', 'seller_name', 'seller_vat_no', 'seller_pan_no', 'seller_phone', 'seller_address', 'seller_email', 'bill_no', 'bill_date', 'discount', 'notes', 'added_by'];
    protected $casts = ['bill_date' => 'date', 'discount' => 'decimal:2'];
    public function customer() { return $this->belongsTo(VatCustomer::class, 'customer_id'); }
    public function extraCustomer() { return $this->belongsTo(VatExtraCustomer::class, 'extra_customer_id'); }
    public function getPartyAttribute() { return $this->customer; }
    public function getCustomerAttribute($value)
    {
        if ($this->extra_customer_id) {
            return $this->relations['extraCustomer'] ?? $this->extraCustomer()->first();
        }
        return $this->relations['customer'] ?? $this->customer()->first();
    }
    public function firm() { return $this->belongsTo(VatFirm::class, 'firm_id'); }
    public function items() { return $this->hasMany(VatSystemBillItem::class, 'vat_system_bill_id'); }
}
