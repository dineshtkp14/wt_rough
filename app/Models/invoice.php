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
        if (!empty($this->fiscal_invoice_no)) {
            return (string) $this->fiscal_invoice_no;
        }

        $fiscalYear = FiscalNumber::fiscalYearForDate($this->inv_date ?: $this->created_at);
        $sequence = static::orderBy('inv_date')
            ->orderBy('id')
            ->get(['id', 'inv_date', 'created_at'])
            ->filter(function ($invoice) use ($fiscalYear) {
                $date = $invoice->inv_date ?: $invoice->created_at;
                return FiscalNumber::fiscalYearForDate($date) === $fiscalYear;
            })
            ->values()
            ->search(fn ($invoice) => (int) $invoice->id === (int) $this->id);

        if ($sequence === false) {
            return (string) $this->id;
        }

        $sequence++;

        return FiscalNumber::format(
            $fiscalYear,
            $sequence
        );
    }

    public function assignFiscalInvoiceNumber(): void
    {
        $fiscalYear = FiscalNumber::fiscalYearForDate($this->inv_date ?: $this->created_at);
        $sequence = static::orderBy('inv_date')
            ->orderBy('id')
            ->get(['id', 'inv_date', 'created_at'])
            ->filter(function ($invoice) use ($fiscalYear) {
                $date = $invoice->inv_date ?: $invoice->created_at;
                return FiscalNumber::fiscalYearForDate($date) === $fiscalYear;
            })
            ->values()
            ->search(fn ($invoice) => (int) $invoice->id === (int) $this->id);

        if ($sequence === false) {
            return;
        }

        $this->fiscal_invoice_no = FiscalNumber::format($fiscalYear, $sequence + 1);
        $this->saveQuietly();
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
