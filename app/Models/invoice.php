<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invoice extends Model
{
    use HasFactory;

    public function customer()
    {
        return $this->belongsTo(customerinfo::class, 'customerid');
    }

    public function salesitems()
    {
        return $this->hasMany(salesitem::class, 'invoiceid');
    }
}
