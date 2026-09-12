<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VatFirm extends Model
{
    protected $fillable = ['name', 'pan_no', 'address', 'phone', 'email', 'is_active'];

    public function bills()
    {
        return $this->hasMany(VatSystemBill::class, 'firm_id');
    }
}
