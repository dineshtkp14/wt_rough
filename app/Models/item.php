<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class item extends Model
{
    use HasFactory;

    protected $table = 'items'; // Specify the actual table name if it's different

    // protected $fillable=[
    //     'quantity'
    // ];


    public function company()
    {
        return $this->belongsTo(company::class, 'companyid');
    }
}
