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
        if (!FiscalNumber::isAfterDisplayCutover($this->created_at)) {
            return (string) $this->id;
        }

        $sequence = static::where('invoicetype', 'payment')
            ->where('created_at', '>=', FiscalNumber::DISPLAY_CUTOVER_AT)
            ->where('id', '<=', $this->id)
            ->count();

        return FiscalNumber::format(
            FiscalNumber::fiscalYearForDate($this->date ?: $this->created_at),
            $sequence,
            'CR'
        );
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
