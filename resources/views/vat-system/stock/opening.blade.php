@extends('layouts.master')

@section('content')
<style>
    .opening-stock-page{background:#f1f5fb;min-height:calc(100vh - 70px)}
    .opening-stock-card{background:#fff;border:0;border-radius:16px;box-shadow:0 8px 25px #173b7212;overflow:hidden}
    .opening-stock-heading{padding:20px 24px;border-bottom:1px solid #e4eaf3}
    .opening-stock-heading h4{color:#172554;font-weight:800}
    .opening-stock-table-wrap{padding:0 18px;overflow-x:auto}
    #openingStockTable{width:100%;min-width:1100px;margin:0;border-collapse:separate;border-spacing:0;table-layout:fixed}
    #openingStockTable thead th{background:#243dba;color:#fff;border:0;border-right:1px solid #5166d0;padding:15px 12px;font-size:11px;font-weight:800;letter-spacing:.3px;text-transform:uppercase;white-space:nowrap;vertical-align:middle}
    #openingStockTable thead th:first-child{border-top-left-radius:4px}#openingStockTable thead th:last-child{border-top-right-radius:4px;border-right:0}
    #openingStockTable tbody td{background:#f8fafc;border:0;border-right:1px solid #b8c0c9;border-bottom:1px solid #b8c0c9;padding:12px 10px;vertical-align:middle}
    #openingStockTable tbody tr:first-child td{border-top:1px solid #b8c0c9}#openingStockTable tbody td:first-child{border-left:1px solid #b8c0c9}
    #openingStockTable tbody tr:hover td{background:#eef5ff}
    #openingStockTable th:nth-child(1),#openingStockTable td:nth-child(1){width:20%}#openingStockTable th:nth-child(2),#openingStockTable td:nth-child(2){width:10%}#openingStockTable th:nth-child(3),#openingStockTable td:nth-child(3){width:8%}#openingStockTable th:nth-child(4),#openingStockTable td:nth-child(4){width:11%}#openingStockTable th:nth-child(5),#openingStockTable td:nth-child(5){width:12%}#openingStockTable th:nth-child(6),#openingStockTable td:nth-child(6){width:11%}#openingStockTable th:nth-child(7),#openingStockTable td:nth-child(7){width:11%}#openingStockTable th:nth-child(8),#openingStockTable td:nth-child(8){width:13%}#openingStockTable th:nth-child(9),#openingStockTable td:nth-child(9){width:4%;text-align:center}
    #openingStockTable input{height:40px;border:1px solid #c5d3e6;border-radius:9px;font-size:13px;padding:8px 10px;min-width:0;width:100%;background:#fff}
    #openingStockTable input:focus{border-color:#2563eb;box-shadow:0 0 0 3px #2563eb1c;outline:0}
    #openingStockTable td:last-child .btn{width:34px;height:34px;padding:0;display:inline-grid;place-items:center}
    .opening-stock-actions{padding:18px;border-top:1px solid #e4eaf3;background:#fff}
    @media(max-width:700px){.opening-stock-heading{padding:18px}.opening-stock-table-wrap{padding:0 10px}.opening-stock-actions{padding:14px}.opening-stock-actions .btn{width:100%;margin:4px 0!important}}
</style>
<div class="main-content opening-stock-page">
    <div class="container-fluid p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div><h1 class="fw-bold text-primary mb-1"><i class="fa fa-box-open me-2"></i>Opening Stock</h1><p class="text-muted mb-0">Add the stock already available for <strong>{{ $firm->name }}</strong>.</p></div>
            <a href="{{ route('vat-system.stock.index') }}" class="btn btn-outline-secondary"><i class="fa fa-arrow-left me-1"></i>Back to Stock</a>
        </div>
        @if($errors->any())<div class="alert alert-danger shadow-sm">{{ $errors->first() }}</div>@endif
        <div class="opening-stock-card">
            <div class="opening-stock-heading"><h4 class="mb-1">Opening Stock Details</h4><small class="text-muted">Add one or more items already available in this firm's inventory.</small></div>
            <div class="card-body p-0">
                <form method="post" action="{{ route('vat-system.stock.opening.store') }}" id="openingStockForm">
                    @csrf
                    <div class="opening-stock-table-wrap"><table id="openingStockTable"><thead><tr><th>Item Name *</th><th>HSN Code</th><th>Unit *</th><th>Opening Qty *</th><th>Purchase Rate</th><th>Sale Rate</th><th>Reorder Level</th><th>Notes</th><th></th></tr></thead><tbody id="openingStockRows"></tbody></table></div>
                    <div class="px-4 pt-3"><div class="alert alert-info mb-0"><i class="fa fa-circle-info me-1"></i>This creates an <strong>Opening Stock</strong> movement in item history. Existing item/unit quantities will be increased.</div></div>
                    <div class="opening-stock-actions"><button type="button" class="btn btn-outline-primary" id="addOpeningRow"><i class="fa fa-plus me-1"></i>Add Another Item</button> <button class="btn btn-primary"><i class="fa fa-save me-1"></i>Save All Opening Stock</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded',()=>{const rows=document.querySelector('#openingStockRows'),add=document.querySelector('#addOpeningRow');let index=0;const row=(i,values={})=>`<tr><td><input name="entries[${i}][item_name]" class="form-control" value="${values.item_name||''}" required placeholder="Item name"></td><td><input name="entries[${i}][hs_code]" class="form-control" value="${values.hs_code||''}" placeholder="HSN"></td><td><input name="entries[${i}][unit]" class="form-control" value="${values.unit||'pcs'}" required></td><td><input name="entries[${i}][quantity]" type="number" min=".001" step=".001" class="form-control" value="${values.quantity||''}" required></td><td><input name="entries[${i}][purchase_rate]" type="number" min="0" step=".01" class="form-control" value="${values.purchase_rate||0}"></td><td><input name="entries[${i}][sale_rate]" type="number" min="0" step=".01" class="form-control" value="${values.sale_rate||0}"></td><td><input name="entries[${i}][reorder_level]" type="number" min="0" step=".001" class="form-control" value="${values.reorder_level||0}"></td><td><input name="entries[${i}][notes]" class="form-control" value="${values.notes||''}" placeholder="Optional"></td><td><button type="button" class="btn btn-outline-danger remove-opening-row" title="Remove row"><i class="fa fa-trash"></i></button></td></tr>`;const addRow=()=>{rows.insertAdjacentHTML('beforeend',row(index++));};add.addEventListener('click',addRow);rows.addEventListener('click',e=>{const button=e.target.closest('.remove-opening-row');if(button&&rows.children.length>1)button.closest('tr').remove();});addRow();});
</script>
@endsection
