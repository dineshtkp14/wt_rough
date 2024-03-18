<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customerledgerdetails extends Model
{
    use HasFactory;
        //added after
    public function customerinfo()
    {
        return $this->belongsTo(customerinfo::class, 'customerid', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customerinfo::class, 'customerid');
    }
}
