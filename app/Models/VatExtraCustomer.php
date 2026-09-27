<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VatExtraCustomer extends Model
{
    protected $table = 'vat_extra_customers';

    protected $fillable = ['firm_id', 'name', 'address', 'contact_name', 'notes', 'added_by'];

    public function firm() { return $this->belongsTo(VatFirm::class, 'firm_id'); }
    public function bills() { return $this->hasMany(VatSystemBill::class, 'extra_customer_id'); }
    public function getPanNoAttribute() { return null; }
    public function getPhoneAttribute() { return $this->contact_name; }
}
