@extends('layouts.master')

@section('content')
@php($isEdit = !empty($vatBill))
<div class="main-content">
    <div class="container py-4 vat-form-page">
        <div class="card vat-form-card mx-auto">
            <div class="vat-form-header">
                <div class="vat-form-header-icon"><i class="fa-solid fa-file-invoice"></i></div>
                <div class="vat-form-heading">
                    <span>VAT BILL MANAGEMENT</span>
                    <h2>{{ $isEdit ? 'Edit VAT Bill' : 'Create VAT Bill' }}</h2>
                    <p>{{ $isEdit ? 'Update the bill information while keeping party details protected.' : 'Select a customer and enter the VAT bill information below.' }}</p>
                </div>
                <span class="vat-form-mode"><i class="fa-solid {{ $isEdit ? 'fa-pen' : 'fa-plus' }}"></i> {{ $isEdit ? 'Edit Mode' : 'New Bill' }}</span>
            </div>

            <div class="card-body vat-form-body">
                <form method="POST" action="{{ $isEdit ? route('vat-bills.entry.update', $vatBill) : route('vat-bills.standalone.store') }}" id="standaloneVatForm">
                    @csrf
                    @if($isEdit) @method('PUT') @endif

                    @if($errors->any())
                        <div class="vat-error-alert">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <div>
                                <strong>Please check the form.</strong>
                                <ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                            </div>
                        </div>
                    @endif

                    <section class="vat-form-section">
                        <div class="section-heading">
                            <span class="section-number">1</span>
                            <div>
                                <h5>Party Details</h5>
                                <p>Choose an existing customer. Selected details cannot be changed manually.</p>
                            </div>
                            <span class="locked-badge"><i class="fa-solid fa-lock"></i> Protected</span>
                        </div>

                        @if(!$isEdit)
                            <div class="customer-lookup">
                                <label for="vatCustomerSearch" class="form-label"><i class="fa-solid fa-magnifying-glass"></i> Search Existing Customer</label>
                                <div class="input-group customer-search-input">
                                    <span class="input-group-text"><i class="fa-solid fa-users"></i></span>
                                    <input type="search" id="vatCustomerSearch" class="form-control"
                                        placeholder="Type customer name, VAT no, phone or address" autocomplete="off">
                                </div>
                                <div class="customer-search-status" id="vatCustomerSearchStatus">Select a customer to fill all party details automatically.</div>
                                <div class="customer-search-results d-none" id="vatCustomerSearchResults"></div>
                            </div>
                        @else
                            <div class="locked-info"><i class="fa-solid fa-shield-halved"></i> Party details are locked to protect this party ledger.</div>
                        @endif

                        <div class="row g-3 party-fields">
                            <div class="col-md-6">
                                <label class="form-label" for="vatPartyName"><i class="fa-solid fa-building-user"></i> Party Name</label>
                                <div class="locked-input"><input class="form-control" id="vatPartyName" name="party_name" value="{{ old('party_name', optional($vatBill)->party_name) }}" readonly required><i class="fa-solid fa-lock"></i></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="vatPartyVatNo"><i class="fa-solid fa-hashtag"></i> Party VAT No.</label>
                                <div class="locked-input"><input class="form-control" id="vatPartyVatNo" name="party_vat_no" value="{{ old('party_vat_no', optional($vatBill)->party_vat_no) }}" readonly><i class="fa-solid fa-lock"></i></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="vatPartyAddress"><i class="fa-solid fa-location-dot"></i> Address</label>
                                <div class="locked-input"><input class="form-control" id="vatPartyAddress" name="party_address" value="{{ old('party_address', optional($vatBill)->party_address) }}" readonly><i class="fa-solid fa-lock"></i></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="vatPartyPhone"><i class="fa-solid fa-phone"></i> Phone</label>
                                <div class="locked-input"><input class="form-control" id="vatPartyPhone" name="party_phone" value="{{ old('party_phone', optional($vatBill)->party_phone) }}" readonly><i class="fa-solid fa-lock"></i></div>
                            </div>
                        </div>
                    </section>

                    <section class="vat-form-section bill-details-section">
                        <div class="section-heading">
                            <span class="section-number">2</span>
                            <div><h5>VAT Bill Details</h5><p>Enter the firm, date, bill number and taxable amount.</p></div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label"><i class="fa-solid fa-shop"></i> Our Firm</label>
                                <select class="form-select" name="firm_type" required>
                                    <option value="">Select Firm</option>
                                    @foreach($firms as $firm)
                                        <option value="{{ $firm }}" {{ old('firm_type', optional($vatBill)->firm_type) === $firm ? 'selected' : '' }}>{{ $firm }} — VAT {{ $firmVatNumbers[$firm] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="fa-solid fa-calendar-days"></i> VAT Date</label>
                                <input type="date" class="form-control" name="date" value="{{ old('date', $isEdit ? $vatBill->date->format('Y-m-d') : now()->toDateString()) }}" required>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label"><i class="fa-solid fa-receipt"></i> VAT Bill No.</label>
                                <input class="form-control" name="bill_no" value="{{ old('bill_no', optional($vatBill)->bill_no) }}" placeholder="Enter bill number" required>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label"><i class="fa-solid fa-coins"></i> Taxable Amount</label>
                                <div class="input-group amount-input"><span class="input-group-text">Rs</span><input type="number" min="0" step="0.01" class="form-control" id="standaloneTaxable" name="amount_without_tax" value="{{ old('amount_without_tax', optional($vatBill)->amount_without_tax) }}" placeholder="0.00" required></div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label">VAT 13%</label>
                                <div class="calculated-field"><span>Rs</span><strong id="standaloneVat">0.00</strong></div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label">Grand Total</label>
                                <div class="calculated-field total-field"><span>Rs</span><strong id="standaloneTotal">0.00</strong></div>
                            </div>
                        </div>
                    </section>

                    <div class="vat-form-actions">
                        <a href="{{ route('vat-bills.index') }}" class="btn cancel-btn"><i class="fa-solid fa-arrow-left"></i> Cancel</a>
                        <button class="btn submit-btn"><i class="fa-solid fa-floppy-disk"></i> {{ $isEdit ? 'Update VAT Bill' : 'Create VAT Bill' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.vat-form-page{max-width:1100px}.vat-form-card{border:0;border-radius:18px;box-shadow:0 18px 45px rgba(15,23,42,.14);max-width:1000px;overflow:visible}.vat-form-header{align-items:center;background:linear-gradient(135deg,#123b8f 0%,#1468dd 60%,#06a5c9 100%);border-radius:18px 18px 0 0;color:#fff;display:flex;gap:18px;padding:24px 28px;position:relative;overflow:hidden}.vat-form-header:after{background:rgba(255,255,255,.08);border-radius:50%;content:"";height:190px;position:absolute;right:-55px;top:-95px;width:190px}.vat-form-header-icon{align-items:center;background:rgba(255,255,255,.17);border:1px solid rgba(255,255,255,.3);border-radius:14px;display:flex;flex:0 0 58px;font-size:27px;height:58px;justify-content:center}.vat-form-heading{position:relative;z-index:1}.vat-form-heading>span{font-size:11px;font-weight:900;letter-spacing:1.5px;opacity:.85}.vat-form-heading h2{font-size:28px;font-weight:900;line-height:1.1;margin:3px 0 5px}.vat-form-heading p{font-size:13px;margin:0;opacity:.85}.vat-form-mode{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.28);border-radius:999px;font-size:12px;font-weight:800;margin-left:auto;padding:8px 12px;position:relative;white-space:nowrap;z-index:1}.vat-form-body{background:#f8fafc;border-radius:0 0 18px 18px;padding:25px}.vat-form-section{background:#fff;border:1px solid #e2e8f0;border-radius:14px;box-shadow:0 4px 12px rgba(15,23,42,.04);padding:22px}.bill-details-section{margin-top:18px}.section-heading{align-items:center;border-bottom:1px solid #e2e8f0;display:flex;gap:12px;margin-bottom:18px;padding-bottom:14px}.section-number{align-items:center;background:linear-gradient(135deg,#1d4ed8,#0ea5e9);border-radius:10px;color:#fff;display:flex;flex:0 0 38px;font-size:17px;font-weight:900;height:38px;justify-content:center}.section-heading h5{color:#172554;font-size:18px;font-weight:900;margin:0}.section-heading p{color:#64748b;font-size:12px;margin:2px 0 0}.locked-badge{background:#ecfdf5;border:1px solid #a7f3d0;border-radius:999px;color:#047857;font-size:11px;font-weight:900;margin-left:auto;padding:7px 10px;white-space:nowrap}.customer-lookup{background:linear-gradient(135deg,#eff6ff,#ecfeff);border:1px solid #bae6fd;border-radius:14px;margin-bottom:18px;padding:17px;position:relative}.customer-lookup .form-label{color:#1e3a8a;font-weight:900}.customer-search-input{box-shadow:0 4px 12px rgba(37,99,235,.08)}.customer-search-input .input-group-text{background:#fff;border-color:#93c5fd;border-radius:10px 0 0 10px;color:#2563eb;font-size:18px;padding:0 16px}.customer-search-input .form-control{border-color:#93c5fd;border-radius:0 10px 10px 0;font-size:15px;font-weight:700;min-height:50px}.customer-search-status{color:#64748b;font-size:12px;font-weight:700;margin-top:8px}.customer-search-results{background:#f8fafc;border:1px solid #bfdbfe;border-radius:14px;box-shadow:0 22px 50px rgba(15,23,42,.24);left:17px;max-height:360px;overflow-y:auto;padding:8px;position:absolute;right:17px;top:96px;z-index:100}.customer-search-results::-webkit-scrollbar{width:8px}.customer-search-results::-webkit-scrollbar-track{background:#e2e8f0;border-radius:10px}.customer-search-results::-webkit-scrollbar-thumb{background:#93c5fd;border-radius:10px}.customer-result-item{align-items:center;background:#fff;border:1px solid transparent;border-radius:10px;cursor:pointer;display:flex;gap:12px;margin-bottom:6px;padding:11px 12px;text-align:left;transition:all .15s ease;width:100%}.customer-result-item:last-child{margin-bottom:0}.customer-result-item:hover,.customer-result-item:focus{background:#eff6ff;border-color:#93c5fd;box-shadow:0 5px 12px rgba(37,99,235,.09);outline:0;transform:translateX(3px)}.customer-result-avatar{align-items:center;background:linear-gradient(135deg,#2563eb,#06b6d4);border-radius:10px;color:#fff;display:flex;flex:0 0 42px;font-size:16px;font-weight:900;height:42px;justify-content:center;text-transform:uppercase}.customer-result-copy{min-width:0}.customer-result-item strong{color:#172554;display:block;font-size:14px;line-height:1.3;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.customer-result-meta{display:flex;flex-wrap:wrap;gap:5px 7px;margin-top:6px}.customer-result-meta span{align-items:center;background:#f1f5f9;border-radius:999px;color:#475569;display:inline-flex;font-size:11px;font-weight:700;gap:4px;padding:3px 7px}.customer-result-meta span i{color:#3b82f6;font-size:10px}.customer-search-empty{align-items:center;color:#64748b;display:flex;gap:10px;padding:16px}.customer-search-empty i{color:#94a3b8;font-size:22px}.selected-customer-message{color:#047857!important;font-weight:900}.locked-info{align-items:center;background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;color:#1e40af;display:flex;font-size:13px;font-weight:700;gap:9px;margin-bottom:18px;padding:11px 13px}.vat-form-section .form-label{color:#334155;font-size:13px;font-weight:800;margin-bottom:6px}.vat-form-section .form-label i{color:#2563eb;margin-right:4px}.vat-form-section .form-control,.vat-form-section .form-select{border-color:#cbd5e1;border-radius:9px;min-height:44px}.vat-form-section .form-control:focus,.vat-form-section .form-select:focus{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.14)}.party-fields .locked-input{position:relative}.party-fields .locked-input input{background:#f1f5f9;color:#334155;cursor:not-allowed;font-weight:700;padding-right:38px}.party-fields .locked-input>i{color:#94a3b8;pointer-events:none;position:absolute;right:14px;top:15px}.amount-input .input-group-text{background:#eff6ff;border-color:#cbd5e1;color:#1d4ed8;font-weight:900}.calculated-field{align-items:center;background:#f0f9ff;border:1px solid #bae6fd;border-radius:9px;color:#0369a1;display:flex;gap:5px;min-height:44px;padding:9px 11px}.calculated-field span{font-size:11px;font-weight:800}.calculated-field strong{font-size:16px;margin-left:auto}.total-field{background:#ecfdf5;border-color:#a7f3d0;color:#047857}.vat-error-alert{align-items:flex-start;background:#fef2f2;border:1px solid #fecaca;border-radius:12px;color:#b91c1c;display:flex;gap:10px;margin-bottom:18px;padding:13px 15px}.vat-error-alert>i{font-size:19px;margin-top:2px}.vat-form-actions{align-items:center;display:flex;gap:10px;justify-content:flex-end;margin-top:20px}.vat-form-actions .btn{border-radius:9px;font-weight:800;min-height:44px;padding:10px 18px}.cancel-btn{background:#fff;border:1px solid #cbd5e1;color:#475569}.cancel-btn:hover{background:#f1f5f9;color:#0f172a}.submit-btn{background:linear-gradient(135deg,#059669,#16a34a);border:0;color:#fff;padding-left:24px!important;padding-right:24px!important}.submit-btn:hover{box-shadow:0 7px 16px rgba(5,150,105,.25);color:#fff;transform:translateY(-1px)}
@media(max-width:767px){.vat-form-page{padding-left:10px;padding-right:10px}.vat-form-header{align-items:flex-start;padding:20px}.vat-form-header-icon{display:none}.vat-form-mode{font-size:10px}.vat-form-heading h2{font-size:23px}.vat-form-body{padding:14px}.vat-form-section{padding:15px}.locked-badge{display:none}.vat-form-actions{align-items:stretch;flex-direction:column-reverse}.vat-form-actions .btn{width:100%}}
</style>

<script>
document.addEventListener('DOMContentLoaded',function(){
    var taxable=document.getElementById('standaloneTaxable');
    var vat=document.getElementById('standaloneVat');
    var total=document.getElementById('standaloneTotal');

    function updateTotals(){
        var amount=parseFloat(taxable.value)||0;
        var vatAmount=Math.round((amount*.13+Number.EPSILON)*100)/100;
        vat.textContent=vatAmount.toFixed(2);
        total.textContent=(amount+vatAmount).toFixed(2);
    }

    taxable.addEventListener('input',updateTotals);
    updateTotals();

    var search=document.getElementById('vatCustomerSearch');
    var results=document.getElementById('vatCustomerSearchResults');
    var status=document.getElementById('vatCustomerSearchStatus');
    if(!search)return;

    var timer=null,request=null;
    function hideResults(){results.classList.add('d-none')}
    function addMeta(container,icon,label,value){
        var span=document.createElement('span');
        var iconElement=document.createElement('i');
        iconElement.className='fa-solid '+icon;
        span.appendChild(iconElement);
        span.appendChild(document.createTextNode(label+': '+(value||'-')));
        container.appendChild(span);
    }
    function selectCustomer(customer){
        document.getElementById('vatPartyName').value=customer.name||'';
        document.getElementById('vatPartyVatNo').value=customer.vat_no||'';
        document.getElementById('vatPartyAddress').value=customer.address||'';
        document.getElementById('vatPartyPhone').value=customer.phoneno||'';
        search.value=customer.name||'';
        status.textContent='Selected: '+(customer.name||'Customer')+' — party details filled and locked.';
        status.classList.add('selected-customer-message');
        hideResults();
    }
    function render(customers){
        results.innerHTML='';
        if(!customers.length){var empty=document.createElement('div');empty.className='customer-search-empty';var emptyIcon=document.createElement('i');emptyIcon.className='fa-solid fa-user-slash';var emptyText=document.createElement('span');emptyText.textContent='No customer found. Search for an existing customer to continue.';empty.appendChild(emptyIcon);empty.appendChild(emptyText);results.appendChild(empty);results.classList.remove('d-none');return}
        customers.forEach(function(customer){
            var button=document.createElement('button');button.type='button';button.className='customer-result-item';
            var avatar=document.createElement('span');avatar.className='customer-result-avatar';avatar.textContent=(customer.name||'?').trim().charAt(0);
            var copy=document.createElement('span');copy.className='customer-result-copy';
            var name=document.createElement('strong');name.textContent=customer.name||'Unnamed Customer';
            var meta=document.createElement('span');meta.className='customer-result-meta';
            addMeta(meta,'fa-hashtag','VAT',customer.vat_no);
            addMeta(meta,'fa-phone','Phone',customer.phoneno);
            addMeta(meta,'fa-location-dot','Address',customer.address);
            copy.appendChild(name);copy.appendChild(meta);button.appendChild(avatar);button.appendChild(copy);
            button.addEventListener('click',function(){selectCustomer(customer)});results.appendChild(button);
        });
        results.classList.remove('d-none');
    }
    search.addEventListener('input',function(){
        clearTimeout(timer);status.classList.remove('selected-customer-message');var q=search.value.trim();
        if(q.length<1){hideResults();status.textContent='Select a customer to fill all party details automatically.';return}
        timer=setTimeout(function(){if(request)request.abort();request=new AbortController();status.textContent='Searching customers...';fetch(@json(url('/api/customer_search'))+'/'+encodeURIComponent(q),{headers:{'Accept':'application/json'},signal:request.signal}).then(function(response){if(!response.ok)throw new Error();return response.json()}).then(function(data){status.textContent=data.length+' customer(s) found';render(data)}).catch(function(error){if(error.name!=='AbortError')status.textContent='Unable to search customers.'})},250);
    });
    document.addEventListener('click',function(event){if(!event.target.closest('.customer-lookup'))hideResults()});
});
</script>
@stop
