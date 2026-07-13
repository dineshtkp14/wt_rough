<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invoice extends Model
{
    use HasFactory;

    public function customer()
    {
        return $this->belongsTo(customerinfo::class, 'customerid');
    }

    public function salesitems()
    {
        return $this->hasMany(salesitem::class, 'invoiceid');
    }

    public function smsLogs()
    {
        return $this->hasMany(SmsLog::class);
    }

    public function vatBill()
    {
        return $this->hasOne(VatBill::class, 'invoice_id');
    }

    /**
     * Get customer's total due amount
     */
    public function getCustomerTotalDueAmount()
    {
        if (!$this->customerid) {
            return 0;
        }

        $customer = customerinfo::find($this->customerid);
        if (!$customer) {
            return 0;
        }

        // Calculate total due from all invoices for this customer
        $totalDue = self::where('customerid', $this->customerid)
            ->where('inv_type', 'credit')
            ->where('created_at', '<=', now())
            ->sum('total');

        return $totalDue;
    }
}
