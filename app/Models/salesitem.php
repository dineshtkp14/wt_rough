<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class salesitem extends Model
{
    use HasFactory;

    public function product()
    {
        return $this->belongsTo(Item::class, 'itemid');
    }
}
