@extends('layouts.master')
@section('content')
@php
    $editing = isset($bill);
    $activeFirm = $activeFirm ?? null;
    $selectedFirm = old('firm_id', isset($bill) ? $bill->firm_id : ($activeFirm?->id ?? ''));
    $initialBsDate = old('bill_date_bs');
    if (!$initialBsDate) {
        $initialBsDate = isset($bill)
            ? \App\Support\NepaliDate::adToBsString($bill->bill_date->format('Y-m-d'), 'en')
            : \App\Support\NepaliDate::adToBsString(now()->format('Y-m-d'), 'en');
    }
    $initialBillDateMode = old('bill_date_mode', 'bs');
    $extraCustomerPayload = !empty($extraCustomer) ? ['id' => $extraCustomer->id, 'name' => $extraCustomer->name, 'address' => $extraCustomer->address, 'contact_name' => $extraCustomer->contact_name, 'notes' => $extraCustomer->notes] : null;
    $extraCustomerEndpoint = !empty($extraCustomer) ? route('vat-system.extra-customers.update', $extraCustomer) : route('vat-system.extra-customers.store');
    $unitOptions = ['PCS', 'KGS', 'FEET', 'KG', 'MTR', 'BOX', 'SET', 'LTR', 'NOS'];
@endphp
<style>
#vatBillForm>.firm-display{display:flex!important;width:100%;margin:0 0 14px!important;justify-content:center!important;text-align:center!important;position:relative!important}
#vatBillForm>.firm-display i{position:absolute!important;left:18px!important}
.create-page-heading{position:relative!important}.selected-firm-heading{position:absolute;left:50%;transform:translateX(-50%);text-align:center;color:#173b72;line-height:1.15}.selected-firm-heading small{display:block;font-size:11px;font-weight:900;letter-spacing:1.2px;text-transform:uppercase;color:#52719a}.selected-firm-heading strong{display:block;font-size:25px;font-weight:900;letter-spacing:.3px;white-space:nowrap}@media(max-width:800px){.selected-firm-heading{position:static;transform:none;margin:10px auto;text-align:center}.create-page-heading{flex-wrap:wrap}.selected-firm-heading strong{font-size:20px}}
#vatBillForm>.firm-display{display:none!important}
.vat-create{background:#eef3f9;min-height:calc(100vh - 70px)}.vat-create .card{border:0;border-radius:14px;box-shadow:0 8px 24px #18365d12}.vat-create .card-header{background:#173b72;color:#fff;font-weight:800}.vat-create label{font-size:12px;font-weight:800;color:#405675;text-transform:uppercase}.vat-create .form-control,.vat-create .form-select{border-radius:8px;border-color:#cad7e7}.vat-create th{background:#173b72;color:#fff;font-size:12px}.vat-create .total-box{background:#f8fbff;border:1px solid #dbe6f2;border-radius:10px}.vat-create .remove-row{font-size:18px}

    .item-cell{position:relative}.item-suggestion-menu{position:fixed;z-index:9999;left:auto;right:auto;top:auto;width:320px;background:#102f63;border:1px solid #6ea8ff;border-radius:12px;box-shadow:0 12px 28px #102f6355;overflow:hidden;display:none}.item-suggestion-menu.show{display:block}.item-suggestion{display:block;width:100%;border:0;border-bottom:1px solid #31578d;background:#102f63;text-align:left;padding:10px 13px;color:#fff;font-size:12px;cursor:pointer}.item-suggestion:last-child{border-bottom:0}.item-suggestion:hover{background:#2563eb}.item-suggestion strong{display:block;font-size:13px;color:#fff}.item-suggestion small{color:#dbeafe}.item-suggestion-empty{padding:12px;color:#dbeafe;font-size:12px}</style>
<style>.vat-create .card-header{padding:15px 20px;font-size:16px}.vat-create .card-body{padding:22px 18px}.vat-create .form-control,.vat-create .form-select{min-height:42px;background:#fbfdff}.vat-create .form-control:focus,.vat-create .form-select:focus{border-color:#2563eb;box-shadow:0 0 0 .2rem #2563eb20}.firm-display{min-height:66px;display:flex;align-items:center;gap:14px;padding:12px 18px;border:1px solid #a8c7ff;border-radius:12px;background:linear-gradient(100deg,#e6f0ff,#f8fbff);color:#102f63;font-weight:900;box-shadow:inset 4px 0 #2563eb}.firm-display i{color:#2563eb;font-size:22px}.firm-display span{font-size:19px;line-height:1.2}.firm-display small{display:block;font-size:10px;color:#52719a;font-weight:800;text-transform:uppercase;letter-spacing:.7px;margin-bottom:3px}.vat-create .bill-row:hover{background:#f4f8ff}.vat-create #billItems td{padding:12px 10px}.vat-create .total-box{box-shadow:inset 0 1px 0 #fff}.vat-create .card-footer .btn{border-radius:10px;font-weight:800;padding:13px}@media(max-width:700px){.firm-display span{font-size:15px}}</style>
<div class="main-content vat-create"><div class="container-fluid p-3 p-md-4">
<div class="d-flex justify-content-between align-items-center mb-4 create-page-heading"><div><h1 class="fw-bold mb-1">{{ $editing ? 'Edit Sales Invoice' : 'Create Sales Invoice' }}</h1><p class="text-muted mb-0">Create an invoice for goods sold to your customer.</p></div><div class="selected-firm-heading"><small>Selected Firm</small><strong>{{ $activeFirm?->name ?? 'Select a firm' }}</strong></div><div class="d-flex gap-2"><a href="{{ route('vat-system.stock.opening.create') }}" class="btn btn-warning fw-bold"><i class="fa fa-box-open me-1"></i>Opening Stock</a><a href="{{ route('vat-system.bills.index') }}" class="btn btn-outline-secondary">Back</a></div></div>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="post" action="{{ $editing ? route('vat-system.bills.update', $bill) : route('vat-system.bills.store') }}" id="vatBillForm" novalidate>@csrf @if($editing) @method('PUT') @endif
<div class="card mb-4"><div class="card-header d-flex justify-content-between align-items-center">Bill and Party Information <button type="button" class="btn btn-light btn-sm fw-bold" id="openExtraCustomer"><i class="fa fa-{{ !empty($extraCustomer) ? 'pen' : 'plus' }} me-1"></i>{{ !empty($extraCustomer) ? 'Edit Extra Customer' : 'Extra Customer' }}</button></div><div class="card-body"><div class="row g-3">
<div class="col-md-4"><label>Firm *</label><select name="firm_id" id="firm_id" class="form-select" required><option value="">Choose firm</option>@foreach($firms as $firm)<option value="{{ $firm->id }}" data-name="{{ $firm->name }}" data-pan="{{ $firm->pan_no }}" data-address="{{ $firm->address }}" data-phone="{{ $firm->phone }}" @selected($selectedFirm == $firm->id)>{{ $firm->name }} â€” PAN {{ $firm->pan_no }}</option>@endforeach</select></div>
<input type="hidden" name="seller_pan_no" id="seller_pan_no" value="{{ old('seller_pan_no', $bill->seller_pan_no ?? '') }}"><input type="hidden" name="seller_address" id="seller_address" value="{{ old('seller_address', $bill->seller_address ?? '') }}">
<div class="col-md-3"><label>Seller / Firm Name *</label><input name="seller_name" class="form-control" value="{{ old('seller_name', $bill->seller_name ?? 'NEPAL') }}" required></div><div class="col-md-3"><label>Seller VAT No.</label><input name="seller_vat_no" class="form-control" value="{{ old('seller_vat_no', $bill->seller_vat_no ?? '123456789') }}"></div><div class="col-md-3"><label>Seller Phone</label><input name="seller_phone" class="form-control" value="{{ old('seller_phone', $bill->seller_phone ?? '123456792') }}"></div><div class="col-md-3"><label>Seller Email</label><input type="email" name="seller_email" class="form-control" value="{{ old('seller_email', $bill->seller_email ?? 'nepalbillingdemo@gmail.com') }}"></div>
<div class="col-md-4"><label>Customer *</label><select name="customer_id" class="form-select" required><option value="">Choose VAT customer</option>@foreach($customers as $customer)<option value="{{ $customer->id }}" @selected(old('customer_id', $bill->customer_id ?? '') == $customer->id)>{{ $customer->name }}{{ $customer->pan_no ? ' ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¾Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¦ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¦ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â '.$customer->pan_no : '' }}</option>@endforeach</select></div><div class="col-md-2"><label>Bill No. *</label><input name="bill_no" class="form-control" value="{{ old('bill_no', $bill->bill_no ?? '') }}" required></div><div class="col-md-3"><label>Bill Date *</label><input type="date" name="bill_date" class="form-control" value="{{ old('bill_date', isset($bill) ? $bill->bill_date->format('Y-m-d') : now()->format('Y-m-d')) }}" required></div>
</div></div></div>
<div class="card mb-4"><div class="card-header d-flex justify-content-between align-items-center">Bill Items <div class="bill-items-actions"><button type="button" class="btn btn-outline-light btn-sm" id="toggleMargin"><i class="fa fa-percent me-1"></i>Add Margin</button><button type="button" class="btn btn-light btn-sm" id="addBillRow"><i class="fa fa-plus me-1"></i>Add Item</button></div></div><div id="marginControl" class="margin-control" hidden><label for="marginPercent"><i class="fa fa-percent me-1"></i>Margin on rate</label><input id="marginPercent" type="number" min="0" max="1000" step=".01" value="0"><span>Example: 157 + 5% = 164.85</span></div><div class="table-responsive"><table class="table align-middle mb-0" id="billItems"><thead><tr><th>#</th><th>Particulars *</th><th>Sale Price</th><th>Purchase Price</th><th>Stock</th><th>H.S. Code</th><th>Unit *</th><th>Qty *</th><th>Rate *</th><th>Taxable</th><th>Amount</th><th></th></tr></thead><tbody>@if($editing)@foreach($bill->items as $item)<tr class="bill-row"><td class="sn">{{ $loop->iteration }}</td><td><input name="items[{{ $loop->index }}][item_name]" class="form-control item-name" autocomplete="off" value="{{ old('items.'.$loop->index.'.item_name', $item->item_name) }}" required></td><td><input class="form-control sale-price text-end" readonly></td><td><input class="form-control purchase-price text-end" readonly></td><td><input class="form-control stock-available text-end" readonly></td><td><input name="items[{{ $loop->index }}][hs_code]" class="form-control" value="{{ old('items.'.$loop->index.'.hs_code', $item->hs_code) }}"></td><td><input name="items[{{ $loop->index }}][unit]" class="form-control" value="{{ old('items.'.$loop->index.'.unit', $item->unit) }}" required></td><td><input name="items[{{ $loop->index }}][quantity]" class="form-control qty" type="number" min=".001" step=".001" value="{{ old('items.'.$loop->index.'.quantity', $item->quantity) }}" required></td><td><input name="items[{{ $loop->index }}][rate]" class="form-control rate" type="number" min="0" step=".01" value="{{ old('items.'.$loop->index.'.rate', $item->rate) }}" required><small class="original-rate" hidden></small></td><td class="text-center"><input name="items[{{ $loop->index }}][is_taxable]" value="1" type="checkbox" class="taxable" @checked(old('items.'.$loop->index.'.is_taxable', $item->is_taxable))></td><td><input class="form-control amount text-end" value="0.00" readonly></td><td><button type="button" class="btn btn-outline-danger remove-row"><i class="fa fa-trash"></i></button></td></tr>@endforeach @else<tr class="bill-row"><td class="sn">1</td><td><input name="items[0][item_name]" class="form-control item-name" autocomplete="off" required></td><td><input class="form-control sale-price text-end" readonly></td><td><input class="form-control purchase-price text-end" readonly></td><td><input class="form-control stock-available text-end" readonly></td><td><input name="items[0][hs_code]" class="form-control"></td><td><input name="items[0][unit]" class="form-control" value="KG" required></td><td><input name="items[0][quantity]" class="form-control qty" type="number" min=".001" step=".001" value="1" required></td><td><input name="items[0][rate]" class="form-control rate" type="number" min="0" step=".01" required><small class="original-rate" hidden></small></td><td class="text-center"><input name="items[0][is_taxable]" value="1" type="checkbox" class="taxable" checked></td><td><input class="form-control amount text-end" value="0.00" readonly></td><td><button type="button" class="btn btn-outline-danger remove-row"><i class="fa fa-trash"></i></button></td></tr>@endif</tbody></table></div></div>
<div class="row g-4"><div class="col-lg-7"><div class="card"><div class="card-header">Notes</div><div class="card-body"><textarea name="notes" rows="4" class="form-control" placeholder="Optional notes">{{ old('notes', $bill->notes ?? '') }}</textarea></div></div></div><div class="col-lg-5"><div class="card"><div class="card-header">Bill Summary</div><div class="card-body"><div class="total-box p-3"><div class="d-flex justify-content-between mb-2">Total <strong id="total">0.00</strong></div><div class="d-flex justify-content-between align-items-center mb-2">Discount <input name="discount" id="discount" class="form-control form-control-sm text-end" style="width:110px" value="{{ old('discount', $bill->discount ?? '0') }}"></div><div class="d-flex justify-content-between mb-2">Non-Taxable Total <strong id="nonTaxable">0.00</strong></div><div class="d-flex justify-content-between mb-2">Taxable Total <strong id="taxableTotal">0.00</strong></div><div class="d-flex justify-content-between mb-2">VAT (13%) <strong id="vat">0.00</strong></div><hr><div class="d-flex justify-content-between fs-5 fw-bold">Grand Total <strong id="grandTotal">0.00</strong></div></div></div><div class="card-footer bg-white border-0 p-3"><button class="btn btn-primary btn-lg w-100"><i class="fa fa-print me-2"></i>{{ $editing ? 'Update & View VAT Bill' : 'Save & View VAT Bill' }}</button></div></div></div></div>
</form></div></div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const units = ['PCS', 'KGS', 'FEET', 'KG', 'MTR', 'BOX', 'SET', 'LTR', 'NOS'];
    const makeUnitDropdown = input => {
        if (!input || input.dataset.unitDropdownReady) return;
        input.dataset.unitDropdownReady = '1';
        const value = String(input.value || 'KG').trim().toUpperCase();
        const select = document.createElement('select');
        select.className = 'form-select unit-select';
        select.setAttribute('aria-label', 'Unit');
        units.forEach(unit => select.add(new Option(unit, unit)));
        if (value && !units.includes(value)) select.add(new Option(value, value));
        select.add(new Option('+ Add More...', '__custom__'));
        select.value = value || 'KG';
        input.type = 'hidden';
        input.classList.add('unit-hidden-value');
        input.value = select.value;
        input.parentNode.insertBefore(select, input);
        select.addEventListener('change', function () {
            if (select.value === '__custom__') {
                const custom = (window.prompt('Enter a unit name, for example: Dozen') || '').trim().toUpperCase();
                if (!custom) { select.value = input.value || 'KG'; return; }
                select.insertBefore(new Option(custom, custom), select.lastElementChild);
                select.value = custom;
            }
            input.value = select.value;
        });
    };
    const syncUnitDropdowns = row => row?.querySelectorAll('.unit-hidden-value').forEach(input => {
        const select = input.parentElement.querySelector('.unit-select');
        if (select && select.value !== input.value) select.value = input.value;
    });
    document.querySelectorAll('input[name$="[unit]"]').forEach(makeUnitDropdown);
    document.querySelector('#billItems tbody')?.addEventListener('mousedown', function (event) {
        if (event.target.closest('.item-suggestion')) setTimeout(() => syncUnitDropdowns(event.target.closest('.bill-row')), 0);
    });
    document.querySelector('#addBillRow')?.addEventListener('click', function () {
        setTimeout(() => {
            const row = document.querySelector('#billItems tbody .bill-row:last-child');
            const input = row?.querySelector('input[name$="[unit]"]');
            makeUnitDropdown(input);
            if (input) { input.value = 'KG'; const select = input.parentElement.querySelector('.unit-select'); if (select) select.value = 'KG'; }
        }, 0);
    });
});
</script>
<datalist id="vatItemSuggestions">@foreach($catalogItems as $catalogItem)<option value="{{ $catalogItem->itemsname }}"></option>@endforeach</datalist>
<datalist id="vatCustomerSuggestions">@foreach($customers as $customer)<option value="{{ $customer->name }} â€” PAN {{ $customer->pan_no ?: '-' }} â€” {{ $customer->phone ?: '-' }}" data-id="{{ $customer->id }}" data-name="{{ $customer->name }}" data-details="PAN: {{ $customer->pan_no ?: 'Not provided' }} Â· Phone: {{ $customer->phone ?: 'Not provided' }}" data-search="{{ strtolower($customer->name.' '.$customer->pan_no.' '.$customer->phone.' '.$customer->address) }}"></option>@endforeach</datalist>
<script>document.addEventListener('DOMContentLoaded',function(){const body=document.querySelector('#billItems tbody');function reindex(){body.querySelectorAll('.bill-row').forEach(function(r,i){r.querySelector('.sn').textContent=i+1;r.querySelectorAll('input[name]').forEach(function(x){x.name=x.name.replace(/items\[\d+\]/,'items['+i+']')})})}function total(){let all=0,tax=0;body.querySelectorAll('.bill-row').forEach(function(r){let a=(+r.querySelector('.qty').value||0)*(+r.querySelector('.rate').value||0);r.querySelector('.amount').value=a.toFixed(2);all+=a;if(r.querySelector('.taxable').checked)tax+=a});let non=all-tax,vat=Math.round(tax*.13*100)/100,discount=+document.querySelector('#discount').value||0;document.querySelector('#total').textContent=all.toFixed(2);document.querySelector('#nonTaxable').textContent=non.toFixed(2);document.querySelector('#taxableTotal').textContent=tax.toFixed(2);document.querySelector('#vat').textContent=vat.toFixed(2);document.querySelector('#grandTotal').textContent=(all-discount+vat).toFixed(2)}document.querySelector('#addBillRow').onclick=function(){let r=body.querySelector('.bill-row').cloneNode(true);delete r.dataset.baseRate;r.querySelectorAll('input').forEach(function(x){if(x.type==='checkbox')x.checked=true;else if(x.classList.contains('qty'))x.value=1;else if(x.classList.contains('amount'))x.value='0.00';else x.value=''});body.appendChild(r);reindex();if(typeof bindSuggestion==='function')bindSuggestion(r.querySelector('.item-name'));total()};body.addEventListener('click',function(e){if(e.target.closest('.remove-row')&&body.children.length>1){e.target.closest('.bill-row').remove();reindex();total()}});body.addEventListener('input',total);body.addEventListener('change',total);document.querySelector('#discount').addEventListener('input',total);total();const catalog={!! $catalogJson !!};const bindSuggestion=input=>{input.dataset.suggestionBound='1';const cell=input.closest('td');cell.classList.add('item-cell');let menu=cell.querySelector('.item-suggestion-menu');if(!menu){menu=document.createElement('div');menu.className='item-suggestion-menu';cell.appendChild(menu)}const show=()=>{const box=input.getBoundingClientRect();menu.style.left=box.left+'px';menu.style.top=(box.bottom+5)+'px';menu.style.width=Math.max(300,box.width)+'px';const q=input.value.trim().toLowerCase();const matches=catalog.filter(i=>i.name.toLowerCase().includes(q)).slice(0,8);menu.innerHTML=matches.length?matches.map(i=>'<button type="button" class="item-suggestion" data-index="'+catalog.indexOf(i)+'"><strong>'+i.name+'</strong><small>Sale Price: '+(i.sale_price??'—')+' &nbsp; Purchase Price: '+(i.purchase_price??'—')+' &nbsp; Stock: '+(i.stock_quantity===null?'Not tracked':Number(i.stock_quantity).toFixed(3))+' &nbsp; Unit: '+(i.unit||'—')+' &nbsp; H.S. Code: '+(i.hs_code||'—')+'</small></button>').join(''):'<div class="item-suggestion-empty">No matching VAT purchase item</div>';menu.classList.add('show')};input.addEventListener('input',show);input.addEventListener('focus',()=>{if(input.value)show()});menu.addEventListener('mousedown',e=>{const button=e.target.closest('.item-suggestion');if(!button)return;e.preventDefault();const found=catalog[button.dataset.index];input.value=found.name;const row=input.closest('.bill-row');const salePrice=found.sale_price??found.price;if(salePrice!==null){row.querySelector('.rate').value=salePrice;row.dataset.baseRate=salePrice}if(found.unit)row.querySelector('input[name$="[unit]"]').value=found.unit;if(found.hs_code)row.querySelector('input[name$="[hs_code]"]').value=found.hs_code;menu.classList.remove('show');total()})};body.querySelectorAll('.item-name').forEach(bindSuggestion);body.addEventListener('focusin',e=>{if(e.target.classList.contains('item-name')&&!e.target.dataset.suggestionBound){e.target.dataset.suggestionBound='1';bindSuggestion(e.target)}});document.addEventListener('click',e=>{if(!e.target.closest('.item-cell'))document.querySelectorAll('.item-suggestion-menu').forEach(m=>m.classList.remove('show'))});body.addEventListener('change',e=>{if(!e.target.classList.contains('item-name'))return;const found=catalog.find(i=>i.name.toLowerCase()===e.target.value.trim().toLowerCase());if(!found)return;const row=e.target.closest('.bill-row');const salePrice=found.sale_price??found.price;if(salePrice!==null){row.querySelector('.rate').value=salePrice;row.dataset.baseRate=salePrice}if(found.unit)row.querySelector('input[name$="[unit]"]').value=found.unit;if(found.hs_code)row.querySelector('input[name$="[hs_code]"]').value=found.hs_code;total()});});</script>
<script>document.addEventListener('DOMContentLoaded',function(){const firm=document.getElementById('firm_id');if(!firm)return;const parent=firm.closest('.col-md-4');if(parent)parent.style.display='none';['seller_vat_no','seller_phone','seller_email'].forEach(function(n){const e=document.querySelector('[name="'+n+'"]');if(e&&e.closest('[class*="col-"]'))e.closest('[class*="col-"]').style.display='none'});const sync=function(){const o=firm.options[firm.selectedIndex];if(!o||!o.value)return;const set=(n,v)=>{const e=document.querySelector('[name="'+n+'"]');if(e)e.value=v||''};set('seller_name',o.dataset.name);set('seller_phone',o.dataset.phone);set('seller_pan_no',o.dataset.pan);set('seller_address',o.dataset.address);set('seller_vat_no','');set('seller_email','')};firm.addEventListener('change',sync);if(firm.value)sync()});</script>
<script>document.addEventListener('DOMContentLoaded',function(){const select=document.querySelector('select[name="customer_id"]');if(!select)return;select.querySelectorAll('option').forEach(function(option){if(option.value){const text=option.textContent;const broken=text.indexOf('Ãƒ');if(broken>-1)option.textContent=text.slice(0,broken).trim()+' â€” PAN '+text.slice(text.lastIndexOf(' ')+1)}})});</script>
<script>document.addEventListener('DOMContentLoaded',function(){const select=document.querySelector('select[name="customer_id"]'),list=document.getElementById('vatCustomerSuggestions');if(!select||!list)return;select.classList.add('d-none');const input=document.createElement('input');input.type='search';input.className='form-control';input.setAttribute('list','vatCustomerSuggestions');input.placeholder='Search customer by name, PAN or contact number';input.autocomplete='off';const selected=select.options[select.selectedIndex];if(selected&&selected.value)input.value=selected.textContent.trim();select.parentNode.insertBefore(input,select);const sync=function(){const q=input.value.trim().toLowerCase();let match=null;list.querySelectorAll('option').forEach(function(o){const searchable=(o.dataset.search||o.value).toLowerCase();if(!match&&(searchable===q||o.value.toLowerCase()===q||searchable.includes(q)&&q.length>=3))match=o});select.value=match?match.dataset.id:'';if(match)input.value=match.value};input.addEventListener('input',sync);input.addEventListener('change',sync);});</script>
<style>.customer-search-wrap{position:relative}.customer-search-menu{position:absolute;z-index:1000;left:0;right:0;top:calc(100% + 5px);display:none;background:#102f63;border:1px solid #6ea8ff;border-radius:10px;box-shadow:0 12px 28px #102f6355;overflow:hidden}.customer-search-menu.show{display:block}.customer-search-result{display:block;width:100%;border:0;border-bottom:1px solid #31578d;background:#102f63;color:#fff;text-align:left;padding:10px 13px;cursor:pointer}.customer-search-result:hover{background:#2563eb}.customer-search-result strong{display:block;font-size:13px}.customer-search-result small{color:#dbeafe}</style>
<style>#vatBillForm>.card:first-of-type{position:relative;z-index:20;overflow:visible!important}.customer-search-wrap{z-index:2100}.customer-search-menu{z-index:2200!important;max-height:280px;overflow-y:auto}.customer-search-result{min-height:52px;font-size:14px}.customer-search-result strong{color:#fff}.customer-search-result small{display:block;margin-top:3px}</style>
<style>.customer-search-wrap input[type="search"]{min-height:50px!important;font-size:17px!important;padding:10px 42px 10px 14px}.customer-search-menu{border-radius:12px}.customer-search-result{min-height:60px!important;padding:12px 15px!important}.customer-search-result strong{font-size:15px!important}.customer-search-result small{font-size:13px!important}</style>
<style>.customer-search-wrap{border-top:3px solid #138a59;border-radius:8px}.customer-search-wrap input[type="search"]{border-radius:0 0 8px 8px!important;padding-left:42px!important}.customer-search-icon{position:absolute;z-index:2;left:15px;top:17px;color:#174f86;font-size:16px}.customer-search-menu{background:#fff!important;border:1px solid #d6e1ef!important;border-top:0!important;border-radius:0 0 10px 10px!important;box-shadow:0 12px 25px #102f6322!important}.customer-search-result{background:#fff!important;color:#1e293b!important;border-bottom:1px solid #e7edf5!important}.customer-search-result:hover{background:#eef6ff!important}.customer-search-result strong{color:#1f2937!important}.customer-search-result small{color:#5d718e!important}.customer-search-menu .customer-search-result:last-child{border-bottom:0!important}</style>
<script>document.addEventListener('DOMContentLoaded',function(){const input=document.querySelector('input[type="search"][list="vatCustomerSuggestions"]'),list=document.getElementById('vatCustomerSuggestions'),select=document.querySelector('select[name="customer_id"]');if(!input||!list||!select)return;input.removeAttribute('list');const wrap=document.createElement('div');wrap.className='customer-search-wrap';input.parentNode.insertBefore(wrap,input);wrap.appendChild(input);const menu=document.createElement('div');menu.className='customer-search-menu';wrap.appendChild(menu);const options=Array.from(list.querySelectorAll('option'));const render=function(){const q=input.value.trim().toLowerCase();if(!q){menu.classList.remove('show');return}const matches=options.filter(o=>(o.dataset.search||o.value).toLowerCase().includes(q)).slice(0,8);menu.innerHTML=matches.length?matches.map(o=>'<button type="button" class="customer-search-result" data-id="'+o.dataset.id+'"><strong>'+o.value.split(' â€” PAN ')[0]+'</strong><small>'+o.value.substring(o.value.indexOf(' â€” PAN ')+3)+'</small></button>').join(''):'<div class="customer-search-result"><small>No matching customer found</small></div>';menu.classList.add('show')};input.addEventListener('input',render);input.addEventListener('focus',function(){if(input.value)render()});menu.addEventListener('mousedown',function(e){const button=e.target.closest('[data-id]');if(!button)return;e.preventDefault();const option=options.find(o=>o.dataset.id===button.dataset.id);select.value=button.dataset.id;input.value=option.value;menu.classList.remove('show')});document.addEventListener('click',function(e){if(!e.target.closest('.customer-search-wrap'))menu.classList.remove('show')});});</script>
<script>document.addEventListener('DOMContentLoaded',function(){const firm=@json($activeFirm);if(!firm)return;const set=(n,v)=>{const e=document.querySelector('[name="'+n+'"]');if(e)e.value=v||''};set('seller_name',firm.name);set('seller_pan_no',firm.pan_no);set('seller_address',firm.address);set('seller_phone',firm.phone);set('seller_vat_no','');set('seller_email','');const input=document.querySelector('[name="seller_name"]'),group=input?.closest('[class*="col-"]');if(!input||!group)return;group.querySelector('label')?.remove();input.style.display='none';const card=document.createElement('div');card.className='firm-display';card.innerHTML='<i class="fa fa-building"></i><span><small>Selected firm</small><br>'+firm.name+'</span>';group.prepend(card)});</script>
<script>document.addEventListener('DOMContentLoaded',function(){const input=document.querySelector('[name="seller_name"]'),group=input?.closest('[class*="col-"]');if(group&&group.querySelector('.firm-display'))group.className='col-12'});</script>
<script>document.addEventListener('DOMContentLoaded',function(){const input=document.querySelector('input[type="search"]'),menu=document.querySelector('.customer-search-menu');if(!input||!menu)return;const place=()=>{const box=input.getBoundingClientRect();menu.style.position='fixed';menu.style.left=box.left+'px';menu.style.top=(box.bottom+6)+'px';menu.style.width=box.width+'px';menu.style.right='auto'};input.addEventListener('input',()=>setTimeout(place));input.addEventListener('focus',()=>setTimeout(place));window.addEventListener('resize',place);window.addEventListener('scroll',place,true)});</script>
<script>document.addEventListener('DOMContentLoaded',function(){const wrap=document.querySelector('.customer-search-wrap'),input=wrap?.querySelector('input[type="search"]');if(!wrap||!input)return;const label=wrap.closest('[class*="col-"]')?.querySelector('label');if(label)label.textContent='Find VAT Customer *';const icon=document.createElement('i');icon.className='fa fa-search customer-search-icon';wrap.prepend(icon)});</script>
<style>
.customer-search-wrap{background:#fff;border:2px solid #2563eb;border-radius:12px;padding:3px;box-shadow:0 4px 12px #2563eb18}.customer-search-wrap:focus-within{border-color:#138a59;box-shadow:0 0 0 4px #138a5920}.customer-search-wrap input[type=search]{height:54px!important;border:0!important;box-shadow:none!important;border-radius:9px!important;font-size:18px!important;color:#173b72!important;padding-left:44px!important}.customer-search-wrap input[type=search]::-webkit-search-cancel-button{display:none}.customer-search-icon{left:16px!important;top:20px!important;color:#2563eb!important}.customer-search-menu{margin-top:3px!important}.customer-search-result{padding:13px 16px!important}.customer-search-result strong{font-size:16px!important}.customer-search-result small{font-size:13px!important}
</style>
<script>document.addEventListener('DOMContentLoaded',function(){const wrap=document.querySelector('.customer-search-wrap'),input=wrap?.querySelector('input[type=search]'),select=document.querySelector('select[name=customer_id]');if(!wrap||!input||!select)return;const options=Array.from(select.querySelectorAll('option'));const displayName=function(option){const value=option?.value||'';const marker=' â€” PAN ';return value.includes(marker)?value.split(marker)[0].trim():value.split(' Ã¢â‚¬â€ PAN ')[0].trim()};const current=options.find(o=>o.value===select.value);if(current)input.value=displayName(current);wrap.addEventListener('mousedown',function(e){const button=e.target.closest('.customer-search-result[data-id]');if(!button)return;const option=options.find(o=>o.dataset.id===button.dataset.id);if(option){select.value=option.dataset.id;input.value=displayName(option)}});input.addEventListener('input',function(){if(!input.value.trim())select.value=''})});</script>
<script>document.addEventListener('DOMContentLoaded',function(){const wrap=document.querySelector('.customer-search-wrap'),input=wrap?.querySelector('input[type=search]'),select=document.querySelector('select[name=customer_id]');if(!wrap||!input||!select)return;let selectedText=input.value.trim();input.addEventListener('focus',function(){if(input.value.trim()===selectedText)input.select()});input.addEventListener('input',function(){if(input.value.trim()!==selectedText)select.value=''});wrap.addEventListener('mousedown',function(e){if(e.target.closest('.customer-search-result[data-id]'))setTimeout(function(){selectedText=input.value.trim()},0)})});</script>
<script>document.addEventListener('DOMContentLoaded',function(){const wrap=document.querySelector('.customer-search-wrap'),input=wrap?.querySelector('input[type=search]'),menu=wrap?.querySelector('.customer-search-menu'),select=document.querySelector('select[name=customer_id]');if(!wrap||!input||!menu||!select)return;const options=Array.from(select.querySelectorAll('option'));let chosen=input.value.trim();const text=o=>o?.value||'';const render=function(q){q=q.trim().toLowerCase();if(!q){menu.classList.remove('show');return}const matches=options.filter(o=>(o.dataset.search||text(o)).toLowerCase().includes(q)).slice(0,8);menu.innerHTML=matches.length?matches.map(o=>'<button type="button" class="customer-search-result" data-id="'+o.dataset.id+'"><strong>'+text(o).split(' Ã¢â‚¬â€ PAN ')[0]+'</strong><small>'+text(o).substring(text(o).indexOf(' Ã¢â‚¬â€ PAN ')+3)+'</small></button>').join(''):'<div class="customer-search-result"><small>No matching customer found</small></div>';menu.classList.add('show')};input.addEventListener('input',function(){const typed=input.value;setTimeout(function(){if(typed.trim()!==chosen&&input.value.trim()!==typed.trim()){input.value=typed;select.value='';render(typed)}},0)});menu.addEventListener('mousedown',function(e){const b=e.target.closest('[data-id]');if(!b)return;const o=options.find(x=>x.dataset.id===b.dataset.id);if(o){select.value=o.dataset.id;input.value=text(o).split(' Ã¢â‚¬â€ PAN ')[0].trim();chosen=input.value;menu.classList.remove('show')}})});</script>
<style>.customer-search-wrap{position:relative!important}.customer-search-wrap .clean-customer-search{height:56px;width:100%;border:0;border-radius:9px;padding:10px 16px 10px 44px;font-size:18px;color:#173b72;outline:none}.customer-search-wrap .clean-customer-search:focus{box-shadow:0 0 0 3px #138a5940}.customer-search-wrap .clean-customer-menu{position:absolute;z-index:99999;left:0;right:0;top:calc(100% + 5px);background:#fff;border:1px solid #cbd9eb;border-radius:0 0 12px 12px;box-shadow:0 12px 28px #102f6330;overflow:hidden}.clean-customer-menu button{display:block;width:100%;padding:12px 16px;text-align:left;background:#fff;border:0;border-bottom:1px solid #e6edf5;color:#173b72;cursor:pointer}.clean-customer-menu button:hover{background:#eef6ff}.clean-customer-menu strong,.clean-customer-menu small{display:block}.clean-customer-menu strong{font-size:16px}.clean-customer-menu small{margin-top:3px;color:#64748b;font-size:13px}</style>
<script>document.addEventListener('DOMContentLoaded',function(){const wrap=document.querySelector('.customer-search-wrap'),select=document.querySelector('select[name=customer_id]');if(!wrap||!select)return;const options=Array.from(select.querySelectorAll('option')).filter(o=>o.value);const oldInput=wrap.querySelector('input[type=search]');const oldMenu=wrap.querySelector('.customer-search-menu');const icon=wrap.querySelector('.customer-search-icon');const cleanName=o=>(o?.value||'').split(' â€” PAN ')[0].split(' Ã¢â‚¬â€ PAN ')[0].trim();const details=o=>(o?.value||'').replace(cleanName(o),'').replace(/^\\s*[â€”Ã¢â‚¬â€-]\\s*/,'').trim();const selected=options.find(o=>o.value===select.value);wrap.innerHTML='';const newIcon=document.createElement('i');newIcon.className='fa fa-search customer-search-icon';wrap.appendChild(newIcon);const input=document.createElement('input');input.type='search';input.className='clean-customer-search';input.placeholder='Search customer by name, PAN or contact number';input.autocomplete='off';input.value=selected?cleanName(selected):'';wrap.appendChild(input);const menu=document.createElement('div');menu.className='clean-customer-menu';menu.style.display='none';wrap.appendChild(menu);function render(){const q=input.value.trim().toLowerCase();if(!q){menu.style.display='none';return}const matches=options.filter(o=>(cleanName(o)+' '+details(o)+' '+(o.dataset.search||'')).toLowerCase().includes(q)).slice(0,10);menu.innerHTML=matches.length?matches.map(o=>'<button type=\"button\" data-id=\"'+o.dataset.id+'\"><strong>'+cleanName(o)+'</strong><small>'+details(o)+'</small></button>').join(''):'<div style=\"padding:14px;color:#64748b\">No matching customer found</div>';menu.style.display='block'}input.addEventListener('input',function(){select.value='';render()});input.addEventListener('focus',render);menu.addEventListener('mousedown',function(e){const b=e.target.closest('[data-id]');if(!b)return;e.preventDefault();const o=options.find(x=>x.dataset.id===b.dataset.id);select.value=o.dataset.id;input.value=cleanName(o);menu.style.display='none'});document.addEventListener('click',function(e){if(!wrap.contains(e.target))menu.style.display='none'});});</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const wrap = document.querySelector('.customer-search-wrap');
    const select = document.querySelector('select[name="customer_id"]');
    const source = document.getElementById('vatCustomerSuggestions');
    if (!wrap || !select) return;

    // Search the customer text, not the numeric value used by the select.
    const options = source
        ? Array.from(source.querySelectorAll('option'))
        : Array.from(select.options).filter(option => option.value);
    const label = option => (option.dataset.name || option.value || option.textContent || '')
        .replace(/\s+/g, ' ').trim();
    const searchText = option => (option.dataset.search || label(option)).toLowerCase();
    const selected = options.find(option => option.dataset.id === select.value || option.value === select.value);

    wrap.innerHTML = '';
    const icon = document.createElement('i');
    icon.className = 'fa fa-search customer-search-icon';
    wrap.appendChild(icon);

    const input = document.createElement('input');
    input.type = 'search';
    input.className = 'clean-customer-search';
    input.placeholder = 'Search customer by name, PAN or contact number';
    input.autocomplete = 'off';
    input.value = selected ? (selected.dataset.name || selected.value).trim() : '';
    wrap.appendChild(input);

    const menu = document.createElement('div');
    menu.className = 'clean-customer-menu';
    menu.style.display = 'none';
    wrap.appendChild(menu);

    const render = function () {
        const query = input.value.trim().toLowerCase();
        if (!query) { menu.style.display = 'none'; return; }

        const matches = options.filter(option => searchText(option).includes(query)).slice(0, 10);
        menu.innerHTML = '';
        if (!matches.length) {
            const empty = document.createElement('div');
            empty.style.cssText = 'padding:14px;color:#64748b';
            empty.textContent = 'No matching customer found';
            menu.appendChild(empty);
        } else {
            matches.forEach(option => {
                const button = document.createElement('button');
                button.type = 'button';
                button.dataset.id = option.dataset.id || option.value;
                const strong = document.createElement('strong');
                strong.textContent = option.dataset.name || option.value;
                const small = document.createElement('small');
                small.textContent = option.dataset.details || option.dataset.search || option.textContent.trim();
                button.append(strong, small);
                menu.appendChild(button);
            });
        }
        menu.style.display = 'block';
    };

    input.addEventListener('input', function () { select.value = ''; render(); });
    input.addEventListener('focus', render);
    menu.addEventListener('mousedown', function (event) {
        const button = event.target.closest('[data-id]');
        if (!button) return;
        event.preventDefault();
        const option = options.find(item => (item.dataset.id || item.value) === button.dataset.id);
        if (!option) return;
        select.value = option.dataset.id || option.value;
        input.value = option.dataset.name || option.value;
        menu.style.display = 'none';
    });
    document.addEventListener('click', event => {
        if (!wrap.contains(event.target)) menu.style.display = 'none';
    });
});
</script>
<style>
.customer-search-wrap{position:relative!important;background:#fff!important;border:1px solid #2563eb!important;border-radius:10px!important;padding:0!important;box-shadow:0 2px 8px rgba(37,99,235,.12)!important;overflow:visible!important}
.vat-create .card-body,.vat-create .row,.customer-search-wrap{overflow:visible!important}
.customer-search-wrap:focus-within{border-color:#2563eb!important;box-shadow:0 0 0 3px rgba(37,99,235,.16)!important}
.customer-search-wrap .clean-customer-search{height:52px!important;min-height:52px!important;font-size:16px!important;font-weight:500;padding:10px 16px 10px 44px!important;color:#173b72!important}
.customer-search-wrap .clean-customer-search::placeholder{color:#8494aa!important;font-weight:400}
.customer-search-menu{top:calc(100% + 6px)!important;margin:0!important;background:#fff!important;border:1px solid #d5dfed!important;border-radius:10px!important;box-shadow:0 10px 24px rgba(15,47,87,.16)!important;overflow:hidden!important}
.clean-customer-menu{z-index:99999!important;max-height:260px!important;overflow-y:auto!important}
.clean-customer-menu button{padding:14px 17px!important;border:0!important;border-bottom:1px solid #e5edf7!important;transition:background .15s ease,transform .15s ease!important}
.clean-customer-menu button:hover{background:#eaf3ff!important}
.clean-customer-menu strong{font-size:17px!important;font-weight:800!important;color:#123b78!important;line-height:1.25!important}
.clean-customer-menu small{margin-top:5px!important;font-size:13px!important;color:#5d7290!important;line-height:1.25!important}
</style>
<style>
#vatBillForm>.firm-display{display:flex!important;width:100%;margin:0 0 14px!important;justify-content:center!important;text-align:center!important;position:relative!important}
#vatBillForm>.firm-display i{position:absolute!important;left:18px!important}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('vatBillForm');
    const firmCard = document.querySelector('#vatBillForm .firm-display');
    if (form && firmCard && form.firstElementChild !== firmCard) form.prepend(firmCard);
    document.querySelectorAll('#vatBillForm .firm-display').forEach(card => card.remove());
});
</script>
<style>
.vat-create .form-control.is-invalid,.vat-create .form-select.is-invalid{border:2px solid #dc3545!important;background:#fff7f7!important;box-shadow:0 0 0 3px rgba(220,53,69,.12)!important}
.vat-create .is-invalid:focus{border-color:#dc3545!important;box-shadow:0 0 0 3px rgba(220,53,69,.18)!important}
.vat-create label.field-invalid{color:#dc3545!important}
.customer-search-wrap.is-invalid{border:2px solid #dc3545!important;box-shadow:0 0 0 3px rgba(220,53,69,.12)!important}
.vat-create .client-validation-error{display:block;color:#dc3545;font-size:11px;font-weight:700;margin-top:4px}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('vatBillForm');
    if (!form) return;
    const customerSelect = form.querySelector('select[name="customer_id"]');
    const customerWrap = form.querySelector('.customer-search-wrap');
    const customerInput = customerWrap?.querySelector('input[type="search"]');

    const errorText = function (field) {
        if (field.name === 'bill_date_bs') return 'Enter a valid Nepali date (YYYY-MM-DD).';
        if (field.name?.includes('[unit]')) return 'Unit is required.';
        if (field.name?.includes('[quantity]')) return 'Enter a quantity greater than 0.';
        if (field.name?.includes('[rate]')) return 'Enter a valid rate.';
        return field.validationMessage || 'This field is required.';
    };

    const showError = function (field, message) {
        field.classList.add('is-invalid');
        field.closest('[class*="col-"]')?.querySelector('label')?.classList.add('field-invalid');
        let error = field.nextElementSibling;
        if (!error?.classList.contains('client-validation-error')) {
            error = document.createElement('small');
            error.className = 'client-validation-error';
            field.after(error);
        }
        error.textContent = message || errorText(field);
    };

    const clearError = function (field) {
        field.classList.remove('is-invalid');
        field.closest('[class*="col-"]')?.querySelector('label')?.classList.remove('field-invalid');
        if (field.nextElementSibling?.classList.contains('client-validation-error')) {
            field.nextElementSibling.remove();
        }
    };

    const validBsDate = function (value) {
        const match = String(value || '').trim().match(/^(\d{4})-(\d{1,2})-(\d{1,2})$/);
        if (!match) return false;
        const year = Number(match[1]);
        const month = Number(match[2]);
        const day = Number(match[3]);
        return year >= 1970 && year <= 2200 && month >= 1 && month <= 12 && day >= 1 && day <= 32;
    };

    const validateField = function (field) {
        if (field.name === 'bill_date_bs' && form.querySelector('[name="bill_date_mode"]')?.value === 'bs') {
            if (!validBsDate(field.value)) {
                showError(field);
                return false;
            }
            clearError(field);
            return true;
        }
        if (!field.checkValidity()) {
            showError(field);
            return false;
        }
        clearError(field);
        return true;
    };

    form.addEventListener('submit', function (event) {
        // Allow an exact customer name typed into the search to be submitted,
        // while still avoiding automatic selection during normal typing.
        if (customerSelect && !customerSelect.value && customerInput) {
            const typed = customerInput.value.trim().toLowerCase();
            const exact = Array.from(document.querySelectorAll('#vatCustomerSuggestions option'))
                .find(option => (option.dataset.name || '').trim().toLowerCase() === typed);
            if (exact) customerSelect.value = exact.dataset.id || '';
        }
        const bsInput = form.querySelector('[name="bill_date_bs"]');
        const fields = Array.from(form.querySelectorAll('input[required], select[required], textarea[required]'));
        if (bsInput && form.querySelector('[name="bill_date_mode"]')?.value === 'bs') fields.push(bsInput);
        let firstInvalid = null;
        fields.forEach(function (field) {
            if (!validateField(field) && !firstInvalid) firstInvalid = field;
        });
        const catalog = window.vatCatalog || [];
        form.querySelectorAll('#billItems .bill-row').forEach(function (row) {
            const itemField = row.querySelector('.item-name');
            const unitField = row.querySelector('input[name$="[unit]"]');
            const quantityField = row.querySelector('.qty');
            if (!itemField || !unitField || !quantityField) return;
            const itemName = itemField.value.trim().toLowerCase();
            const unit = unitField.value.trim().toLowerCase();
            const match = catalog.find(item => item.name.trim().toLowerCase() === itemName && item.unit.trim().toLowerCase() === unit);
            if (!match) {
                showError(itemField, 'Select an item from the VAT stock list.');
                if (!firstInvalid) firstInvalid = itemField;
            }
        });
        if (!firstInvalid) return;
        event.preventDefault();
        const focusTarget = firstInvalid === customerSelect ? customerInput : firstInvalid;
        customerWrap?.classList.toggle('is-invalid', firstInvalid === customerSelect);
        focusTarget?.focus();
        focusTarget?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    form.addEventListener('input', function (event) {
        if (!event.target.matches('input, select, textarea')) return;
        validateField(event.target);
        if (event.target === customerInput && customerSelect?.value) customerWrap?.classList.remove('is-invalid');
    });

    form.addEventListener('change', function (event) {
        if (event.target.matches('input, select, textarea')) validateField(event.target);
    });
});
</script>
<style>
#billItems{table-layout:fixed;width:100%}
#billItems th:nth-child(1),#billItems td:nth-child(1){width:4%;padding-left:8px;padding-right:8px}
#billItems th:nth-child(2),#billItems td:nth-child(2){width:22%}
#billItems th:nth-child(3),#billItems td:nth-child(3){width:12%}
#billItems th:nth-child(4),#billItems td:nth-child(4){width:9%}
#billItems th:nth-child(5),#billItems td:nth-child(5){width:8%}
#billItems th:nth-child(6),#billItems td:nth-child(6){width:11%}
#billItems th:nth-child(7),#billItems td:nth-child(7){width:10%}
#billItems th:nth-child(8),#billItems td:nth-child(8){width:12%}
#billItems th:nth-child(9),#billItems td:nth-child(9){width:5%;padding-left:4px;padding-right:4px;text-align:center}
#billItems td:nth-child(9) .remove-row{width:32px;height:32px;padding:0;font-size:13px}
#billItems~*{box-sizing:border-box}.vat-create #addBillRow{margin-left:auto!important;white-space:nowrap;display:inline-flex;align-items:center}
#billItems th:nth-child(7),#billItems td:nth-child(7){width:12%}
#billItems th:nth-child(8),#billItems td:nth-child(8){width:6%;text-align:center}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('billItems');
    if (!table) return;
    table.querySelectorAll('tr').forEach(function (row) {
        const taxable = row.children[6];
        const amount = row.children[7];
        if (taxable && amount) amount.after(taxable);
    });
});
</script>
<style>
    .vat-date-mode-row{display:flex;flex-wrap:wrap;gap:8px;align-items:center}
    .vat-date-mode-row select{max-width:145px;flex:0 0 145px}
    .vat-date-mode-row input{flex:1 1 180px;min-width:0}
    .vat-date-mode-row .client-validation-error{flex:0 0 100%;margin-left:153px;margin-top:-3px}
    .vat-date-help{display:block;margin-top:5px;color:#52719a;font-size:11px;font-weight:700}
    @media(max-width:600px){.vat-date-mode-row{display:block}.vat-date-mode-row select{max-width:none;width:100%;margin-bottom:7px}.vat-date-mode-row input{width:100%}.vat-date-mode-row .client-validation-error{margin-left:0}}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const adInput = document.querySelector('.vat-create [name="bill_date"]');
    if (!adInput) return;

    const dateGroup = adInput.closest('[class*="col-"]');
    if (!dateGroup || dateGroup.querySelector('.vat-date-mode-row')) return;

    const initialBsDate = @json($initialBsDate);
    const initialMode = @json($initialBillDateMode);
    const modeSelect = document.createElement('select');
    modeSelect.name = 'bill_date_mode';
    modeSelect.className = 'form-select';
    modeSelect.innerHTML = '<option value="ad">English (A.D.)</option><option value="bs">Nepali (B.S.)</option>';

    const bsInput = document.createElement('input');
    bsInput.type = 'text';
    bsInput.name = 'bill_date_bs';
    bsInput.className = 'form-control';
    bsInput.placeholder = 'YYYY-MM-DD (e.g. 2083-06-31)';
    bsInput.inputMode = 'numeric';
    bsInput.maxLength = 10;
    bsInput.value = initialBsDate;

    const wrapper = document.createElement('div');
    wrapper.className = 'vat-date-mode-row';
    adInput.parentNode.insertBefore(wrapper, adInput);
    wrapper.appendChild(modeSelect);
    wrapper.appendChild(adInput);
    wrapper.appendChild(bsInput);

    const help = document.createElement('small');
    help.className = 'vat-date-help';
    dateGroup.appendChild(help);

    function syncDateMode() {
        const nepali = modeSelect.value === 'bs';
        adInput.style.display = nepali ? 'none' : '';
        bsInput.style.display = nepali ? '' : 'none';
        help.textContent = nepali
            ? 'Enter Nepali date (B.S.). It will be converted automatically when saved.'
            : 'Date is saved internally in English (A.D.) format.';
    }

    modeSelect.value = initialMode === 'bs' ? 'bs' : 'ad';
    modeSelect.addEventListener('change', syncDateMode);
    syncDateMode();
});
</script>
<script>window.vatCatalog={!! $catalogJson !!};document.addEventListener('DOMContentLoaded',function(){const catalog=window.vatCatalog||[];const fillInfo=function(row,item){if(!row||!item)return;const sale=item.sale_price??item.price;const saleField=row.querySelector('.sale-price');const purchaseField=row.querySelector('.purchase-price');const stockField=row.querySelector('.stock-available');if(saleField)saleField.value=sale??'';if(purchaseField)purchaseField.value=item.purchase_price??'';if(stockField)stockField.value=item.stock_quantity===null?'Not tracked':Number(item.stock_quantity).toFixed(3)};document.querySelector('#billItems tbody').addEventListener('mousedown',function(event){const button=event.target.closest('.item-suggestion');if(button)fillInfo(button.closest('.item-cell')?.closest('.bill-row'),catalog[Number(button.dataset.index)])});document.querySelector('#billItems tbody').addEventListener('change',function(event){if(!event.target.classList.contains('item-name'))return;const item=catalog.find(i=>i.name.toLowerCase()===event.target.value.trim().toLowerCase());fillInfo(event.target.closest('.bill-row'),item)});document.querySelectorAll('#billItems .item-name').forEach(function(input){const item=catalog.find(i=>i.name.toLowerCase()===input.value.trim().toLowerCase());fillInfo(input.closest('.bill-row'),item)})});</script>
<style>#billItems{min-width:0}</style>
<script>document.addEventListener('DOMContentLoaded',function(){const table=document.getElementById('billItems');if(!table)return;table.querySelectorAll('tr').forEach(function(row){[5,4,3,2].forEach(function(index){row.children[index]?.remove()})})});</script>
<script>document.addEventListener('mousedown',function(event){if(!event.target.closest('.item-suggestion'))return;setTimeout(function(){document.querySelectorAll('.item-suggestion-menu').forEach(function(menu){menu.classList.remove('show')});},0)},true);</script>
<div class="modal fade" id="extraCustomerModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Extra Customer</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form id="extraCustomerForm"><div class="modal-body"><div id="extraCustomerError" class="alert alert-danger d-none"></div><div class="mb-3"><label class="form-label">Customer Name *</label><input name="name" class="form-control" required></div><div class="mb-3"><label class="form-label">Address</label><input name="address" class="form-control"></div><div class="mb-3"><label class="form-label">Contact Name</label><input name="contact_name" class="form-control"></div><div class="mb-3"><label class="form-label">Notes</label><textarea name="notes" rows="3" class="form-control"></textarea></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i>Save and Select</button></div></form></div></div></div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.querySelector('select[name="customer_id"]');
    const open = document.getElementById('openExtraCustomer');
    const form = document.getElementById('extraCustomerForm');
    if (!select || !open || !form || typeof bootstrap === 'undefined') return;
    let extra = document.querySelector('input[name="extra_customer_id"]');
    if (!extra) { extra = document.createElement('input'); extra.type = 'hidden'; extra.name = 'extra_customer_id'; select.form.appendChild(extra); }
    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('extraCustomerModal'));
    const searchInput = () => document.querySelector('.clean-customer-search, .customer-search-wrap input[type="search"]');
    @if(!empty($extraCustomer))
    const oldOption = new Option(@json($extraCustomer->name.' (Extra customer)'), '');
    oldOption.dataset.extraId = @json((string) $extraCustomer->id);
    select.add(oldOption); extra.value = @json((string) $extraCustomer->id); select.value = ''; select.required = false;
    if (searchInput()) searchInput().value = @json($extraCustomer->name);
    @endif
    const existing = @json($extraCustomerPayload);
    const endpoint = @json($extraCustomerEndpoint);
    if (existing) {
        const input = searchInput();
        const field = input?.closest('[class*="col-"]');
        if (field && !document.getElementById('visibleEditExtraCustomer')) {
            const editButton = document.createElement('button');
            editButton.type = 'button'; editButton.id = 'visibleEditExtraCustomer';
            editButton.className = 'btn btn-outline-primary btn-sm mt-2 fw-bold';
            editButton.innerHTML = '<i class="fa fa-pen me-1"></i>Edit Extra Customer Name and Details';
            editButton.addEventListener('click', () => open.click());
            field.appendChild(editButton);
        }
    }
    open.addEventListener('click', () => { form.reset(); if (existing) { Object.keys(existing).filter(k => k !== 'id').forEach(k => { const field = form.elements[k]; if (field) field.value = existing[k] || ''; }); } document.getElementById('extraCustomerError').classList.add('d-none'); modal.show(); });
    select.addEventListener('change', () => { if (select.value) { extra.value = ''; select.required = true; } });
    document.querySelector('.clean-customer-search, .customer-search-wrap input[type="search"]')?.addEventListener('input', () => { extra.value = ''; select.required = true; });
    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        const error = document.getElementById('extraCustomerError'); error.classList.add('d-none');
        const button = form.querySelector('button[type="submit"]'); button.disabled = true;
        try {
            const payload = new FormData(form);
            if (existing) payload.append('_method', 'PUT');
            const response = await fetch(endpoint, { method: 'POST', headers: {'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'Accept': 'application/json'}, body: payload });
            const data = await response.json(); if (!response.ok) throw new Error(data.message || Object.values(data.errors || {}).flat().join(' ') || 'Could not save extra customer.');
            let option = Array.from(select.options).find(item => item.dataset.extraId === String(data.id));
            if (option) option.textContent = data.name + ' (Extra customer)'; else { option = new Option(data.name + ' (Extra customer)', ''); option.dataset.extraId = String(data.id); select.add(option); }
            select.value = ''; extra.value = data.id; select.required = false;
            const input = searchInput(); if (input) input.value = data.name;
            if (existing) Object.assign(existing, data);
            modal.hide();
        } catch (e) { error.textContent = e.message; error.classList.remove('d-none'); }
        button.disabled = false;
    });
});
</script>

<style>
/* Final visual layer: a calmer, clearer invoice workflow. */
.vat-create {
    background: radial-gradient(circle at 90% 0%, #e4efff 0, transparent 36%), #f4f7fb;
    padding-bottom: 42px;
}
.vat-create > .container-fluid { max-width: 1540px; margin: 0 auto; }
.vat-create .create-page-heading {
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) minmax(260px, auto) auto;
    gap: 22px;
    padding: 8px 2px 22px;
    align-items: center;
}
.vat-create .create-page-heading h1 {
    color: #142f5d;
    font-size: clamp(28px, 3vw, 42px);
    letter-spacing: -1px;
    line-height: 1.05;
}
.vat-create .create-page-heading p { color: #6d7f99 !important; font-size: 15px; }
.vat-create .selected-firm-heading {
    position: static !important;
    transform: none !important;
    min-width: 260px;
    padding: 10px 18px;
    border: 1px solid #cfe0f7;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 8px 22px rgba(23,59,114,.08);
}
.vat-create .selected-firm-heading small { color: #7387a3; font-size: 10px; }
.vat-create .selected-firm-heading strong { color: #173b72; font-size: 17px; white-space: normal; }
.vat-create .create-page-heading > .d-flex:last-child { justify-content: flex-end; }
.vat-create .create-page-heading > .d-flex:last-child .btn { border-radius: 11px; padding: 11px 15px; }
.vat-create .card {
    border: 1px solid #e1eaf5;
    border-radius: 18px;
    box-shadow: 0 12px 30px rgba(23,59,114,.08);
    overflow: hidden;
}
.vat-create .card-header {
    min-height: 64px;
    padding: 16px 22px;
    display: flex;
    align-items: center;
    background: linear-gradient(105deg, #173b72, #2457a6) !important;
    color: #fff;
    letter-spacing: .1px;
}
.vat-create .card-header button { border: 0; border-radius: 9px; padding: 9px 14px; color: #173b72; }
.vat-create .card-body { padding: 25px 23px; }
.vat-create label { color: #536b88; font-size: 11px; letter-spacing: .55px; margin-bottom: 7px; }
.vat-create .form-control, .vat-create .form-select {
    min-height: 45px;
    border: 1px solid #cfdaea;
    border-radius: 10px;
    background: #fbfdff;
    color: #1c385e;
    transition: border-color .18s, box-shadow .18s, background .18s;
}
.vat-create .form-control:hover, .vat-create .form-select:hover { background: #fff; border-color: #a9bfdb; }
.vat-create .form-control:focus, .vat-create .form-select:focus {
    background: #fff;
    border-color: #2877e8;
    box-shadow: 0 0 0 4px rgba(40,119,232,.12);
}
.vat-create .firm-display { border-radius: 12px; box-shadow: inset 4px 0 #2877e8; }
.vat-create #billItems { min-width: 920px; border-collapse: separate; border-spacing: 0; }
.vat-create #billItems thead th {
    background: #eef4fc !important;
    color: #3d5879 !important;
    border-bottom: 1px solid #d8e3f1;
    font-size: 10px;
    letter-spacing: .7px;
    padding: 14px 10px;
    text-transform: uppercase;
}
.vat-create #billItems tbody td { border-bottom: 1px solid #e8eef6; padding: 13px 9px; background: #fff; }
.vat-create #billItems tbody tr:hover td { background: #f8fbff; }
.vat-create #billItems .sn { color: #7590b0; font-weight: 900; text-align: center; }
.vat-create #billItems .amount { background: #f1f6fc; color: #173b72; font-weight: 800; }
.vat-create #billItems .taxable { width: 18px; height: 18px; accent-color: #2563eb; }
.vat-create #billItems .remove-row { border-radius: 9px; width: 36px; height: 36px; padding: 0; }
.vat-create .total-box {
    border: 1px solid #d6e3f2;
    border-radius: 14px;
    background: linear-gradient(145deg, #f8fbff, #eef5fd);
    color: #506783;
}
.vat-create .total-box strong { color: #173b72; }
.vat-create #grandTotal { color: #138a59; font-size: 24px; }
.vat-create .card-footer { background: #fff !important; padding: 0 23px 23px !important; }
.vat-create .card-footer .btn-primary {
    border: 0;
    border-radius: 12px;
    background: linear-gradient(110deg, #1769d5, #234bc0);
    box-shadow: 0 8px 18px rgba(35,75,192,.22);
}
.vat-create .card-footer .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 11px 22px rgba(35,75,192,.3); }
.vat-create .col-lg-5 > .card { position: sticky; top: 88px; }
.vat-create textarea.form-control { min-height: 145px; resize: vertical; }
@media (max-width: 1050px) {
    .vat-create .create-page-heading { grid-template-columns: 1fr auto; }
    .vat-create .selected-firm-heading { grid-column: 1 / -1; grid-row: 2; text-align: left; }
}
@media (max-width: 700px) {
    .vat-create .create-page-heading { display: flex !important; flex-wrap: wrap; gap: 12px; }
    .vat-create .create-page-heading > div:first-child { flex: 1 1 100%; }
    .vat-create .selected-firm-heading { width: 100%; min-width: 0; }
    .vat-create .create-page-heading > .d-flex:last-child { width: 100%; justify-content: flex-start; }
    .vat-create .card-body { padding: 18px 14px; }
    .vat-create .card-header { padding: 13px 15px; min-height: 56px; }
    .vat-create .col-lg-5 > .card { position: static; }
}
</style>

<style>
/* Party information: make the primary fields balanced and easy to scan. */
.vat-create #vatBillForm > .card:first-of-type .card-header {
    min-height: 70px;
    background: linear-gradient(105deg, #173b72 0%, #2459ac 100%) !important;
    letter-spacing: .15px;
}
.vat-create #vatBillForm > .card:first-of-type .card-header::before {
    content: '\f2bb';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    margin-right: 12px;
    color: #bfdbfe;
    font-size: 18px;
}
.vat-create #vatBillForm > .card:first-of-type .card-header button {
    background: rgba(255,255,255,.98);
    color: #17427e;
    border: 1px solid rgba(255,255,255,.65);
    box-shadow: 0 4px 12px rgba(7,31,72,.18);
    transition: transform .18s ease, box-shadow .18s ease;
}
.vat-create #vatBillForm > .card:first-of-type .card-header button:hover {
    transform: translateY(-1px);
    box-shadow: 0 7px 16px rgba(7,31,72,.25);
}
.vat-create #vatBillForm > .card:first-of-type .card-body {
    padding: 20px 29px 21px;
    background: linear-gradient(180deg, #fff 0%, #fbfdff 100%);
}
.vat-create #vatBillForm > .card:first-of-type .card-body > .row {
    display: grid !important;
    grid-template-columns: minmax(340px, 1.55fr) minmax(150px, .7fr) minmax(340px, 1.35fr);
    gap: 24px !important;
    align-items: start;
    margin-top: 0 !important;
    margin-bottom: 0 !important;
}
.vat-create #vatBillForm > .card:first-of-type .card-body > .row > .col-md-4:has(.customer-search-wrap),
.vat-create #vatBillForm > .card:first-of-type .card-body > .row > .col-md-2,
.vat-create #vatBillForm > .card:first-of-type .card-body > .row > .col-md-3:last-child {
    width: auto !important;
    padding: 0 !important;
}
.vat-create #vatBillForm > .card:first-of-type .card-body > .row > .col-md-4:has(.customer-search-wrap) { grid-column: 1; }
.vat-create #vatBillForm > .card:first-of-type .card-body > .row > .col-md-2 { grid-column: 2; }
.vat-create #vatBillForm > .card:first-of-type .card-body > .row > .col-md-3:last-child { grid-column: 3; }
.vat-create #vatBillForm > .card:first-of-type label {
    display: block;
    margin-bottom: 9px;
    color: #3e5d82;
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .8px;
}
.vat-create #vatBillForm > .card:first-of-type .customer-search-wrap,
.vat-create #vatBillForm > .card:first-of-type input[name="bill_no"] {
    width: 100%;
}
.vat-create #vatBillForm > .card:first-of-type input[name="bill_no"] { min-height: 58px; font-size: 18px; }
.vat-create #vatBillForm > .card:first-of-type .customer-search-wrap .clean-customer-search { height: 58px !important; }
.vat-create #vatBillForm > .card:first-of-type .vat-date-mode-row {
    display: grid;
    grid-template-columns: 145px minmax(180px, 1fr);
    gap: 9px;
    align-items: start;
}
.vat-create #vatBillForm > .card:first-of-type .vat-date-mode-row select,
.vat-create #vatBillForm > .card:first-of-type .vat-date-mode-row input { width: 100%; max-width: none; min-height: 58px; }
.vat-create #vatBillForm > .card:first-of-type .vat-date-mode-row .client-validation-error {
    grid-column: 1 / -1;
    margin: 0;
}
.vat-create #vatBillForm > .card:first-of-type .vat-date-help {
    margin-top: 8px;
    color: #66809f;
    font-size: 11px;
    line-height: 1.35;
}
@media (max-width: 950px) {
    .vat-create #vatBillForm > .card:first-of-type .card-body > .row { grid-template-columns: minmax(0, 1fr) minmax(150px, .55fr); }
    .vat-create #vatBillForm > .card:first-of-type .card-body > .row > .col-md-4:has(.customer-search-wrap) { grid-column: 1 / -1; }
    .vat-create #vatBillForm > .card:first-of-type .card-body > .row > .col-md-2 { grid-column: 1; }
    .vat-create #vatBillForm > .card:first-of-type .card-body > .row > .col-md-3:last-child { grid-column: 2; }
}
@media (max-width: 600px) {
    .vat-create #vatBillForm > .card:first-of-type .card-body { padding: 18px 16px 18px; }
    .vat-create #vatBillForm > .card:first-of-type .card-body > .row { display: block !important; }
    .vat-create #vatBillForm > .card:first-of-type .card-body > .row > div { margin-bottom: 18px; }
    .vat-create #vatBillForm > .card:first-of-type .vat-date-mode-row { display: block; }
    .vat-create #vatBillForm > .card:first-of-type .vat-date-mode-row select { margin-bottom: 8px; }
}
</style>
<style>
/* Larger, bolder product suggestions for faster item selection. */
.vat-create .item-suggestion-menu { width: min(520px, calc(100vw - 40px)) !important; border-radius: 13px !important; }
.vat-create .item-suggestion { padding: 14px 16px !important; min-height: 67px; }
.vat-create .item-suggestion strong { font-size: 18px !important; line-height: 1.3; font-weight: 900 !important; letter-spacing: .05px; }
.vat-create .item-suggestion small { display: block; margin-top: 6px; font-size: 14px !important; line-height: 1.4; font-weight: 700; color: #dbeafe !important; }
.vat-create .item-suggestion:hover strong { color: #fff; }
.vat-create #billItems .item-name { font-size: 18px; font-weight: 700; }
.vat-create #billItems .rate, .vat-create #billItems .unit-select, .vat-create #billItems .qty { font-size: 17px; font-weight: 600; }
</style>
<style>
.bill-items-actions { display:flex; gap:8px; align-items:center; }
.bill-items-actions .btn { border-radius:9px; font-weight:800; }
.margin-control { display:flex; align-items:center; gap:10px; padding:12px 20px; background:#f3f8ff; border-bottom:1px solid #dbe7f5; color:#31547e; }
.margin-control label { margin:0; color:#31547e; font-size:12px; font-weight:900; text-transform:none; }
.margin-control input { width:105px; min-height:38px; border:1px solid #b8cde7; border-radius:8px; padding:6px 10px; font-weight:800; color:#173b72; }
.margin-control span { font-size:11px; color:#7186a1; font-weight:700; }
.original-rate { display:block; margin-top:4px; color:#6b83a5; font-size:11px; font-weight:800; white-space:nowrap; }
@media(max-width:600px) { .bill-items-actions { gap:5px; } .bill-items-actions .btn { padding:7px 8px; font-size:11px; } .margin-control { flex-wrap:wrap; padding:10px 14px; } .margin-control span { flex-basis:100%; } }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('billItems');
    const marginButton = document.getElementById('toggleMargin');
    const marginControl = document.getElementById('marginControl');
    const marginInput = document.getElementById('marginPercent');
    if (!table || !marginButton || !marginControl || !marginInput) return;
    const body = table.querySelector('tbody');
    const currentMargin = () => Math.max(0, Number(marginInput.value) || 0);
    const updateOriginalRate = row => {
        const rate = row?.querySelector('.rate');
        const original = row?.querySelector('.original-rate');
        if (!rate || !original) return;
        const base = Number(row.dataset.baseRate ?? rate.value) || 0;
        original.textContent = 'Original: ' + base.toFixed(2);
        original.hidden = marginControl.hidden;
    };
    const applyMargin = row => {
        const rate = row?.querySelector('.rate');
        if (!rate || rate.value === '') return;
        if (row.dataset.baseRate === undefined) row.dataset.baseRate = rate.value;
        const base = Number(row.dataset.baseRate) || 0;
        rate.value = (base * (1 + currentMargin() / 100)).toFixed(2);
        updateOriginalRate(row);
    };
    marginButton.addEventListener('click', function () {
        const open = marginControl.hidden;
        marginControl.hidden = !open;
        marginButton.classList.toggle('active', open);
        body.querySelectorAll('.bill-row').forEach(row => updateOriginalRate(row));
        if (open) marginInput.focus();
    });
    marginInput.addEventListener('input', function () {
        body.querySelectorAll('.bill-row').forEach(applyMargin);
        body.dispatchEvent(new Event('input', { bubbles: true }));
    });
    body.addEventListener('mousedown', function (event) {
        if (!event.target.closest('.item-suggestion')) return;
        const row = event.target.closest('.bill-row');
        setTimeout(() => { applyMargin(row); body.dispatchEvent(new Event('input', { bubbles: true })); }, 0);
    });
    body.addEventListener('change', function (event) {
        if (event.target.classList.contains('item-name')) {
            const row = event.target.closest('.bill-row');
            setTimeout(() => { applyMargin(row); body.dispatchEvent(new Event('input', { bubbles: true })); }, 0);
        }
    });
    body.querySelectorAll('.bill-row').forEach(row => {
        const rate = row.querySelector('.rate');
        if (rate?.value) row.dataset.baseRate = (Number(rate.value) / (1 + currentMargin() / 100)).toFixed(2);
    });
});
</script>
<style>
/* Keep the party card compact so the item-entry rows stay above the fold. */
@media (min-width: 951px) {
    .vat-create #vatBillForm > .card:first-of-type .card-body > .row {
        position: relative;
        top: -38px;
        margin-bottom: -38px !important;
    }
}
@media (min-width: 601px) and (max-width: 950px) {
    .vat-create #vatBillForm > .card:first-of-type .card-body > .row {
        position: relative;
        top: -24px;
        margin-bottom: -24px !important;
    }
}
@media (max-width: 600px) {
    .vat-create #vatBillForm > .card:first-of-type .card-body > .row {
        position: relative;
        top: -10px;
        margin-bottom: -10px !important;
    }
}
</style>
<style>
/* Compact invoice header and party fields without changing functionality. */
.vat-create .create-page-heading h1 {
    white-space: nowrap;
    font-size: clamp(25px, 2.45vw, 38px);
    letter-spacing: -.7px;
}
.vat-create #vatBillForm > .card:first-of-type {
    border: 0;
    border-radius: 16px;
    box-shadow: 0 8px 22px rgba(23,59,114,.06);
    background: transparent;
    overflow: visible;
    margin-bottom: 18px !important;
}
.vat-create #vatBillForm > .card:first-of-type .card-header { display: none !important; }
.vat-create #vatBillForm > .card:first-of-type .card-body {
    padding: 21px 26px 20px;
    border: 1px solid #dfe8f3;
    border-radius: 16px;
    background: linear-gradient(180deg, #fff 0%, #fbfdff 100%);
}
.vat-create #vatBillForm > .card:first-of-type .card-body > .row { margin-top: 0 !important; }
.vat-create #vatBillForm > .card:nth-of-type(2) { margin-top: 0 !important; }
@media (max-width: 1050px) {
    .vat-create .create-page-heading h1 { font-size: clamp(24px, 3.5vw, 34px); }
}
@media (max-width: 600px) {
    .vat-create .create-page-heading h1 { font-size: 25px; }
    .vat-create #vatBillForm > .card:first-of-type .card-body { padding: 17px 15px 16px; }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const repositionSuggestions = function () {
        document.querySelectorAll('.item-suggestion-menu.show').forEach(function (menu) {
            const input = menu.closest('.item-cell')?.querySelector('.item-name');
            if (!input) return;
            const box = input.getBoundingClientRect();
            menu.style.position = 'fixed';
            menu.style.left = box.left + 'px';
            menu.style.top = (box.bottom + 5) + 'px';
            menu.style.width = Math.max(300, box.width) + 'px';
            menu.style.maxHeight = Math.max(150, window.innerHeight - box.bottom - 15) + 'px';
        });
    };
    window.addEventListener('scroll', repositionSuggestions, true);
    window.addEventListener('resize', repositionSuggestions);
    document.addEventListener('input', function (event) {
        if (event.target.classList.contains('item-name')) setTimeout(repositionSuggestions, 0);
    });
});
</script>
@endsection
