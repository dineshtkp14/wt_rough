<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VatSupplier extends Model
{
    protected $fillable = ['firm_id', 'name', 'vat_no', 'pan_no', 'phone', 'address'];

    public function firm()
    {
        return $this->belongsTo(VatFirm::class);
    }
}
