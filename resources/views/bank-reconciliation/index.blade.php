@extends('layouts.master')

@section('content')
<div class="main-content bank-reconciliation-page">
<div class="container-fluid py-3">
    <h3>Bank Reconciliation</h3>
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

    <div class="card mb-3"><div class="card-body">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-md-5"><label>Bank account</label><select name="account_id" class="form-select" onchange="this.form.submit()"><option value="">Select account</option>@foreach($accounts as $a)<option value="{{ $a->id }}" @selected($account && $account->id == $a->id)>{{ $a->name }}{{ $a->bank_name ? ' - '.$a->bank_name : '' }}</option>@endforeach</select></div>
        </form>
    </div></div>

    <div class="row g-3 mb-3">
        <div class="col-md-4"><div class="card"><div class="card-body"><small>Book balance (estimate)</small><h4>Rs {{ number_format($summary['book'], 2) }}</h4></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><small>Statement balance</small><h4>Rs {{ number_format($summary['statement'], 2) }}</h4></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><small>Unmatched transactions</small><h4>{{ $summary['unmatched'] }}</h4></div></div></div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-6"><div class="card"><div class="card-header">Import bank statement</div><div class="card-body"><form method="post" action="{{ route('bank-reconciliation.import') }}" enctype="multipart/form-data">@csrf<input type="hidden" name="bank_account_id" value="{{ $account?->id }}"><input type="file" name="statement" accept=".csv,.txt" class="form-control mb-2" required><small class="text-muted">CSV columns: Date, Description, Reference, Amount — or Date, Debit, Credit.</small><button class="btn btn-primary mt-2" @disabled(!$account)>Import CSV</button></form></div></div></div>
        <div class="col-lg-6"><div class="card"><div class="card-header">Add bank adjustment</div><div class="card-body"><form method="post" action="{{ route('bank-reconciliation.adjustments.store') }}" class="row g-2">@csrf<input type="hidden" name="bank_account_id" value="{{ $account?->id }}"><div class="col-md-4"><input type="date" name="adjustment_date" value="{{ now()->format('Y-m-d') }}" class="form-control" required></div><div class="col-md-8"><input name="description" placeholder="Bank charge / interest" class="form-control" required></div><div class="col-md-5"><input type="number" step="0.01" name="amount" placeholder="+ money in, - money out" class="form-control" required></div><div class="col-md-7"><button class="btn btn-outline-primary" @disabled(!$account)>Add adjustment</button></div></form></div></div></div>
    </div>

    <div class="card mb-3"><div class="card-header">Add bank account</div><div class="card-body"><form method="post" action="{{ route('bank-reconciliation.accounts.store') }}" class="row g-2">@csrf<div class="col-md-3"><input name="name" placeholder="Account name" class="form-control" required></div><div class="col-md-3"><input name="bank_name" placeholder="Bank name" class="form-control"></div><div class="col-md-3"><input name="account_number" placeholder="Account number" class="form-control"></div><div class="col-md-2"><input type="number" step="0.01" name="opening_balance" placeholder="Opening balance" class="form-control"></div><div class="col-md-1"><button class="btn btn-success">Add</button></div></form></div></div>

    <div class="card"><div class="card-header">Statement transactions</div><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Date</th><th>Description</th><th>Reference</th><th class="text-end">Amount</th><th>Status</th><th>Action</th></tr></thead><tbody>@forelse($transactions as $t)<tr><td>{{ $t->transaction_date->format('Y-m-d') }}</td><td>{{ $t->description }}</td><td>{{ $t->reference }}</td><td class="text-end">Rs {{ number_format($t->amount, 2) }}</td><td><span class="badge {{ $t->status === 'matched' ? 'bg-success' : 'bg-warning text-dark' }}">{{ ucfirst($t->status) }}</span></td><td>@if($t->status === 'unmatched')<form method="post" action="{{ route('bank-reconciliation.match', $t) }}" class="d-flex gap-1">@csrf<select name="matched_type" class="form-select form-select-sm"><option value="customer_payment">Customer payment</option><option value="bank_deposit">Bank deposit</option><option value="expense">Expense</option><option value="adjustment">Adjustment</option></select><input name="matched_id" type="number" min="1" placeholder="ID" class="form-control form-control-sm" style="width:75px"><button class="btn btn-sm btn-primary">Match</button></form>@else<form method="post" action="{{ route('bank-reconciliation.unmatch', $t) }}">@csrf<button class="btn btn-sm btn-outline-secondary">Unmatch</button></form>@endif</td></tr>@empty<tr><td colspan="6" class="text-center py-4">Create an account and import a CSV statement to begin.</td></tr>@endforelse</tbody></table></div><div class="p-2">{{ method_exists($transactions, 'links') ? $transactions->links() : '' }}</div></div>
</div>
</div>
@endsection
