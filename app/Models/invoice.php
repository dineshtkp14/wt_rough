<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use App\Support\FiscalNumber;

class invoice extends Model
{
    use HasFactory;

    public function getFiscalDisplayInvoiceNoAttribute(): string
    {
        if (!empty($this->fiscal_invoice_no)) {
            return (string) $this->fiscal_invoice_no;
        }

        // Post-cutover invoices must have a persisted fiscal number. Do not
        // derive one from date ordering: that fallback can display a number
        // already owned by another invoice after a failed save.
        $invoiceDate = $this->inv_date ?: $this->created_at;
        if (FiscalNumber::isAfterDisplayCutover($invoiceDate)) {
            return (string) $this->id;
        }

        $fiscalYear = FiscalNumber::fiscalYearForDate($invoiceDate);
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

        // A backdated invoice must not reuse a number already assigned to a
        // later invoice. Allocate after the highest stored sequence instead
        // of deriving it from date ordering.
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $prefix = $fiscalYear . '-';
            $nextSequence = static::query()
                ->whereNotNull('fiscal_invoice_no')
                ->pluck('fiscal_invoice_no')
                ->map(function ($number) use ($prefix) {
                    return str_starts_with((string) $number, $prefix)
                        ? (int) substr((string) $number, strlen($prefix))
                        : 0;
                })
                ->max() + 1;

            $this->fiscal_invoice_no = FiscalNumber::format($fiscalYear, $nextSequence);

            try {
                $this->saveQuietly();
                return;
            } catch (QueryException $exception) {
                // A concurrent request may have claimed this sequence. Retry
                // after re-reading the current maximum.
                if ((int) ($exception->errorInfo[1] ?? 0) !== 1062) {
                    throw $exception;
                }
            }
        }

        throw new \RuntimeException('Unable to allocate a unique fiscal invoice number.');
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
