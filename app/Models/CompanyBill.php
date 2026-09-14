<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CompanyBill extends Model
{
    protected $fillable = ['firm_id','supplier_id','company_name','company_vat_no','company_pan_no','company_address','company_phone','bill_no','bill_date','payment_mode','discount','notes','added_by'];
    protected $casts = ['bill_date' => 'date', 'discount' => 'decimal:2'];
    public function items() { return $this->hasMany(CompanyBillItem::class); }
    public function supplier() { return $this->belongsTo(VatSupplier::class); }
    public function firm() { return $this->belongsTo(VatFirm::class, 'firm_id'); }
}
