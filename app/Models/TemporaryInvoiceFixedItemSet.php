<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryInvoiceFixedItemSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
    ];

    public function items()
    {
        return $this->hasMany(TemporaryInvoiceFixedItemSetItem::class)
            ->orderBy('sort_order');
    }
}
