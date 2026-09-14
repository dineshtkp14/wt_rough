<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VatCustomer extends Model
{
    protected $table = 'vat_customers';

    protected $fillable = [
        'firm_id', 'name', 'address', 'pan_no', 'phone', 'email', 'notes', 'added_by',
    ];

    public function salesInvoices() { return $this->hasMany(VatSystemBill::class, 'customer_id'); }
    public function firm() { return $this->belongsTo(VatFirm::class); }
}
