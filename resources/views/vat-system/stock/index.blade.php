@extends('layouts.master')

@section('content')
<style>
    .vat-stock-page{background:#f1f5fb;min-height:calc(100vh - 70px)}
    .stock-hero{background:linear-gradient(135deg,#173b72,#2563eb);color:#fff;border-radius:18px;padding:28px 32px;box-shadow:0 10px 25px #173b7226}
    .stock-hero h1{font-weight:800;margin:0;font-size:clamp(28px,4vw,42px)}
    .stock-hero p{margin:6px 0 0;color:#dbeafe}
    .stock-card{background:#fff;border:0;border-radius:16px;box-shadow:0 8px 25px #173b7212}
    .metric{padding:20px 22px;display:flex;align-items:center;gap:15px}
    .metric-icon{width:48px;height:48px;border-radius:13px;display:grid;place-items:center;font-size:20px}
    .metric small{display:block;color:#6b7b93;font-weight:700;text-transform:uppercase;font-size:10px;letter-spacing:.5px}
    .metric strong{display:block;color:#172554;font-size:24px}
    .search-panel{padding:18px}
    .search-panel .form-control{height:48px;border-radius:10px}
    .main-content .stock-table{margin:0;table-layout:fixed !important;width:100%;min-width:1100px}
    .stock-table th{background:#243dba;color:#fff;border:0;font-size:11px;letter-spacing:.3px;padding:15px 12px;white-space:normal;line-height:1.2}
    .stock-table td{padding:16px 14px;vertical-align:middle;color:#233b61;border-color:#e4eaf3}
    .stock-table th:nth-child(1){width:22%}.stock-table th:nth-child(2){width:8%}.stock-table th:nth-child(3){width:13%}.stock-table th:nth-child(4),.stock-table th:nth-child(5){width:12%}.stock-table th:nth-child(6),.stock-table th:nth-child(7){width:10%}.stock-table th:nth-child(8){width:13%}
    .main-content .stock-table th:nth-child(1),.main-content .stock-table td:nth-child(1){width:20% !important}.main-content .stock-table th:nth-child(2),.main-content .stock-table td:nth-child(2){width:8% !important}.main-content .stock-table th:nth-child(3),.main-content .stock-table td:nth-child(3){width:13% !important}.main-content .stock-table th:nth-child(4),.main-content .stock-table td:nth-child(4){width:12% !important}.main-content .stock-table th:nth-child(5),.main-content .stock-table td:nth-child(5){width:12% !important}.main-content .stock-table th:nth-child(6),.main-content .stock-table td:nth-child(6){width:10% !important}.main-content .stock-table th:nth-child(7),.main-content .stock-table td:nth-child(7){width:10% !important}.main-content .stock-table th:nth-child(8),.main-content .stock-table td:nth-child(8){width:15% !important}
    .item-name{font-weight:800;color:#172554}.item-unit{font-size:11px;color:#7a8ba3}
    .status-badge{display:inline-flex;align-items:center;gap:6px;border-radius:30px;padding:7px 11px;font-size:11px;font-weight:800;white-space:nowrap}
    .status-ok{background:#dcfce7;color:#15803d}.status-low{background:#fef3c7;color:#a16207}.status-out{background:#fee2e2;color:#b91c1c}
    .adjust-form{display:flex;gap:4px;align-items:center;width:100%;white-space:nowrap}.adjust-form select{flex:0 0 52px;width:52px}.adjust-form input{flex:1 1 auto;width:1px;min-width:0}.adjust-form .form-control,.adjust-form .form-select{font-size:11px;height:34px;padding:5px}.adjust-form button{flex:0 0 56px;height:34px;min-width:0;font-size:10px;font-weight:800;padding:5px 4px}
    .stock-table td:last-child{padding-left:8px;padding-right:8px}
    @media(max-width:1100px){.stock-table{min-width:920px}.table-scroll{overflow-x:auto}.stock-hero{padding:22px}.metric strong{font-size:20px}}
    @media(max-width:600px){.stock-hero .actions{margin-top:18px}.metric{padding:15px}.metric-icon{width:40px;height:40px}.stock-table{min-width:900px}}
</style>
<div class="main-content vat-stock-page">
    <div class="container-fluid p-3 p-md-4">
        <div class="stock-hero d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div><h1><i class="fa fa-boxes-stacked me-2"></i>VAT Stock</h1><p>Track inventory received from company purchase bills.</p></div>
            <div class="actions"><a href="{{ route('vat-system.company-bills.create') }}" class="btn btn-light fw-bold"><i class="fa fa-plus me-1"></i>Add Purchase Bill</a><a href="{{ route('vat-system.index') }}" class="btn btn-outline-light ms-2">Back</a></div>
        </div>
        @php $allStocks=$stocks->getCollection(); $totalItems=$stocks->total(); $low=$allStocks->filter(fn($s)=>$s->reorder_level>0&&$s->quantity<=$s->reorder_level&&$s->quantity>0)->count(); $out=$allStocks->filter(fn($s)=>$s->quantity<=0)->count(); $value=$allStocks->sum(fn($s)=>(float)$s->quantity*(float)$s->purchase_rate); @endphp
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3"><div class="stock-card metric"><div class="metric-icon bg-primary-subtle text-primary"><i class="fa fa-cubes"></i></div><div><small>Total Items</small><strong>{{ $totalItems }}</strong></div></div></div>
            <div class="col-6 col-xl-3"><div class="stock-card metric"><div class="metric-icon bg-success-subtle text-success"><i class="fa fa-box-open"></i></div><div><small>On This Page</small><strong>{{ number_format($allStocks->sum(fn($s)=>(float)$s->quantity),3) }}</strong></div></div></div>
            <div class="col-6 col-xl-3"><div class="stock-card metric"><div class="metric-icon bg-warning-subtle text-warning"><i class="fa fa-triangle-exclamation"></i></div><div><small>Low Stock</small><strong>{{ $low }}</strong></div></div></div>
            <div class="col-6 col-xl-3"><div class="stock-card metric"><div class="metric-icon bg-danger-subtle text-danger"><i class="fa fa-circle-xmark"></i></div><div><small>Out of Stock</small><strong>{{ $out }}</strong></div></div></div>
        </div>
        @if(session('success'))<div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger border-0 shadow-sm">{{ $errors->first() }}</div>@endif
        <div class="stock-card mb-4"><form class="search-panel row g-2" id="stockSearchForm"><div class="col-md-10"><div class="input-group"><span class="input-group-text bg-white"><i class="fa fa-search text-primary"></i></span><input id="stockSearch" name="search" class="form-control border-start-0" value="{{ request('search') }}" placeholder="Search by item name..." autocomplete="off"></div></div><div class="col-md-2"><button class="btn btn-primary w-100 h-100" type="submit"><i class="fa fa-search me-1"></i>Search</button></div></form></div>
        <div class="stock-card overflow-hidden"><div class="p-3 border-bottom d-flex justify-content-between align-items-center"><div><h4 class="mb-1 fw-bold text-dark">Inventory Overview</h4><small class="text-muted">Use adjustment only for physical stock corrections.</small></div><span id="stockRecordCount" class="badge rounded-pill bg-primary-subtle text-primary">{{ $stocks->total() }} records</span></div><div class="table-scroll"><table class="table stock-table align-middle"><thead><tr><th>Item</th><th>Unit</th><th>Available</th><th>Purchase Rate</th><th>Sale Rate</th><th>Reorder</th><th>Status</th><th>Quick Adjustment</th></tr></thead><tbody id="stockRows">
        @forelse($stocks as $stock)
            @php $status=$stock->quantity<=0?'Out of stock':($stock->reorder_level>0&&$stock->quantity<=$stock->reorder_level?'Low stock':'In stock'); $class=$status==='Out of stock'?'status-out':($status==='Low stock'?'status-low':'status-ok'); @endphp
            <tr><td><div class="item-name">{{ $stock->item_name }}</div><div class="item-unit">Updated {{ $stock->updated_at?->format('Y-m-d') }}</div></td><td>{{ $stock->unit }}</td><td class="fw-bold">{{ number_format($stock->quantity,3) }}</td><td>Rs {{ number_format($stock->purchase_rate,2) }}</td><td>Rs {{ number_format($stock->sale_rate,2) }}</td><td>{{ number_format($stock->reorder_level,3) }}</td><td><span class="status-badge {{ $class }}"><i class="fa fa-circle fa-xs"></i>{{ $status }}</span></td><td><form method="post" action="{{ route('vat-system.stock.adjust',$stock) }}" class="adjust-form">@csrf<select name="type" class="form-select form-select-sm"><option value="in">IN</option><option value="out">OUT</option></select><input name="quantity" type="number" min=".001" step=".001" class="form-control form-control-sm" placeholder="Qty" required><button class="btn btn-success btn-sm">Save</button></form></td></tr>
        @empty<tr><td colspan="8" class="text-center p-5"><i class="fa fa-box-open fa-3x text-muted mb-3"></i><h5>No VAT stock yet</h5><p class="text-muted">Save a company purchase bill to add inventory.</p></td></tr>@endforelse
        </tbody></table></div><div class="p-3" id="stockPagination">{{ $stocks->links() }}</div></div>
</div>
</div>
<script>
document.addEventListener('DOMContentLoaded',()=>{const input=document.querySelector('#stockSearch'),form=document.querySelector('#stockSearchForm'),rows=document.querySelector('#stockRows'),count=document.querySelector('#stockRecordCount'),pager=document.querySelector('#stockPagination');let timer;const esc=v=>String(v??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));const render=data=>{count.textContent=data.total+' records';pager.innerHTML='';if(!data.items.length){rows.innerHTML='<tr><td colspan="8" class="text-center p-5"><i class="fa fa-box-open fa-3x text-muted mb-3"></i><h5>No matching VAT stock</h5></td></tr>';return}rows.innerHTML=data.items.map(s=>{const status=s.quantity<=0?'Out of stock':(s.reorder_level>0&&s.quantity<=s.reorder_level?'Low stock':'In stock');const cls=status==='Out of stock'?'status-out':(status==='Low stock'?'status-low':'status-ok');return '<tr><td><div class="item-name">'+esc(s.item_name)+'</div><div class="item-unit">Live result</div></td><td>'+esc(s.unit)+'</td><td class="fw-bold">'+s.quantity.toFixed(3)+'</td><td>Rs '+s.purchase_rate.toFixed(2)+'</td><td>Rs '+s.sale_rate.toFixed(2)+'</td><td>'+s.reorder_level.toFixed(3)+'</td><td><span class="status-badge '+cls+'"><i class="fa fa-circle fa-xs"></i>'+status+'</span></td><td><a class="btn btn-sm btn-outline-primary" href="'+@json(route('vat-system.stock.index'))+'">Refresh</a></td></tr>'}).join('')};const search=()=>{clearTimeout(timer);timer=setTimeout(()=>fetch(@json(route('vat-system.stock.index'))+'?search='+encodeURIComponent(input.value),{headers:{Accept:'application/json'}}).then(r=>r.json()).then(render),250)};input.addEventListener('input',search);form.addEventListener('submit',e=>{e.preventDefault();search()})});
</script>
@endsection
