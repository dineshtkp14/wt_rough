<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\FiscalNumber;

class customerledgerdetails extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $ledger) {
            if ($ledger->invoicetype === 'payment' && !$ledger->fiscal_receipt_no) {
                $fiscalYear = FiscalNumber::fiscalYearForDate($ledger->date ?: now());
                $last = static::where('invoicetype', 'payment')
                    ->where('fiscal_receipt_no', 'like', 'CR-' . $fiscalYear . '-%')
                    ->orderByDesc('fiscal_receipt_no')
                    ->value('fiscal_receipt_no');
                $sequence = $last ? ((int) substr($last, strrpos($last, '-') + 1) + 1) : 1;
                $ledger->fiscal_receipt_no = FiscalNumber::format($fiscalYear, $sequence, 'CR');
            }
        });
    }

    public function getDisplayReceiptNoAttribute(): string
    {
        return $this->fiscal_receipt_no ?: (string) $this->id;
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
}
