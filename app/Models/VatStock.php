<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class VatStock extends Model
{
    protected $fillable = ['firm_id','item_name','unit','quantity','purchase_rate','sale_rate','reorder_level','notes'];
    protected $casts = ['quantity'=>'decimal:3','purchase_rate'=>'decimal:2','sale_rate'=>'decimal:2','reorder_level'=>'decimal:3'];
    public function movements() { return $this->hasMany(VatStockMovement::class); }
    public function firm() { return $this->belongsTo(VatFirm::class, 'firm_id'); }
}
