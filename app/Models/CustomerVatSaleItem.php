<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerVatSaleItem extends Model
{
    use HasFactory;

    protected $fillable = ['vat_stock_item_id', 'item_name', 'quantity', 'unit', 'rate', 'amount'];

    protected $casts = [
        'quantity' => 'decimal:3',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(CustomerVatSale::class, 'customer_vat_sale_id');
    }

    public function vatStockItem()
    {
        return $this->belongsTo(VatStockItem::class, 'vat_stock_item_id');
    }
}
