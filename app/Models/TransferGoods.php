<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferGoods extends Model
{
    use HasFactory;
    protected $table = 'transfergoods';

    public function item()
    {
        return $this->belongsTo(Item::class, 'itemid');
    }
}
