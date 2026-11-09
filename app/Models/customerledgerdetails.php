<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\FiscalNumber;

class customerledgerdetails extends Model
{
    use HasFactory;

    public function getFiscalDisplayReceiptNoAttribute(): string
    {
        // Migrations and newly created receipts use the persisted fiscal number
        // whenever it is available. This prevents a receipt from changing its
        // customer when rows are inserted later.
        if (!empty($this->fiscal_receipt_no)) {
            return (string) $this->fiscal_receipt_no;
        }

        // Match the migration sequence: payment date first, then database id.
        // The previous id-only calculation produced incorrect receipt matches.
        $receiptDate = $this->date ?: $this->created_at;
        $fiscalYear = FiscalNumber::fiscalYearForDate($receiptDate);
        $sequence = static::where('invoicetype', 'payment')
            ->orderBy('date')
            ->orderBy('id')
            ->get(['id', 'date', 'created_at'])
            ->filter(function ($receipt) use ($fiscalYear) {
                $date = $receipt->date ?: $receipt->created_at;
                return FiscalNumber::fiscalYearForDate($date) === $fiscalYear;
            })
            ->values()
            ->search(fn ($receipt) => (int) $receipt->id === (int) $this->id);

        if ($sequence === false) {
            return (string) $this->id;
        }

        $sequence = $sequence + 1;

        return FiscalNumber::format(
            $fiscalYear,
            $sequence,
            'CR'
        );
    }

    public static function nextFiscalReceiptNumber(string $fiscalYear): string
    {
        $prefix = 'CR-' . $fiscalYear . '-';

        $highestSequence = static::where('invoicetype', 'payment')
            ->where('fiscal_receipt_no', 'like', $prefix . '%')
            ->pluck('fiscal_receipt_no')
            ->map(function ($receiptNo) use ($prefix) {
                $sequence = substr((string) $receiptNo, strlen($prefix));
                return ctype_digit($sequence) ? (int) $sequence : 0;
            })
            ->max();

        return FiscalNumber::format($fiscalYear, ((int) $highestSequence) + 1, 'CR');
    }

    public function assignFiscalReceiptNumber(): void
    {
        if ($this->invoicetype !== 'payment') {
            return;
        }

        $fiscalYear = FiscalNumber::fiscalYearForDate($this->date ?: $this->created_at);
        $this->fiscal_receipt_no = static::nextFiscalReceiptNumber($fiscalYear);
        $this->saveQuietly();
    }

    public function getDisplayReceiptNoAttribute(): string
    {
        if (!FiscalNumber::shouldShowFiscalToCurrentUser()) {
            return (string) $this->id;
        }

        return $this->fiscal_display_receipt_no;
    }
        //added after
    public function customerinfo()
    {
        return $this->belongsTo(customerinfo::class, 'customerid', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(customerinfo::class, 'customerid');
    }

    public function invoice()
    {
        return $this->belongsTo(invoice::class, 'invoiceid');
    }
}
