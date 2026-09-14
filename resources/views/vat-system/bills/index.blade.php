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
        <div><h1 class="fw-bold mb-1">Saved Sales Invoices</h1><p class="text-muted mb-0">{{ $firm->name }} — view and manage this firm’s sales invoices.</p></div>
        <div class="d-flex gap-2"><a href="{{ route('vat-system.firm.switch', ['next' => 'sales']) }}" class="btn btn-outline-primary"><i class="fa fa-repeat me-1"></i>Change Firm</a><a href="{{ route('vat-system.index') }}" class="btn btn-outline-secondary"><i class="fa fa-arrow-left me-1"></i>Back</a><a href="{{ route('vat-system.create') }}" class="btn btn-primary"><i class="fa fa-plus me-1"></i>Create VAT Bill</a></div>
    </div>
    @if($customer)<div class="alert alert-info py-2"><i class="fa fa-filter me-1"></i>Showing invoices for <strong>{{ $customer->name }}</strong> only. <a href="{{ route('vat-system.bills.index') }}" class="ms-2">Show all {{ $firm->name }} invoices</a></div>@endif
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="invoice-search"><i class="fa fa-search"></i><input id="vatBillSearch" type="search" value="{{ $search ?? '' }}" placeholder="Search by bill no., customer, PAN, firm or payment mode..." autocomplete="off"><button type="button" id="clearVatBillSearch" aria-label="Clear search">&times;</button><span id="vatBillSearchStatus"></span></div>
    <div class="card overflow-hidden"><div class="card-header d-flex justify-content-between align-items-center"><span><i class="fa fa-file-invoice me-2"></i>All VAT Bills</span><span class="badge bg-light text-dark">{{ $bills->total() }} bills</span></div>
        @if($bills->count())
        <div class="table-responsive"><table class="table table-hover mb-0 align-middle"><thead><tr><th>Bill No.</th><th>Bill Date</th><th>Firm</th><th>Customer</th><th>Payment Mode</th><th class="text-end">Grand Total</th><th>Created By</th><th class="text-end">Action</th></tr></thead><tbody>
        @foreach($bills as $bill)
        @php $total=$bill->items->sum(fn($item)=>(float)$item->quantity*(float)$item->rate); $taxable=$bill->items->where('is_taxable',true)->sum(fn($item)=>(float)$item->quantity*(float)$item->rate); $grand=$total-(float)$bill->discount+round($taxable*.13,2); @endphp
        <tr><td class="bill-number">#{{ $bill->bill_no }}</td><td>{{ $bill->bill_date->format('Y-m-d') }}</td><td><strong>{{ $bill->firm->name ?? $bill->seller_name }}</strong><br><small class="text-muted">PAN: {{ $bill->seller_pan_no ?: '-' }}</small></td><td><strong>{{ $bill->customer->name ?? '-' }}</strong><br><small class="text-muted">{{ $bill->customer->pan_no ?? '' }}</small></td><td>{{ $bill->payment_mode ?: '-' }}</td><td class="text-end fw-bold">{{ number_format($grand,2) }}</td><td>{{ $bill->added_by ?: '-' }}</td><td class="text-end text-nowrap"><a href="{{ route('vat-system.bills.show', $bill) }}" class="btn btn-info btn-sm text-white"><i class="fa fa-eye me-1"></i>View</a> <a href="{{ route('vat-system.bills.edit', $bill) }}" class="btn btn-warning btn-sm"><i class="fa fa-edit me-1"></i>Edit</a><form method="post" action="{{ route('vat-system.bills.destroy', $bill) }}" class="d-inline" onsubmit="return confirm('Delete this VAT bill?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa fa-trash me-1"></i>Delete</button></form></td></tr>
        @endforeach
        </tbody></table></div><div class="p-3">{{ $bills->links() }}</div>
        @else
        <div class="empty text-center"><i class="fa fa-file-invoice fa-3x mb-3"></i><h4>No VAT bills saved yet</h4><a href="{{ route('vat-system.create') }}" class="btn btn-primary">Create First VAT Bill</a></div>
        @endif
    </div>
</div></div>
<style>
.invoice-search{display:flex;align-items:center;gap:10px;background:#fff;border:1px solid #cbd9eb;border-radius:12px;padding:8px 14px;margin-bottom:16px;box-shadow:0 5px 16px rgba(23,59,114,.08)}.invoice-search>i{color:#1769ff;font-size:17px}.invoice-search input{flex:1;border:0;outline:0;color:#173b72;font-size:16px;padding:8px 2px}.invoice-search input::placeholder{color:#8292aa}.invoice-search:focus-within{border-color:#1769ff;box-shadow:0 0 0 3px rgba(23,105,255,.14)}.invoice-search button{display:none;border:0;background:#eaf1ff;color:#1769ff;border-radius:50%;width:28px;height:28px;font-size:22px;line-height:20px}.invoice-search button.show{display:block}.invoice-search span{font-size:12px;color:#64748b;white-space:nowrap}.vat-bills-loading{opacity:.45;pointer-events:none}
</style>
<script>
document.addEventListener('DOMContentLoaded',function(){const input=document.getElementById('vatBillSearch'),clear=document.getElementById('clearVatBillSearch'),status=document.getElementById('vatBillSearchStatus'),table=document.querySelector('.vat-list table'),body=table?.querySelector('tbody'),card=table?.closest('.card');if(!input||!body||!card)return;let timer,controller;const esc=v=>String(v??'').replace(/[&<>'"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[c]));const token='{{ csrf_token() }}';const render=items=>{body.innerHTML=items.length?items.map(b=>{const total=Number(b.total)||0,taxable=Number(b.taxable)||0,grand=total-Number(b.discount||0)+Math.round(taxable*.13*100)/100;return `<tr><td class="bill-number">#${esc(b.bill_no)}</td><td>${esc(b.date)}</td><td><strong>${esc(b.firm)}</strong><br><small class="text-muted">PAN: ${esc(b.pan)}</small></td><td><strong>${esc(b.customer)}</strong><br><small class="text-muted">${esc(b.customer_pan)}</small></td><td>${esc(b.payment_mode)}</td><td class="text-end fw-bold">${grand.toFixed(2)}</td><td>${esc(b.created_by)}</td><td class="text-end text-nowrap"><a href="${esc(b.show_url)}" class="btn btn-info btn-sm text-white"><i class="fa fa-eye me-1"></i>View</a> <a href="${esc(b.edit_url)}" class="btn btn-warning btn-sm"><i class="fa fa-edit me-1"></i>Edit</a><form method="post" action="${esc(b.delete_url)}" class="d-inline" onsubmit="return confirm('Delete this VAT bill?')"><input type="hidden" name="_token" value="${token}"><input type="hidden" name="_method" value="DELETE"><button class="btn btn-danger btn-sm"><i class="fa fa-trash me-1"></i>Delete</button></form></td></tr>`}).join(''):`<tr><td colspan="8" class="empty text-center">No VAT bills match your search.</td></tr>`};const search=()=>{const q=input.value.trim();clear.classList.toggle('show',!!q);if(controller)controller.abort();if(!q){location.href=location.pathname;return}controller=new AbortController();card.classList.add('vat-bills-loading');status.textContent='Searching...';fetch(`${location.pathname}?search=${encodeURIComponent(q)}{{ $customer ? '&customer_id='.$customer->id : '' }}`,{headers:{Accept:'application/json'},signal:controller.signal}).then(r=>r.json()).then(d=>{render(d.items||[]);status.textContent=(d.total||0)+' result'+((d.total||0)===1?'':'s')}).catch(e=>{if(e.name!=='AbortError')status.textContent='Search failed'}).finally(()=>card.classList.remove('vat-bills-loading'))};input.addEventListener('input',()=>{clearTimeout(timer);timer=setTimeout(search,250)});clear.addEventListener('click',()=>{input.value='';search();input.focus()})});
</script>
@endsection
