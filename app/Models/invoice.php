<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\FiscalNumber;

class invoice extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $invoice) {
            if (!$invoice->fiscal_invoice_no) {
                $fiscalYear = FiscalNumber::fiscalYearForDate($invoice->inv_date ?: now());
                $last = static::where('fiscal_invoice_no', 'like', $fiscalYear . '-%')
                    ->orderByDesc('fiscal_invoice_no')
                    ->value('fiscal_invoice_no');
                $sequence = $last ? ((int) substr($last, strrpos($last, '-') + 1) + 1) : 1;
                $invoice->fiscal_invoice_no = FiscalNumber::format($fiscalYear, $sequence);
            }
        });
    }

    public function getDisplayInvoiceNoAttribute(): string
    {
        return $this->fiscal_invoice_no ?: (string) $this->id;
    }

    public function getVisibleInvoiceNoAttribute(): string
    {
        return auth()->check() && auth()->user()->isAdmin()
            ? (string) $this->id
            : $this->display_invoice_no;
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
