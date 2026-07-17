<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VatStockItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'myfirm_id',
        'item_name',
        'unit',
        'quantity',
        'cost_price',
        'sale_rate',
        'warning_quantity',
        'notes',
        'added_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'cost_price' => 'decimal:2',
        'sale_rate' => 'decimal:2',
        'warning_quantity' => 'decimal:3',
    ];

    public function firm()
    {
        return $this->belongsTo(Myfirm::class, 'myfirm_id');
    }

    public function saleLines()
    {
        return $this->hasMany(CustomerVatSaleItem::class, 'vat_stock_item_id');
    }
}
