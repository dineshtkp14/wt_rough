@extends('layouts.master')

@section('content')
<style>
.vat-list{background:#eef3f9;min-height:calc(100vh - 70px)}
.vat-list .card{border:0;border-radius:14px;box-shadow:0 8px 24px #18365d12}
.vat-list .card-header{background:#173b72;color:#fff;font-weight:800}
.vat-list thead th{background:#3549d3;color:#fff;border:0;white-space:nowrap}
.vat-list tbody td{vertical-align:middle;color:#173b72}
.vat-list .bill-number{font-weight:800;color:#172554}
.vat-list .empty{padding:60px 20px;color:#6b7b93}
</style>
<div class="main-content vat-list"><div class="container-fluid p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h1 class="fw-bold mb-1">Saved Sales Invoices</h1><p class="text-muted mb-0">View and manage all generated sales invoices.</p></div>
        <div class="d-flex gap-2"><a href="{{ route('vat-system.index') }}" class="btn btn-outline-secondary"><i class="fa fa-arrow-left me-1"></i>Back</a><a href="{{ route('vat-system.create') }}" class="btn btn-primary"><i class="fa fa-plus me-1"></i>Create VAT Bill</a></div>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card overflow-hidden"><div class="card-header d-flex justify-content-between align-items-center"><span><i class="fa fa-file-invoice me-2"></i>All VAT Bills</span><span class="badge bg-light text-dark">{{ $bills->total() }} bills</span></div>
        @if($bills->count())
        <div class="table-responsive"><table class="table table-hover mb-0 align-middle"><thead><tr><th>Bill No.</th><th>Bill Date</th><th>Customer</th><th>Payment Mode</th><th class="text-end">Grand Total</th><th>Created By</th><th class="text-end">Action</th></tr></thead><tbody>
        @foreach($bills as $bill)
        @php $total=$bill->items->sum(fn($item)=>(float)$item->quantity*(float)$item->rate); $taxable=$bill->items->where('is_taxable',true)->sum(fn($item)=>(float)$item->quantity*(float)$item->rate); $grand=$total-(float)$bill->discount+round($taxable*.13,2); @endphp
        <tr><td class="bill-number">#{{ $bill->bill_no }}</td><td>{{ $bill->bill_date->format('Y-m-d') }}</td><td><strong>{{ $bill->customer->name ?? '-' }}</strong><br><small class="text-muted">{{ $bill->customer->pan_no ?? '' }}</small></td><td>{{ $bill->payment_mode ?: '-' }}</td><td class="text-end fw-bold">{{ number_format($grand,2) }}</td><td>{{ $bill->added_by ?: '-' }}</td><td class="text-end text-nowrap"><a href="{{ route('vat-system.bills.show', $bill) }}" class="btn btn-info btn-sm text-white"><i class="fa fa-eye me-1"></i>View</a> <a href="{{ route('vat-system.bills.edit', $bill) }}" class="btn btn-warning btn-sm"><i class="fa fa-edit me-1"></i>Edit</a><form method="post" action="{{ route('vat-system.bills.destroy', $bill) }}" class="d-inline" onsubmit="return confirm('Delete this VAT bill?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa fa-trash me-1"></i>Delete</button></form></td></tr>
        @endforeach
        </tbody></table></div><div class="p-3">{{ $bills->links() }}</div>
        @else
        <div class="empty text-center"><i class="fa fa-file-invoice fa-3x mb-3"></i><h4>No VAT bills saved yet</h4><a href="{{ route('vat-system.create') }}" class="btn btn-primary">Create First VAT Bill</a></div>
        @endif
    </div>
</div></div>
@endsection
