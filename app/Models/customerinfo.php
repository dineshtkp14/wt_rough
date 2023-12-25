<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customerinfo extends Model
{
    use HasFactory;
    //added after
    public function customerledgerdetails()
    {
        return $this->hasMany(customerledgerdetails::class, 'customerid', 'id');
    }
}
