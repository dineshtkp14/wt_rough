@extends('layouts.master')

@section('content')
<div class="main-content"><div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><h1 class="fw-bold mb-1">VAT Customers</h1><p class="text-muted mb-0">Customers used only for VAT bills.</p></div>
        <div class="d-flex gap-2"><a href="{{ route('vat-system.index') }}" class="btn btn-outline-secondary">Back</a><a href="{{ route('vat-system.customers.create') }}" class="btn btn-primary"><i class="fa fa-plus me-1"></i>Add Customer</a></div>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-warning">{{ session('error') }}</div>@endif
    <form class="mb-3" method="get" id="vatCustomerSearchForm"><div class="row g-2"><div class="col-md-8"><input name="search" value="{{ $search }}" class="form-control" id="vatCustomerSearch" placeholder="Search by name, PAN, address or phone"></div><div class="col-md-4"><select name="type" id="vatCustomerType" class="form-select"><option value="">All types</option><option value="shop" @selected($type === 'shop')>Shop</option><option value="customer" @selected($type === 'customer')>Customer</option></select></div></div></form>
    <div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-dark"><tr><th>#</th><th>Name</th><th>Type</th><th>Address</th><th>PAN/VAT No.</th><th>Phone</th><th class="text-end">Actions</th></tr></thead><tbody id="vatCustomerRows">
        @forelse($customers as $customer)
            <tr><td>{{ $customers->firstItem() + $loop->index }}</td><td class="fw-bold">{{ $customer->name }}</td><td><span class="badge text-bg-{{ $customer->customer_type === 'shop' ? 'primary' : 'secondary' }}">{{ ucfirst($customer->customer_type ?: 'customer') }}</span></td><td>{{ $customer->address ?: '-' }}</td><td>{{ $customer->pan_no ?: '-' }}</td><td>{{ $customer->phone ?: '-' }}</td><td class="text-end"><a href="{{ route('vat-system.customers.edit', $customer) }}" class="btn btn-sm btn-warning"><i class="fa fa-pen"></i></a> <form class="d-inline" method="post" action="{{ route('vat-system.customers.destroy', $customer) }}" onsubmit="return confirm('Delete this VAT customer?');">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button></form></td></tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted py-5">No VAT customers found.</td></tr>
        @endforelse
    </tbody></table></div><div class="p-3">{{ $customers->links() }}</div></div>
</div></div>
<script>document.addEventListener('DOMContentLoaded',function(){const input=document.getElementById('vatCustomerSearch'),type=document.getElementById('vatCustomerType'),form=document.getElementById('vatCustomerSearchForm'),body=document.getElementById('vatCustomerRows');if(!input||!body)return;const esc=v=>String(v??'-').replace(/[&<>'"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[c]));let timer;const load=()=>{const q=input.value.trim();fetch('{{ route('vat-system.customers.index') }}?search='+encodeURIComponent(q)+'&type='+encodeURIComponent(type.value),{headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}}).then(r=>r.json()).then(rows=>{body.innerHTML=rows.length?rows.map((c,i)=>'<tr><td>'+(i+1)+'</td><td class="fw-bold">'+esc(c.name)+'</td><td><span class="badge text-bg-'+(c.customer_type==='shop'?'primary':'secondary')+'">'+esc((c.customer_type||'customer').replace(/^./,x=>x.toUpperCase()))+'</span></td><td>'+esc(c.address)+'</td><td>'+esc(c.pan_no)+'</td><td>'+esc(c.phone)+'</td><td class="text-end"><a href="{{ url('/vat-system/customers') }}/'+c.id+'/edit" class="btn btn-sm btn-warning"><i class="fa fa-pen"></i></a></td></tr>').join(''):'<tr><td colspan="7" class="text-center text-muted py-5">No VAT customers found.</td></tr>'})};input.addEventListener('input',()=>{clearTimeout(timer);timer=setTimeout(load,250)});type.addEventListener('change',load);form.addEventListener('submit',e=>{e.preventDefault();load()})});</script>
@endsection
