<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class VatStockMovement extends Model
{
    protected $fillable = ['vat_stock_id','movement_type','quantity','rate','reference','notes'];
    protected $casts = ['quantity'=>'decimal:3','rate'=>'decimal:2'];
    public function stock() { return $this->belongsTo(VatStock::class, 'vat_stock_id'); }
}
