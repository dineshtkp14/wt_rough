<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\FiscalNumber;

class invoice extends Model
{
    use HasFactory;

    public function getFiscalDisplayInvoiceNoAttribute(): string
    {
        if (!FiscalNumber::isAfterDisplayCutover($this->created_at)) {
            return (string) $this->id;
        }

        $sequence = static::where('created_at', '>=', FiscalNumber::DISPLAY_CUTOVER_AT)
            ->where('id', '<=', $this->id)
            ->count();

        return FiscalNumber::format(
            FiscalNumber::fiscalYearForDate($this->inv_date ?: $this->created_at),
            $sequence
        );
    }

    public function getVisibleInvoiceNoAttribute(): string
    {
        if (!FiscalNumber::shouldShowFiscalToCurrentUser()) {
            return (string) $this->id;
        }

        return $this->fiscal_display_invoice_no;
    }

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
