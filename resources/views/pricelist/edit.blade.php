@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')


<div class="main-content price-edit-page">

@yield('breadcrumb')


<div class="container price-edit-container">


<form class="price-edit-card" action="{{route('pricelists.update',$pricelistdata->id)}}" method="post">
                @csrf
                @method('put')
                <div class="price-edit-fields">
                <div class="col-md-6">
                    <label for="itemname" class="form-label">Item Name</label>
                    <input type="text" class="form-control @error('itemname') is-invalid @enderror" 
                        name="itemname" id="itemname" value="{{ old('itemname',$pricelistdata->itemname) }}" required>
                    @error('itemname')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
            <div class="col-md-6">
                    <label for="unit" class="form-label">Unit</label>
                    <select class="form-select @error('unit') is-invalid @enderror" name="unit" id="unit">
                        <option value="">select</option>
                        @foreach (['pcs', 'kg', 'feet', 'mtr'] as $unit)
                            <option value="{{ $unit }}" @selected(old('unit', $pricelistdata->unit ?? '') === $unit)>{{ $unit }}</option>
                        @endforeach
                    </select>
                    @error('unit')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
            <div class="col-md-6">
                    <label for="costprice" class="form-label">Cost Price</label>
                    <div class="price-edit-input"><span>Rs.</span><input type="number" step="0.01" min="0" id="costprice" class="form-control @error('costprice') is-invalid @enderror"
                        name="costprice" value="{{ old('costprice',$pricelistdata->costprice) }}"></div>
                    @error('costprice')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                    <label for="saleprice" class="form-label">MRP</label>
                    <div class="price-edit-input"><span>Rs.</span><input type="number" step="0.01" min="0" id="saleprice" class="form-control @error('saleprice') is-invalid @enderror"
                        name="saleprice" value="{{ old('saleprice',$pricelistdata->saleprice) }}"></div>
                    @error('saleprice')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                    <label for="wholesaleprice" class="form-label">Wholesale Price</label>
                    <div class="price-edit-input"><span>Rs.</span><input type="number" step="0.01" min="0" id="wholesaleprice" class="form-control @error('wholesaleprice') is-invalid @enderror"
                        name="wholesaleprice" value="{{ old('wholesaleprice',$pricelistdata->wholesaleprice) }}"></div>
                    @error('wholesaleprice')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

           

            <div class="col-md-6">
                    <label for="note" class="form-label">Note <small>(optional)</small></label>
                    <input type="text" class="form-control @error('note') is-invalid @enderror" 
                        name="note" id="note" value="{{ old('note',$pricelistdata->note) }}" placeholder="Add a note if needed">
                    @error('note')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
            </div>

            <div class="price-edit-actions">
                    <a href="{{ route('pricelists.index') }}" class="price-cancel-btn">Cancel</a>
                    <button type="submit" class="price-update-btn"><i class="fa-solid fa-check"></i> Update Price</button>
            </div>
</form>
</div>


</div>
<style>
    .price-edit-page { min-height: calc(100vh - 80px); }
    .price-edit-container { max-width: 1120px; padding: 0; width: 100%; }
    .price-edit-card { background: #fff; border: 1px solid #dbe3ef; border-radius: 14px; box-shadow: 0 14px 32px rgba(15,23,42,.08); overflow: hidden; }
    .price-edit-header { align-items: center; background: linear-gradient(135deg,#f8fafc,#eef4ff); border-bottom: 1px solid #dbe3ef; display: flex; gap: 14px; padding: 22px 26px; }
    .price-edit-icon { align-items: center; background: #4f46e5; border-radius: 11px; color: #fff; display: inline-flex; flex: 0 0 46px; font-size: 19px; height: 46px; justify-content: center; }
    .price-edit-header small { color: #64748b; font-size: 11px; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; }
    .price-edit-header h2 { color: #172033; font-size: 22px; font-weight: 900; margin: 2px 0 3px; }
    .price-edit-header p { color: #64748b; font-size: 13px; margin: 0; }
    .price-edit-fields { display: grid; gap: 20px 34px; grid-template-columns: repeat(2,minmax(0,1fr)); padding: 26px; }
    .price-edit-fields .col-md-6 { width: auto; }
    .price-edit-fields .form-label { color: #334155; font-size: 13px; font-weight: 900; margin-bottom: 7px; }
    .price-edit-fields .form-label small { color: #94a3b8; font-weight: 700; }
    .price-edit-fields .form-control, .price-edit-fields .form-select { border: 1px solid #cbd5e1; border-radius: 9px; min-height: 44px; padding: 9px 12px; }
    .price-edit-fields .form-control:focus, .price-edit-fields .form-select:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.14); }
    .price-edit-input { align-items: stretch; display: flex; }
    .price-edit-input span { align-items: center; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 9px 0 0 9px; color: #475569; display: flex; font-size: 13px; font-weight: 900; padding: 0 12px; }
    .price-edit-input .form-control { border-radius: 0 9px 9px 0; flex: 1; }
    .price-edit-actions { align-items: center; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; gap: 10px; justify-content: flex-end; padding: 16px 26px; }
    .price-cancel-btn, .price-update-btn { border-radius: 9px; font-size: 14px; font-weight: 900; min-height: 42px; padding: 10px 18px; text-decoration: none; }
    .price-cancel-btn { border: 1px solid #cbd5e1; color: #475569; }
    .price-update-btn { background: #2563eb; border: 0; color: #fff; }
    .price-update-btn:hover { background: #1d4ed8; color: #fff; }
    @media (min-width: 701px) {
        .price-edit-container { max-width: none !important; width: calc(100vw - 332px) !important; margin-left: 0 !important; margin-right: 0 !important; }
        .price-edit-card { width: 100%; }
        .price-edit-header { padding: 28px 34px; }
        .price-edit-fields { gap: 24px 42px; padding: 34px; }
        .price-edit-fields .form-label { font-size: 16px; font-weight: 900; }
        .price-edit-fields .form-control, .price-edit-fields .form-select { font-size: 18px; font-weight: 700; min-height: 54px; }
        .price-edit-input span { font-size: 16px; font-weight: 900; }
        .price-edit-header h2 { font-size: 28px; font-weight: 900; }
        .price-edit-header p { font-size: 15px; }
        .price-edit-actions { justify-content: center; padding: 24px 34px; position: relative; }
        .price-cancel-btn { font-size: 17px; font-weight: 900; min-height: 52px; padding: 12px 26px; position: absolute; right: 34px; }
        .price-update-btn { font-size: 24px; font-weight: 900; min-height: 76px; min-width: 440px; padding: 18px 42px; }
    }
    @media (max-width: 700px) { .price-edit-container { padding: 0 12px; } .price-edit-fields { grid-template-columns: 1fr; padding: 18px; } .price-edit-actions { flex-direction: column-reverse; } .price-cancel-btn, .price-update-btn { text-align: center; width: 100%; } }
</style>
@stop
