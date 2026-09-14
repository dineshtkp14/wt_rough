@extends('layouts.master')
@section('content')
<style>
.party-page{min-height:calc(100vh - 70px);background:#eef4fb;padding:28px}.party-shell{max-width:1250px;margin:auto}.party-head{display:flex;justify-content:space-between;align-items:center;gap:20px;margin-bottom:26px}.party-kicker{color:#2563eb;font-size:12px;font-weight:800;letter-spacing:1.4px;text-transform:uppercase}.party-head h1{color:#102f5f;font-size:38px;font-weight:900;margin:7px 0}.party-head p{color:#64748b;margin:0}.firm-pill{display:inline-flex;gap:8px;align-items:center;margin-top:14px;padding:8px 15px;border:1px solid #bfdbfe;border-radius:999px;background:#eff6ff;color:#174a91;font-weight:800;font-size:13px}.party-table-box{background:#fff;border:1px solid #dbe7f5;border-radius:16px;box-shadow:0 10px 28px #1d4e8918;overflow:hidden}.party-table-title{display:flex;justify-content:space-between;align-items:center;padding:18px 22px;background:#173f7a;color:#fff}.party-table-title h2{margin:0;font-size:19px}.party-table-title span{font-size:13px;background:#fff;color:#173f7a;padding:5px 10px;border-radius:20px;font-weight:800}.party-table{width:100%;border-collapse:collapse}.party-table th{padding:14px 16px;background:#354bd2;color:#fff;text-align:left;font-size:12px;text-transform:uppercase;letter-spacing:.4px;white-space:nowrap}.party-table td{padding:16px;border-bottom:1px solid #e2eaf4;color:#475569;vertical-align:middle}.party-table tbody tr:nth-child(even){background:#f8fbff}.party-table tbody tr:hover{background:#edf5ff}.party-table .serial{width:55px;color:#64748b;font-weight:800}.customer-name{color:#123b76;font-weight:900;font-size:16px}.customer-sub{font-size:12px;color:#94a3b8;margin-top:3px}.invoice-link{display:inline-flex;align-items:center;gap:6px;color:#2563eb;text-decoration:none;font-weight:800;white-space:nowrap}.invoice-link:hover{text-decoration:underline}.no-invoices{color:#94a3b8;font-size:13px}.action-group{display:flex;gap:7px;white-space:nowrap}.action-group .btn{border-radius:7px;font-size:12px;font-weight:700}.empty-row{text-align:center;padding:60px!important;color:#64748b}.empty-row i{display:block;font-size:40px;color:#94a3b8;margin-bottom:12px}@media(max-width:800px){.party-page{padding:16px}.party-head{align-items:flex-start;flex-direction:column}.party-head h1{font-size:30px}.party-table-box{overflow-x:auto}.party-table{min-width:850px}.party-table-title{min-width:850px}}\n+</style>
<div class="main-content party-page"><div class="party-shell">
 <form method="GET" action="{{ route('vat-system.party-ledger.customers') }}" class="mb-3 d-flex align-items-end gap-2"><div><label class="form-label fw-bold">Financial Year</label><select name="fiscal_year" class="form-select">@foreach($fiscalYears as $value=>$label)<option value="{{ $value }}" {{ request('fiscal_year', array_key_first($fiscalYears)) === $value ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select></div><button class="btn btn-primary">Apply Fiscal Year</button></form>
 <div class="party-head"><div><div class="party-kicker"><i class="fa fa-book-open me-1"></i> Customer Accounts</div><h1>VAT Party Ledger</h1><p>View invoices, balances, and confirmation letters for your customers.</p><div class="firm-pill"><i class="fa fa-building"></i>{{ $firm->name }}</div></div><div class="d-flex gap-2"><a href="{{ route('vat-system.firm.switch', ['next' => 'ledger', 'fiscal_year' => $fiscalYear]) }}" class="btn btn-outline-primary px-4"><i class="fa fa-repeat me-1"></i>Change Firm</a><a href="{{ route('vat-system.index') }}" class="btn btn-outline-primary px-4"><i class="fa fa-arrow-left me-1"></i>Back</a><a href="{{ route('vat-system.party-ledger.print-all') }}" target="_blank" class="btn btn-primary px-4"><i class="fa fa-print me-1"></i>Print All Balance Confirmations &amp; Party Ledgers</a></div></div>
 <section class="party-table-box"><div class="party-table-title"><h2><i class="fa fa-users me-2"></i>Customer List</h2><span>{{ $customers->count() }} customers</span></div><div class="table-responsive"><table class="party-table"><thead><tr><th class="serial">#</th><th>Customer</th><th>PAN / VAT No.</th><th>Contact</th><th>Address</th><th>Invoices</th><th>Actions</th></tr></thead><tbody>
 @forelse($customers as $customer)<tr><td class="serial">{{ $loop->iteration }}</td><td><div class="customer-name">{{ $customer->name }}</div><div class="customer-sub">VAT customer</div></td><td>{{ $customer->pan_no ?: '-' }}</td><td><i class="fa fa-phone text-primary me-1"></i>{{ $customer->phone ?: '-' }}</td><td>{{ $customer->address ?: '-' }}</td><td>@if($customer->sales_invoices_count)<a class="invoice-link" href="{{ route('vat-system.bills.index',['customer_id'=>$customer->id]) }}"><i class="fa fa-file-invoice"></i>{{ $customer->sales_invoices_count }} invoice{{ $customer->sales_invoices_count == 1 ? '' : 's' }}</a>@else<span class="no-invoices">No invoices</span>@endif</td><td><div class="action-group"><a href="{{ route('vat-system.party-ledger',$customer) }}" class="btn btn-primary"><i class="fa fa-book me-1"></i>Ledger</a><a href="{{ route('vat-system.balance-confirmation',$customer) }}" class="btn btn-outline-primary" title="Balance Confirmation"><i class="fa fa-file-signature"></i></a></div></td></tr>
 @empty<tr><td colspan="7" class="empty-row"><i class="fa fa-users"></i><strong>No customers found</strong><br>Add a VAT customer to start tracking transactions.</td></tr>@endforelse
 </tbody></table></div></section>
</div></div>
<style>
.party-live-search{position:relative;margin:16px 20px 0}.party-live-search i{position:absolute;left:15px;top:13px;color:#2563eb}.party-live-search input{width:100%;height:44px;padding:8px 40px;border:1px solid #cbd9eb;border-radius:9px;color:#173b72;outline:none}.party-live-search input:focus{border-color:#2563eb;box-shadow:0 0 0 3px #2563eb20}.party-search-empty{display:none;text-align:center;padding:30px;color:#64748b}.party-search-empty.show{display:table-row}.party-search-empty td{padding:30px!important}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const box = document.querySelector('.party-table-box');
    const table = box?.querySelector('.party-table');
    const title = box?.querySelector('.party-table-title');
    if (!box || !table || !title) return;
    const bar = document.createElement('div');
    bar.className = 'party-live-search';
    bar.innerHTML = '<i class="fa fa-search"></i><input type="search" placeholder="Search customer by name, PAN, phone or address" autocomplete="off">';
    title.after(bar);
    const input = bar.querySelector('input');
    const rows = Array.from(table.querySelectorAll('tbody tr')).filter(row => !row.querySelector('.empty-row'));
    table.querySelector('tbody .empty-row')?.remove();
    const count = title.querySelector('span');
    const originalCount = rows.length;
    const empty = document.createElement('tr');
    empty.className = 'party-search-empty';
    empty.innerHTML = '<td colspan="7"><i class="fa fa-search mb-2"></i><br><strong>No matching customer found</strong></td>';
    table.querySelector('tbody').appendChild(empty);
    input.addEventListener('input', function () {
        const query = input.value.trim().toLowerCase();
        let visible = 0;
        rows.forEach(function (row) {
            const match = !query || row.textContent.toLowerCase().includes(query);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        empty.classList.toggle('show', visible === 0);
        if (count) count.textContent = visible + (visible === 1 ? ' customer' : ' customers');
    });
});
</script>
<style>
.party-pagination{display:flex;justify-content:center;align-items:center;gap:6px;padding:16px 20px;background:#fff}.party-pagination button{min-width:34px;height:34px;padding:4px 10px;border:1px solid #cbd9eb;border-radius:7px;background:#fff;color:#2563eb;font-weight:700;cursor:pointer}.party-pagination button:hover:not(:disabled),.party-pagination button.active{background:#2563eb;color:#fff;border-color:#2563eb}.party-pagination button:disabled{color:#a5b4c7;background:#f8fafc;cursor:not-allowed}.party-page-info{margin:0 8px;color:#64748b;font-size:13px}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const box = document.querySelector('.party-table-box');
    const table = box?.querySelector('.party-table');
    const input = box?.querySelector('.party-live-search input');
    if (!box || !table || !input) return;
    const rows = Array.from(table.querySelectorAll('tbody tr')).filter(row => !row.classList.contains('party-search-empty') && !row.querySelector('.empty-row'));
    const wrapper = table.closest('.table-responsive');
    const pager = document.createElement('div');
    pager.className = 'party-pagination';
    wrapper.after(pager);
    const perPage = 10;
    let page = 1;
    function render() {
        const query = input.value.trim().toLowerCase();
        const matches = rows.filter(row => !query || row.textContent.toLowerCase().includes(query));
        const pages = Math.max(1, Math.ceil(matches.length / perPage));
        page = Math.min(page, pages);
        rows.forEach(row => row.style.display = 'none');
        matches.slice((page - 1) * perPage, page * perPage).forEach(row => row.style.display = '');
        pager.innerHTML = '';
        const previous = document.createElement('button');
        previous.textContent = '‹'; previous.title = 'Previous page'; previous.disabled = page === 1;
        previous.onclick = () => { page--; render(); };
        pager.appendChild(previous);
        for (let number = 1; number <= pages; number++) {
            const button = document.createElement('button');
            button.textContent = number; button.className = number === page ? 'active' : '';
            button.onclick = () => { page = number; render(); };
            pager.appendChild(button);
        }
        const next = document.createElement('button');
        next.textContent = '›'; next.title = 'Next page'; next.disabled = page === pages;
        next.onclick = () => { page++; render(); };
        pager.appendChild(next);
        const info = document.createElement('span');
        info.className = 'party-page-info';
        info.textContent = matches.length ? 'Page ' + page + ' of ' + pages : 'No results';
        pager.appendChild(info);
    }
    input.addEventListener('input', function () { page = 1; render(); });
    render();
});
</script>
@endsection
