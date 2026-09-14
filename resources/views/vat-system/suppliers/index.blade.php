@extends('layouts.master')
@section('content')
<style>
.supplier-page{min-height:calc(100vh - 70px);background:#eef4fb;padding:28px}.supplier-shell{max-width:1200px;margin:auto}.supplier-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}.supplier-head h1{color:#102f5f;font-weight:900;margin:0}.supplier-head p{color:#64748b;margin:5px 0 0}.supplier-box{background:#fff;border-radius:15px;box-shadow:0 10px 28px #1d4e8918;overflow:hidden}.supplier-title{background:#173f7a;color:#fff;padding:17px 22px;font-weight:800;font-size:19px}.supplier-search{margin:18px 22px}.supplier-search input{height:44px;border:1px solid #cbd9eb;border-radius:8px;padding:8px 14px;width:100%}.supplier-table{width:100%;border-collapse:collapse}.supplier-table th{background:#354bd2;color:#fff;text-align:left;padding:13px 15px;font-size:12px;text-transform:uppercase}.supplier-table td{padding:14px 15px;border-bottom:1px solid #e2eaf4;color:#475569}.supplier-table tr:nth-child(even){background:#f8fbff}.supplier-name{font-weight:800;color:#123b76}.supplier-actions{display:flex;gap:7px;white-space:nowrap}.supplier-actions .btn{font-size:12px;font-weight:700}.supplier-empty{text-align:center;padding:55px!important;color:#64748b}@media(max-width:800px){.supplier-page{padding:16px}.supplier-head{align-items:flex-start;gap:12px;flex-direction:column}.supplier-box{overflow-x:auto}.supplier-table{min-width:850px}}
</style>
<div class="main-content supplier-page"><div class="supplier-shell">
<div class="supplier-head"><div><h1>VAT Suppliers</h1><p>Manage suppliers used for purchase bills.</p><span class="badge text-bg-primary mt-2">{{ $firm->name }}</span></div><div class="d-flex gap-2"><a href="{{ route('vat-system.firm.switch', ['next' => 'purchase']) }}" class="btn btn-outline-primary"><i class="fa fa-repeat me-1"></i>Change Firm</a><a href="{{ route('vat-system.suppliers.create') }}" class="btn btn-primary"><i class="fa fa-plus me-1"></i>Add Supplier</a></div></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<section class="supplier-box"><div class="supplier-title"><i class="fa fa-truck me-2"></i>Supplier List</div><form class="supplier-search"><input type="search" name="search" value="{{ $search }}" placeholder="Search by supplier name, PAN, VAT or phone"></form><div class="table-responsive"><table class="supplier-table"><thead><tr><th>#</th><th>Supplier Name</th><th>VAT No.</th><th>PAN No.</th><th>Phone</th><th>Address</th><th>Actions</th></tr></thead><tbody>@forelse($suppliers as $supplier)<tr><td>{{ $suppliers->firstItem() + $loop->index }}</td><td class="supplier-name">{{ $supplier->name }}</td><td>{{ $supplier->vat_no ?: '-' }}</td><td>{{ $supplier->pan_no ?: '-' }}</td><td>{{ $supplier->phone ?: '-' }}</td><td>{{ $supplier->address ?: '-' }}</td><td><div class="supplier-actions"><a href="{{ route('vat-system.suppliers.edit',$supplier) }}" class="btn btn-warning"><i class="fa fa-pen"></i></a><form method="post" action="{{ route('vat-system.suppliers.destroy',$supplier) }}" onsubmit="return confirm('Delete this supplier?')">@csrf @method('DELETE')<button class="btn btn-danger"><i class="fa fa-trash"></i></button></form></div></td></tr>@empty<tr><td colspan="7" class="supplier-empty"><i class="fa fa-truck fa-2x mb-2"></i><br>No suppliers found.</td></tr>@endforelse</tbody></table></div></section>
<div class="mt-3">{{ $suppliers->links() }}</div>
</div></div>
<style>
.supplier-search{position:relative}.supplier-search:before{display:none}.supplier-search input{padding-left:14px!important}.supplier-live-empty{display:none;text-align:center;color:#64748b}.supplier-live-empty.show{display:table-row}.supplier-live-empty td{padding:35px!important}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.querySelector('.supplier-search input');
    const table = document.querySelector('.supplier-table');
    if (!input || !table) return;
    const body = table.querySelector('tbody');
    const rows = Array.from(body.querySelectorAll('tr')).filter(row => !row.querySelector('.supplier-empty'));
    body.querySelector('.supplier-empty')?.remove();
    const empty = document.createElement('tr');
    empty.className = 'supplier-live-empty';
    empty.innerHTML = '<td colspan="7"><i class="fa fa-search fa-2x mb-2"></i><br><strong>No matching supplier found</strong></td>';
    body.appendChild(empty);
    input.addEventListener('input', function () {
        const query = input.value.trim().toLowerCase();
        let visible = 0;
        rows.forEach(function (row) {
            const match = !query || row.textContent.toLowerCase().includes(query);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        empty.classList.toggle('show', visible === 0);
    });
});
</script>
@endsection
