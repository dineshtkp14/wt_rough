<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\BankReconciliationAdjustment;
use App\Models\BankStatementTransaction;
use App\Models\customerledgerdetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BankReconciliationController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::check(), 403);
        $accounts = BankAccount::where('active', true)->orderBy('name')->get();
        $account = $accounts->firstWhere('id', (int) $request->account_id) ?: $accounts->first();
        $transactions = $account ? $account->transactions()->orderByDesc('transaction_date')->orderByDesc('id')->paginate(30)->withQueryString() : collect();
        $summary = $account ? [
            'book' => (float) $account->opening_balance + (float) Bank::sum('amount') + (float) customerledgerdetails::where('invoicetype', 'payment')->sum('credit') - (float) DB::table('expenses')->sum('amount'),
            'statement' => (float) $account->opening_balance + (float) $account->transactions()->sum('amount'),
            'unmatched' => $account->transactions()->where('status', 'unmatched')->count(),
        ] : ['book' => 0, 'statement' => 0, 'unmatched' => 0];
        return view('bank-reconciliation.index', compact('accounts', 'account', 'transactions', 'summary'));
    }

    public function storeAccount(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:120', 'bank_name' => 'nullable|string|max:120', 'account_number' => 'nullable|string|max:80', 'opening_balance' => 'nullable|numeric']);
        BankAccount::create($data);
        return back()->with('success', 'Bank account added.');
    }

    public function import(Request $request)
    {
        $data = $request->validate(['bank_account_id' => 'required|exists:bank_accounts,id', 'statement' => 'required|file|mimes:csv,txt|max:5120']);
        $handle = fopen($request->file('statement')->getRealPath(), 'r');
        $headers = array_map(fn ($v) => strtolower(trim((string) $v)), fgetcsv($handle));
        $dateKey = $this->header($headers, ['date', 'transaction date', 'value date']);
        $descKey = $this->header($headers, ['description', 'details', 'particulars', 'narration']);
        $refKey = $this->header($headers, ['reference', 'reference no', 'ref']);
        $amountKey = $this->header($headers, ['amount', 'transaction amount']);
        $debitKey = $this->header($headers, ['debit', 'withdrawal', 'withdrawals']);
        $creditKey = $this->header($headers, ['credit', 'deposit', 'deposits']);
        if ($dateKey === null || ($amountKey === null && $debitKey === null && $creditKey === null)) return back()->with('error', 'CSV needs a Date and Amount, or Date, Debit and Credit columns.');
        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (!array_filter($row, fn ($v) => trim((string) $v) !== '')) continue;
            $amount = $amountKey !== null ? $this->number($row[$amountKey] ?? 0) : $this->number($row[$creditKey] ?? 0) - $this->number($row[$debitKey] ?? 0);
            if (!$amount || !($date = $this->date($row[$dateKey] ?? ''))) continue;
            $transaction = BankStatementTransaction::create(['bank_account_id' => $data['bank_account_id'], 'transaction_date' => $date, 'description' => $descKey === null ? null : ($row[$descKey] ?? null), 'reference' => $refKey === null ? null : ($row[$refKey] ?? null), 'amount' => $amount, 'running_balance' => null, 'imported_by' => Auth::id()]);
            $this->autoMatch($transaction);
            $count++;
        }
        fclose($handle);
        return back()->with('success', "$count statement transaction(s) imported.");
    }

    public function match(Request $request, BankStatementTransaction $transaction)
    {
        $data = $request->validate(['matched_type' => 'required|in:customer_payment,bank_deposit,expense,adjustment', 'matched_id' => 'nullable|integer']);
        $transaction->update(['status' => 'matched', 'matched_type' => $data['matched_type'], 'matched_id' => $data['matched_id'] ?? null]);
        return back()->with('success', 'Transaction marked as matched.');
    }

    public function unmatch(BankStatementTransaction $transaction)
    {
        $transaction->update(['status' => 'unmatched', 'matched_type' => null, 'matched_id' => null]);
        return back()->with('success', 'Transaction returned to unmatched.');
    }

    public function adjustment(Request $request)
    {
        $data = $request->validate(['bank_account_id' => 'required|exists:bank_accounts,id', 'adjustment_date' => 'required|date', 'description' => 'required|string|max:255', 'amount' => 'required|numeric']);
        BankReconciliationAdjustment::create($data);
        return back()->with('success', 'Adjustment added.');
    }

    private function header(array $headers, array $names): ?int { foreach ($names as $name) { $key = array_search($name, $headers, true); if ($key !== false) return $key; } return null; }
    private function number($value): float { return (float) str_replace([',', 'Rs', 'NPR', '$', ' '], '', (string) $value); }
    private function date($value): ?string { try { return \Carbon\Carbon::parse($value)->format('Y-m-d'); } catch (\Throwable $e) { return null; } }

    private function autoMatch(BankStatementTransaction $transaction): void
    {
        $amount = (float) $transaction->amount;
        if ($amount > 0) {
            $payment = customerledgerdetails::where('invoicetype', 'payment')->whereDate('date', $transaction->transaction_date)->whereRaw('ABS(credit - ?) < 0.01', [$amount])->whereNotExists(function ($q) { $q->select(DB::raw(1))->from('bank_statement_transactions as bst')->where('bst.matched_type', 'customer_payment')->whereColumn('bst.matched_id', 'customerledgerdetails.id')->where('bst.status', 'matched'); })->first();
            if ($payment) { $transaction->update(['status' => 'matched', 'matched_type' => 'customer_payment', 'matched_id' => $payment->id]); return; }
            $deposit = Bank::whereDate('date', $transaction->transaction_date)->whereRaw('ABS(amount - ?) < 0.01', [$amount])->first();
            if ($deposit) { $transaction->update(['status' => 'matched', 'matched_type' => 'bank_deposit', 'matched_id' => $deposit->id]); return; }
        } elseif ($amount < 0) {
            $expense = DB::table('expenses')->whereDate('date', $transaction->transaction_date)->whereRaw('ABS(amount - ?) < 0.01', [abs($amount)])->first();
            if ($expense) { $transaction->update(['status' => 'matched', 'matched_type' => 'expense', 'matched_id' => $expense->id]); }
        }
    }
}
