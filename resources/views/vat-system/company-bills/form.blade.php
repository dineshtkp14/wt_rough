@extends('layouts.master')
@section('content')
<style>.company-bill-form .purchase-items-table{width:100%;min-width:1050px;table-layout:fixed}.company-bill-form .purchase-items-table th,.company-bill-form .purchase-items-table td{vertical-align:middle}.company-bill-form .purchase-items-table th:nth-child(1),.company-bill-form .purchase-items-table td:nth-child(1){width:6% !important}.company-bill-form .purchase-items-table th:nth-child(2),.company-bill-form .purchase-items-table td:nth-child(2){width:24% !important}.company-bill-form .purchase-items-table th:nth-child(3),.company-bill-form .purchase-items-table td:nth-child(3){width:15% !important}.company-bill-form .purchase-items-table th:nth-child(4),.company-bill-form .purchase-items-table td:nth-child(4){width:12% !important}.company-bill-form .purchase-items-table th:nth-child(5),.company-bill-form .purchase-items-table td:nth-child(5){width:12% !important}.company-bill-form .purchase-items-table th:nth-child(6),.company-bill-form .purchase-items-table td:nth-child(6){width:14% !important}.company-bill-form .purchase-items-table th:nth-child(7),.company-bill-form .purchase-items-table td:nth-child(7){width:9% !important}.company-bill-form .purchase-items-table th:nth-child(8),.company-bill-form .purchase-items-table td:nth-child(8){width:8% !important}.company-bill-form .purchase-items-table input{width:100%;min-width:0}</style>
<style>
.company-page-heading{position:relative}.selected-company-heading{position:absolute;left:50%;transform:translateX(-50%);text-align:center;color:#173b72;line-height:1.15}.selected-company-heading small{display:block;font-size:11px;font-weight:900;letter-spacing:1.2px;text-transform:uppercase;color:#52719a}.selected-company-heading strong{display:block;font-size:25px;font-weight:900;letter-spacing:.3px;white-space:nowrap}@media(max-width:900px){.selected-company-heading{position:static;transform:none;margin:10px auto;text-align:center}.company-page-heading{flex-wrap:wrap}.selected-company-heading strong{font-size:20px}}
</style>
<div class="main-content company-bill-form" style="background:#eef3f9;min-height:100vh"><div class="container-fluid p-4"><div class="d-flex justify-content-between align-items-center mb-4 company-page-heading"><div><h1 class="fw-bold">{{ isset($bill) ? 'Edit Company Purchase Bill' : 'Add Company Purchase Bill' }}</h1><p class="text-muted">Enter the bill received after purchasing from a company.</p></div><div class="selected-company-heading"><small>Selected Firm</small><strong>{{ $firm->name }}</strong></div><div class="d-flex gap-2 align-items-start"><input type="file" id="companyBillDocument" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" hidden><button type="button" class="btn btn-success" id="fillCompanyBill"><i class="fa fa-file-arrow-up me-1"></i>Fill Invoice by Document</button><a href="{{ route('vat-system.company-bills.index') }}" class="btn btn-outline-secondary">Back</a></div></div>
<div id="companyBillScanStatus" class="alert alert-info d-none"></div>@if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
<form method="post" id="companyBillForm" novalidate action="{{ isset($bill) ? route('vat-system.company-bills.update',$bill) : route('vat-system.company-bills.store') }}">@csrf @if(isset($bill)) @method('PUT') @endif
<div class="card mb-4"><div class="card-header bg-primary text-white fw-bold">Company / Supplier Details</div><div class="card-body"><div class="row g-3"><div class="col-md-4"><label>Company Name *</label><input name="company_name" class="form-control" value="{{ old('company_name',$bill->company_name ?? '') }}" required></div><div class="col-md-2"><label>VAT No.</label><input name="company_vat_no" class="form-control" value="{{ old('company_vat_no',$bill->company_vat_no ?? '') }}"></div><div class="col-md-2"><label>PAN No.</label><input name="company_pan_no" class="form-control" value="{{ old('company_pan_no',$bill->company_pan_no ?? '') }}"></div><div class="col-md-4"><label>Phone</label><input name="company_phone" class="form-control" value="{{ old('company_phone',$bill->company_phone ?? '') }}"></div><div class="col-md-6"><label>Address</label><input name="company_address" class="form-control" value="{{ old('company_address',$bill->company_address ?? '') }}"></div><div class="col-md-2"><label>Bill No. *</label><input name="bill_no" class="form-control" value="{{ old('bill_no',$bill->bill_no ?? '') }}" required></div><div class="col-md-2"><label>Bill Date *</label><input type="date" name="bill_date" class="form-control" value="{{ old('bill_date',isset($bill)?$bill->bill_date->format('Y-m-d'):now()->format('Y-m-d')) }}" required></div><div class="col-md-2"><label>Payment Mode</label><input name="payment_mode" class="form-control" value="{{ old('payment_mode',$bill->payment_mode ?? 'BANK ACCOUNT') }}"></div></div></div></div>
<div class="card mb-4"><div class="card-header bg-primary text-white fw-bold d-flex justify-content-between">Purchased Items <button type="button" class="btn btn-light btn-sm" id="addRow">+ Add Item</button></div><div class="table-responsive"><table class="table align-middle mb-0 purchase-items-table" id="items"><thead><tr><th>#</th><th>Particulars *</th><th>H.S. Code</th><th>Unit *</th><th>Qty *</th><th>Rate *</th><th>VAT</th><th></th></tr></thead><tbody>@if(isset($bill))@foreach($bill->items as $i=>$item)<tr><td class="sn">{{ $i+1 }}</td><td><input name="items[{{ $i }}][item_name]" class="form-control" value="{{ old('items.'.$i.'.item_name', $item->item_name) }}" required></td><td><input name="items[{{ $i }}][hs_code]" class="form-control" value="{{ old('items.'.$i.'.hs_code', $item->hs_code) }}"></td><td><input name="items[{{ $i }}][unit]" class="form-control" value="{{ old('items.'.$i.'.unit', $item->unit) }}" required></td><td><input name="items[{{ $i }}][quantity]" class="form-control qty" type="number" step=".001" value="{{ old('items.'.$i.'.quantity', $item->quantity) }}" required></td><td><input name="items[{{ $i }}][rate]" class="form-control rate" type="number" step=".01" value="{{ old('items.'.$i.'.rate', $item->rate) }}" required></td><td><input name="items[{{ $i }}][is_taxable]" value="1" type="checkbox" @checked(old('items.'.$i.'.is_taxable', $item->is_taxable))></td><td><button type="button" class="btn btn-outline-danger remove"><i class="fa fa-trash"></i></button></td></tr>@endforeach @else<tr><td class="sn">1</td><td><input name="items[0][item_name]" class="form-control" value="{{ old('items.0.item_name') }}" required></td><td><input name="items[0][hs_code]" class="form-control" value="{{ old('items.0.hs_code') }}"></td><td><input name="items[0][unit]" class="form-control" value="{{ old('items.0.unit', 'KG') }}" required></td><td><input name="items[0][quantity]" class="form-control qty" type="number" step=".001" value="{{ old('items.0.quantity', 1) }}" required></td><td><input name="items[0][rate]" class="form-control rate" type="number" step=".01" value="{{ old('items.0.rate') }}" required></td><td><input name="items[0][is_taxable]" value="1" type="checkbox" @checked(old('items.0.is_taxable', true))></td><td><button type="button" class="btn btn-outline-danger remove"><i class="fa fa-trash"></i></button></td></tr>@endif</tbody></table></div></div>
<div class="row"><div class="col-md-8"><textarea name="notes" class="form-control" rows="3" placeholder="Notes">{{ old('notes',$bill->notes ?? '') }}</textarea></div><div class="col-md-4"><div class="card p-3"><div class="d-flex justify-content-between">Total <strong id="purchaseTotal">0.00</strong></div><div class="d-flex justify-content-between">VAT (13%) <strong id="purchaseVat">0.00</strong></div><hr><div class="d-flex justify-content-between fw-bold">Grand Total <strong id="purchaseGrand">0.00</strong></div><button class="btn btn-primary w-100 mt-3">{{ isset($bill) ? 'Update Company Bill' : 'Save Company Bill' }}</button></div></div></div></form></div></div>
<script>document.addEventListener('DOMContentLoaded',()=>{const b=document.querySelector('#items tbody');const total=()=>{let t=0,tax=0;b.querySelectorAll('tr').forEach(r=>{let a=(+r.querySelector('.qty').value||0)*(+r.querySelector('.rate').value||0);t+=a;if(r.querySelector('[type=checkbox]').checked)tax+=a});document.querySelector('#purchaseTotal').textContent=t.toFixed(2);document.querySelector('#purchaseVat').textContent=(tax*.13).toFixed(2);document.querySelector('#purchaseGrand').textContent=(t+tax*.13).toFixed(2)};const re=()=>b.querySelectorAll('tr').forEach((r,i)=>{r.querySelector('.sn').textContent=i+1;r.querySelectorAll('[name]').forEach(x=>x.name=x.name.replace(/items\[\d+\]/,'items['+i+']'))});document.querySelector('#addRow').onclick=()=>{let r=b.querySelector('tr').cloneNode(true);r.querySelectorAll('input').forEach(x=>{if(x.type==='checkbox')x.checked=true;else x.value=x.classList.contains('qty')?'1':''});b.append(r);re();total()};b.addEventListener('click',e=>{if(e.target.closest('.remove')&&b.children.length>1){e.target.closest('tr').remove();re();total()}});b.addEventListener('input',total);b.addEventListener('change',total);total()});</script>
@vite('resources/js/bill-ocr.js')
<script>
document.addEventListener('DOMContentLoaded',function(){const button=document.querySelector('#fillCompanyBill'),fileInput=document.querySelector('#companyBillDocument'),status=document.querySelector('#companyBillScanStatus'),body=document.querySelector('#items tbody');const recalc=()=>{let total=0,taxable=0;body.querySelectorAll('tr').forEach(row=>{const amount=(+row.querySelector('.qty').value||0)*(+row.querySelector('.rate').value||0);total+=amount;if(row.querySelector('[type=checkbox]').checked)taxable+=amount});document.querySelector('#purchaseTotal').textContent=total.toFixed(2);document.querySelector('#purchaseVat').textContent=(taxable*.13).toFixed(2);document.querySelector('#purchaseGrand').textContent=(total+taxable*.13).toFixed(2)};body.addEventListener('input',recalc);body.addEventListener('change',recalc);const show=(m,c)=>{status.className='alert alert-'+c;status.textContent=m};button.addEventListener('click',()=>fileInput.click());fileInput.addEventListener('change',async()=>{const file=fileInput.files[0];if(!file)return;if(file.size>10*1024*1024){show('Document must be 10 MB or smaller.','danger');return}const original=button.innerHTML;button.disabled=true;button.innerHTML='<span class="spinner-border spinner-border-sm"></span> Reading...';show('Reading the document locally. Please wait...','info');try{if(!window.BillOCR)throw new Error('OCR module did not load. Refresh the page and try again.');const invoice=await window.BillOCR.extract(file);if(invoice.supplier_name)document.querySelector('[name="company_name"]').value=invoice.supplier_name;if(invoice.bill_no)document.querySelector('[name="bill_no"]').value=invoice.bill_no;if(invoice.invoice_date)document.querySelector('[name="bill_date"]').value=invoice.invoice_date;const template=body.querySelector('tr').cloneNode(true);body.innerHTML='';(invoice.items||[]).slice(0,12).forEach((item,i)=>{const row=template.cloneNode(true);row.querySelector('.sn').textContent=i+1;row.querySelectorAll('[name]').forEach(x=>x.name=x.name.replace(/items\[\d+\]/,'items['+i+']'));row.querySelector('input[name$="[item_name]"]').value=item.name||'';row.querySelector('input[name$="[unit]"]').value=item.unit||'pcs';row.querySelector('input[name$="[quantity]"]').value=item.quantity||1;row.querySelector('input[name$="[rate]"]').value=item.cost_rate||0;row.querySelector('input[name$="[hs_code]"]').value=item.hs_code||'';row.querySelector('input[type="checkbox"]').checked=true;body.appendChild(row)});if(!body.children.length)body.appendChild(template);recalc();show('Bill details filled from the document. Please review every field before saving.','success')}catch(e){show(e.message||'The document could not be read.','danger')}finally{button.disabled=false;button.innerHTML=original;fileInput.value=''}})});
</script>
<style>
.supplier-field{position:relative}.supplier-live-menu{position:absolute;z-index:9999;left:0;right:0;top:calc(100% + 5px);display:none;background:#fff;border:1px solid #cbd9eb;border-radius:9px;box-shadow:0 10px 24px #102f6330;overflow:hidden}.supplier-live-menu.show{display:block}.supplier-live-option{display:block;width:100%;padding:12px 15px;text-align:left;background:#fff;border:0;border-bottom:1px solid #edf1f6;color:#173b72;cursor:pointer}.supplier-live-option:hover{background:#eef6ff}.supplier-live-option strong,.supplier-live-option small{display:block}.supplier-live-option small{margin-top:3px;color:#64748b}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.querySelector('input[name="company_name"]');
    if (!input) return;
    const suppliers = @json($suppliers);
    const wrapper = document.createElement('div');
    wrapper.className = 'supplier-field';
    input.parentNode.insertBefore(wrapper, input);
    wrapper.appendChild(input);
    const menu = document.createElement('div');
    menu.className = 'supplier-live-menu';
    wrapper.appendChild(menu);
    function render() {
        const query = input.value.trim().toLowerCase();
        if (!query) { menu.classList.remove('show'); return; }
        const matches = suppliers.filter(s => (s.name + ' ' + (s.vat || '') + ' ' + (s.pan || '') + ' ' + (s.phone || '')).toLowerCase().includes(query)).slice(0, 8);
        menu.innerHTML = matches.length ? matches.map((s, i) => '<button type="button" class="supplier-live-option" data-index="'+i+'"><strong>'+s.name+'</strong><small>PAN: '+(s.pan || 'Not provided')+' · VAT: '+(s.vat || 'Not provided')+' · Phone: '+(s.phone || 'Not provided')+'</small></button>').join('') : '<div class="p-3 text-muted">No matching supplier found</div>';
        menu.classList.add('show');
        menu.querySelectorAll('[data-index]').forEach((button, i) => button.dataset.supplier = JSON.stringify(matches[i]));
    }
    input.addEventListener('input', render);
    input.addEventListener('focus', render);
    menu.addEventListener('mousedown', function (event) {
        const button = event.target.closest('[data-supplier]');
        if (!button) return;
        event.preventDefault();
        const supplier = JSON.parse(button.dataset.supplier);
        input.value = supplier.name;
        const set = (name, value) => { const field = document.querySelector('[name="'+name+'"]'); if (field) field.value = value || ''; };
        set('company_vat_no', supplier.vat); set('company_pan_no', supplier.pan); set('company_phone', supplier.phone); set('company_address', supplier.address);
        menu.classList.remove('show');
    });
    document.addEventListener('click', event => { if (!wrapper.contains(event.target)) menu.classList.remove('show'); });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    ['company_vat_no', 'company_pan_no', 'company_phone', 'company_address'].forEach(function (name) {
        const field = document.querySelector('[name="' + name + '"]');
        const group = field?.closest('[class*="col-"]');
        if (group) group.style.display = 'none';
    });
});
</script>
<style>
.company-bill-form .supplier-field{position:relative;width:100%}
.company-bill-form .supplier-field:before{content:'\f002';font-family:'Font Awesome 6 Free';font-weight:900;position:absolute;z-index:2;left:15px;top:16px;color:#2563eb;font-size:16px}
.company-bill-form .supplier-field input[name="company_name"]{height:54px!important;border:2px solid #2563eb!important;border-radius:10px!important;padding:10px 16px 10px 43px!important;font-size:18px!important;font-weight:600!important;color:#173b72!important;box-shadow:0 3px 10px rgba(37,99,235,.12)!important;outline:none!important}
.company-bill-form .supplier-field input[name="company_name"]::placeholder{color:#8292aa;font-weight:400}
.company-bill-form .supplier-field input[name="company_name"]:focus{border-color:#138a59!important;box-shadow:0 0 0 4px rgba(19,138,89,.14)!important}
.supplier-live-menu{top:calc(100% + 4px)!important;border-radius:10px!important;box-shadow:0 12px 28px rgba(15,47,87,.2)!important}
.supplier-field .supplier-live-menu{position:absolute!important;left:0!important;right:0!important;width:100%!important;top:calc(100% + 4px)!important;z-index:999999!important}
.company-bill-form form>.card:first-child{position:relative!important;z-index:1000!important;overflow:visible!important}.company-bill-form form>.card:nth-child(2){position:relative;z-index:1}
.company-bill-form form,.company-bill-form form>.card:first-child .card-body,.supplier-field{overflow:visible!important}.supplier-field{z-index:1001!important}
.supplier-live-option{padding:14px 16px!important}.supplier-live-option strong{font-size:17px!important;font-weight:800!important;color:#123b78!important}.supplier-live-option small{font-size:13px!important;color:#64748b!important;margin-top:5px!important}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.querySelector('input[name="company_name"]');
    const menu = document.querySelector('.supplier-live-menu');
    if (!input || !menu) return;
    const positionMenu = function () {
        if (!menu.classList.contains('show')) return;
        const rect = input.getBoundingClientRect();
        const menuHeight = Math.min(menu.scrollHeight, 260);
        const openAbove = rect.bottom + menuHeight + 8 > window.innerHeight && rect.top > menuHeight + 8;
        menu.style.position = 'fixed';
        menu.style.left = rect.left + 'px';
        menu.style.width = rect.width + 'px';
        menu.style.top = (openAbove ? rect.top - menuHeight - 5 : rect.bottom + 5) + 'px';
    };
    input.addEventListener('input', () => setTimeout(positionMenu, 0));
    input.addEventListener('focus', () => setTimeout(positionMenu, 0));
    window.addEventListener('resize', positionMenu);
    window.addEventListener('scroll', positionMenu, true);
});
</script>
<style>
.supplier-field .supplier-live-menu{position:fixed!important;z-index:2147483000!important}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.querySelector('input[name="company_name"]');
    const menu = document.querySelector('.supplier-live-menu');
    if (!input || !menu) return;
    const place = function () {
        if (!menu.classList.contains('show')) return;
        const rect = input.getBoundingClientRect();
        const height = Math.min(menu.scrollHeight || 180, 260);
        const top = rect.bottom + height + 8 > window.innerHeight && rect.top > height + 8
            ? rect.top - height - 5 : rect.bottom + 5;
        menu.style.setProperty('left', rect.left + 'px', 'important');
        menu.style.setProperty('top', top + 'px', 'important');
        menu.style.setProperty('width', rect.width + 'px', 'important');
    };
    input.addEventListener('input', () => setTimeout(place, 10));
    input.addEventListener('focus', () => setTimeout(place, 10));
    window.addEventListener('resize', place);
    window.addEventListener('scroll', place, true);
});
</script>
<style>
.company-bill-form .validation-invalid{border:2px solid #dc3545!important;background:#fff7f7!important;box-shadow:0 0 0 3px rgba(220,53,69,.12)!important}.company-bill-form label.validation-label-invalid{color:#dc3545!important}.company-bill-form .validation-error{display:block;margin-top:5px;color:#dc3545;font-size:12px;font-weight:700}.company-bill-form .supplier-field.validation-invalid{border:2px solid #dc3545!important;border-radius:10px;box-shadow:0 0 0 3px rgba(220,53,69,.12)!important}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('companyBillForm');
    if (!form) return;
    const supplierNames = @json($suppliers);
    const supplierInput = form.querySelector('[name="company_name"]');
    const supplierField = supplierInput?.closest('.supplier-field');
    const message = function (field, text) {
        field.classList.add('validation-invalid');
        const group = field.closest('[class*="col-"]') || field.parentElement;
        group.querySelector('label')?.classList.add('validation-label-invalid');
        let error = group.querySelector('.validation-error');
        if (!error) { error = document.createElement('small'); error.className = 'validation-error'; group.appendChild(error); }
        error.textContent = text;
    };
    const clear = function (field) {
        field.classList.remove('validation-invalid');
        const group = field.closest('[class*="col-"]') || field.parentElement;
        group.querySelector('label')?.classList.remove('validation-label-invalid');
        group.querySelector('.validation-error')?.remove();
    };
    form.addEventListener('input', function (event) { if (event.target.matches('input,textarea,select')) { clear(event.target); if (event.target === supplierInput) supplierField?.classList.remove('validation-invalid'); } });
    form.addEventListener('submit', function (event) {
        let first = null;
        const name = supplierInput?.value.trim().toLowerCase();
        const validSupplier = supplierNames.some(s => (s.name || '').trim().toLowerCase() === name);
        if (!validSupplier) {
            event.preventDefault();
            message(supplierInput, name ? 'Select a supplier from the search results.' : 'Please select a supplier.');
            supplierField?.classList.add('validation-invalid');
            first = supplierInput;
        }
        form.querySelectorAll('input[required],select[required],textarea[required]').forEach(function (field) {
            if (!field.checkValidity()) { message(field, 'This field is required.'); if (!first) first = field; }
        });
        if (first) { first.focus(); first.scrollIntoView({behavior:'smooth',block:'center'}); }
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.querySelector('input[name="company_name"]');
    if (!input) return;
    input.setAttribute('autocomplete', 'new-password');
    input.setAttribute('autocorrect', 'off');
    input.setAttribute('autocapitalize', 'words');
    input.setAttribute('spellcheck', 'false');
    input.setAttribute('role', 'combobox');
    input.setAttribute('aria-autocomplete', 'list');
    input.setAttribute('aria-expanded', 'false');
    input.addEventListener('input', function () { input.setAttribute('aria-expanded', 'true'); });
});
</script>
<style>
.company-bill-form{background:linear-gradient(180deg,#edf4fc 0%,#f7faff 100%)!important}
.company-bill-form>.container-fluid{max-width:1600px;margin:0 auto;padding:30px clamp(16px,3vw,42px)!important}
.company-bill-form .company-page-heading{position:relative;padding:8px 4px 20px;margin-bottom:22px!important}
.company-bill-form .company-page-heading h1{font-size:clamp(28px,3.4vw,45px);letter-spacing:-.8px;color:#102f63;margin-bottom:6px!important}
.company-bill-form .company-page-heading p{font-size:15px;color:#64748b}
.company-bill-form .selected-company-heading{background:#e7f0ff;border:1px solid #a9c8ff;border-radius:14px;padding:10px 24px;box-shadow:0 6px 16px rgba(37,99,235,.1)}
.company-bill-form .selected-company-heading strong{font-size:clamp(19px,2vw,28px);color:#123b78}
.company-bill-form .card{border:1px solid #dbe6f4!important;border-radius:15px!important;box-shadow:0 10px 28px rgba(23,59,114,.09)!important;overflow:visible!important}
.company-bill-form .card-header{background:linear-gradient(100deg,#123b78,#1769e8)!important;border:0!important;padding:15px 18px!important;font-size:16px;letter-spacing:.1px}
.company-bill-form .card-body{padding:24px!important}
.company-bill-form label{font-size:13px!important;font-weight:800!important;color:#173b72!important}
.company-bill-form .form-control{min-height:42px;border:1px solid #c5d5e9;border-radius:9px;color:#173b72;background:#fbfdff;box-shadow:none;transition:.18s}
.company-bill-form .form-control:focus{border-color:#2563eb;box-shadow:0 0 0 4px rgba(37,99,235,.12);background:#fff}
.company-bill-form .purchase-items-table{border:1px solid #d6e1ef!important}
.company-bill-form .purchase-items-table thead th{background:#2945cb!important;padding:13px 12px!important;font-size:12px!important;letter-spacing:.2px}
.company-bill-form .purchase-items-table tbody td{padding:12px 10px!important;background:#fff}
.company-bill-form .purchase-items-table tbody tr:nth-child(even) td{background:#f8fbff}
.company-bill-form .purchase-items-table .remove{width:38px;height:38px;padding:0!important}
.company-bill-form textarea{min-height:120px!important;resize:vertical}
.company-bill-form .row>.col-md-4>.card{background:#fff!important;border:1px solid #d6e1ef!important}
.company-bill-form .row>.col-md-4>.card .d-flex{font-size:15px;color:#475569;margin:5px 0}
.company-bill-form .row>.col-md-4>.card .fw-bold{font-size:18px;color:#102f63}
.company-bill-form .row>.col-md-4>.card button[type=submit]{min-height:48px;font-size:15px;background:linear-gradient(100deg,#1264e8,#1769ff);border:0}
.company-bill-form #fillCompanyBill{box-shadow:0 5px 14px rgba(21,128,61,.18)}
@media(max-width:900px){.company-bill-form .selected-company-heading{position:static;transform:none;margin:12px auto;width:100%;text-align:center}.company-bill-form .company-page-heading>div:last-child{width:100%;justify-content:center}.company-bill-form .company-page-heading>div:last-child .btn{flex:1}.company-bill-form .company-page-heading{padding-bottom:8px}}
</style>
<script>
document.addEventListener('DOMContentLoaded',function(){const input=document.querySelector('input[name="company_name"]');if(input&&!input.placeholder)input.placeholder='Search supplier by name, VAT, PAN or phone';});
</script>
<style>
.company-bill-form .company-page-heading{display:grid!important;grid-template-columns:minmax(300px,1fr) minmax(360px,auto) auto;align-items:center;gap:22px;width:100%}
.company-bill-form .company-page-heading>div:first-child{min-width:0}
.company-bill-form .company-page-heading>div:first-child h1{white-space:normal;line-height:1.08}
.company-bill-form .selected-company-heading{position:static!important;left:auto!important;transform:none!important;width:auto;min-width:360px;max-width:620px;margin:0;text-align:center;white-space:normal}
.company-bill-form .selected-company-heading strong{white-space:normal;line-height:1.15}
.company-bill-form .company-page-heading>div:last-child{justify-self:end;white-space:nowrap}
@media(max-width:1180px){.company-bill-form .company-page-heading{grid-template-columns:minmax(280px,1fr) auto;gap:16px}.company-bill-form .selected-company-heading{grid-column:1/-1;grid-row:2;justify-self:center;min-width:min(100%,520px)}}
@media(max-width:600px){.company-bill-form .company-page-heading{display:flex!important;flex-direction:column;align-items:stretch;gap:12px}.company-bill-form .selected-company-heading{min-width:0;max-width:none;order:2}.company-bill-form .company-page-heading>div:last-child{order:3;justify-self:stretch;display:flex;width:100%}.company-bill-form .company-page-heading>div:last-child .btn{flex:1}}
</style>
<style>.company-bill-form input[name="payment_mode"]{display:none!important}</style>
<script>document.addEventListener('DOMContentLoaded',function(){const field=document.querySelector('.company-bill-form [name="payment_mode"]');const group=field?.closest('[class*="col-"]');if(group)group.style.display='none';});</script>
@endsection
