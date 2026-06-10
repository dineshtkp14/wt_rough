<?php

namespace App\Services;

use App\Models\CreditnotesCustomerledgerdetail;
use App\Models\customerledgerdetails;

class CustomerLedgerBalance
{
    public function totalDue(int $customerId): float
    {
        $ledgerRows = customerledgerdetails::where('customerid', $customerId)->get();
        $debitNotCash = (float) $ledgerRows->where('invoicetype', '!=', 'cash')->sum('debit');
        $ledgerCredit = (float) $ledgerRows->sum('credit');
        $creditNoteCredit = (float) $this->creditNoteRowsForLedger($customerId)
            ->sum(fn ($row) => $this->creditNoteAmount($row));

        return round($debitNotCash - $ledgerCredit - $creditNoteCredit, 2);
    }

    public function creditNoteRowsForLedger(int $customerId, ?string $from = null, ?string $to = null)
    {
        $query = CreditnotesCustomerledgerdetail::where('customerid', $customerId);

        if ($from && $to) {
            $query->whereBetween('date', [$from, $to]);
        }

        return $query->get()->reject(function ($row) use ($customerId) {
            return $this->hasExistingCreditNoteLedgerRow($customerId, $row);
        });
    }

    private function hasExistingCreditNoteLedgerRow(int $customerId, $creditNoteRow): bool
    {
        $creditNoteAmount = $this->creditNoteAmount($creditNoteRow);

        return customerledgerdetails::where('customerid', $customerId)
            ->where(function ($query) use ($creditNoteRow, $creditNoteAmount) {
                $query->where('cninvoiceid', $creditNoteRow->invoiceid)
                    ->orWhere('returnidforcreditnotes', $creditNoteRow->invoiceid)
                    ->orWhere(function ($returnQuery) use ($creditNoteRow, $creditNoteAmount) {
                        $returnQuery->where('date', $creditNoteRow->date)
                            ->where(function ($typeQuery) {
                                $typeQuery->where('particulars', 'salesreturn')
                                    ->orWhere('particulars', 'Goods_Return')
                                    ->orWhere('voucher_type', 'return')
                                    ->orWhere('voucher_type', 'Return');
                            })
                            ->whereBetween('credit', [
                                $creditNoteAmount - 0.01,
                                $creditNoteAmount + 0.01,
                            ]);
                    });
            })
            ->exists();
    }

    private function creditNoteAmount($row): float
    {
        $debit = (float) ($row->debit ?? 0);
        $credit = (float) ($row->credit ?? 0);

        return abs($debit) > 0.009 ? $debit : $credit;
    }
}
