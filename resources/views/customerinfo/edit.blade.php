@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 

@yield('breadcrumb')


<div class="container">

<form class="row gx-5 gy-3" action="{{route('customerinfos.update',$cus->id)}}" method="post">
                @csrf
                @method('put')            

          <div class="col-md-6">
                    <label for="inputPassword4" class="form-label"> Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                        name="name" id="customerNameInput" placeholder="Enter Name" value="{{ old('name',$cus->name) }}">
                    @error('name')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Address</label>
                    <input type="text" class="form-control @error('address') is-invalid @enderror" 
                        name="address" id="customerAddressInput" value="{{ old('address',$cus->address) }}">
                    @error('address')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Email</label>
                    <input type="text" class="form-control @error('email') is-invalid @enderror" 
                        name="email" value="{{ old('email',$cus->email) }}">
                    @error('email')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">PhoneNo</label>
                    <input type="text" class="form-control @error('phoneno') is-invalid @enderror" 
                        name="phoneno" id="customerPhoneInput" value="{{ old('phoneno',$cus->phoneno) }}">
                    @error('phoneno')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Alternate PhoneNo</label>
                <input type="text" class="form-control @error('alternate_phoneno') is-invalid @enderror" 
                    name="alternate_phoneno" id="customerAltPhoneInput" value="{{ old('alternate_phoneno',$cus->alternate_phoneno) }}">
                @error('alternate_phoneno')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
        </div>

        <div class="col-md-12">
            <div id="duplicateCustomerWarning" class="duplicate-warning" style="display: none;">
                <div class="duplicate-warning-title">
                    <i class="fas fa-triangle-exclamation"></i>
                    Possible duplicate customer found
                </div>
                <div id="duplicateCustomerList"></div>
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label">
                Customer Type <span style="color: red;">*</span>
            </label>
        <select name="type" id="customerTypeSelect" class="form-control @error('type') is-invalid @enderror">
            <option value="">-- Select Type --</option>
        
            <option value="shop"
                {{ old('type', $cus->type) == 'shop' ? 'selected' : '' }}>
                Shop
            </option>
        
            <option value="customer"
                {{ old('type', $cus->type) == 'customer' ? 'selected' : '' }}>
                Customer
            </option>
        </select>
        </div>

        <div class="col-md-6" id="customerVatNoBox" style="display: none;">
            <label class="form-label">VAT No</label>
            <input type="text" class="form-control @error('vat_no') is-invalid @enderror"
                name="vat_no" id="customerVatNoInput" value="{{ old('vat_no',$cus->vat_no) }}" placeholder="Enter VAT No">
            @error('vat_no')
                <p class="invalid-feedback">{{ $message }}</p>
            @enderror
        </div>
           

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Remarks</label>
                    <textarea type="text" class="form-control @error('remarks') is-invalid @enderror" 
                        name="remarks" value="{{ old('remarks',$cus->remarks) }}" rows="4" cols="50">{{ old('remarks',$cus->remarks) }}</textarea>
                    @error('remarks')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="d-grid gap-2 pt-2 pb-4">
                    <button type="submit" class="btn btn-lg btn-primary">Update</button>
            </div>
</form>
</div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const customers = @json($duplicateCustomers ?? []);
        const nameInput = document.getElementById('customerNameInput');
        const addressInput = document.getElementById('customerAddressInput');
        const phoneInput = document.getElementById('customerPhoneInput');
        const altPhoneInput = document.getElementById('customerAltPhoneInput');
        const typeSelect = document.getElementById('customerTypeSelect');
        const vatNoBox = document.getElementById('customerVatNoBox');
        const vatNoInput = document.getElementById('customerVatNoInput');
        const warning = document.getElementById('duplicateCustomerWarning');
        const list = document.getElementById('duplicateCustomerList');

        function updateVatNoVisibility() {
            const isShop = typeSelect && typeSelect.value === 'shop';
            vatNoBox.style.display = isShop ? '' : 'none';

            if (!isShop && vatNoInput) {
                vatNoInput.value = '';
            }
        }

        function cleanText(value) {
            return String(value || '').trim().toUpperCase().replace(/\s+/g, ' ');
        }

        function cleanPhone(value) {
            return String(value || '').replace(/\D+/g, '');
        }

        function escapeHtml(value) {
            return String(value || '').replace(/[&<>"']/g, function (char) {
                return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'}[char];
            });
        }

        function duplicateReason(customer, name, address, phone, altPhone) {
            const customerName = cleanText(customer.name);
            const customerAddress = cleanText(customer.address);
            const customerPhone = cleanPhone(customer.phoneno);
            const customerAltPhone = cleanPhone(customer.alternate_phoneno);
            const enteredPhones = [phone, altPhone].filter(Boolean);

            if (enteredPhones.length && enteredPhones.some(item => item === customerPhone || item === customerAltPhone)) {
                return 'same phone number';
            }

            if (name.length >= 4 && (customerName.includes(name) || name.includes(customerName))) {
                return address.length >= 3 && customerAddress.includes(address) ? 'similar name and address' : 'similar name';
            }

            return '';
        }

        function renderWarning() {
            const name = cleanText(nameInput.value);
            const address = cleanText(addressInput.value);
            const phone = cleanPhone(phoneInput.value);
            const altPhone = cleanPhone(altPhoneInput.value);

            const matches = customers.map(customer => ({
                customer: customer,
                reason: duplicateReason(customer, name, address, phone, altPhone),
            })).filter(item => item.reason).slice(0, 5);

            if (!matches.length) {
                warning.style.display = 'none';
                list.innerHTML = '';
                return;
            }

            warning.style.display = 'block';
            list.innerHTML = matches.map(item => {
                const customer = item.customer;
                return '<div class="duplicate-warning-row">'
                    + '<strong>ID ' + escapeHtml(customer.id) + ' - ' + escapeHtml(customer.name || '-') + '</strong>'
                    + '<span>' + escapeHtml(customer.address || '-') + ' | ' + escapeHtml(customer.phoneno || '-') + ' | ' + escapeHtml(item.reason) + '</span>'
                    + '</div>';
            }).join('');
        }

        [nameInput, addressInput, phoneInput, altPhoneInput].forEach(input => {
            input.addEventListener('input', renderWarning);
        });

        typeSelect.addEventListener('change', updateVatNoVisibility);
        updateVatNoVisibility();
        renderWarning();
    });
</script>

<style>
    .duplicate-warning {
        border: 2px solid #f59e0b;
        border-radius: 8px;
        background: #fff7ed;
        padding: 12px 14px;
    }

    .duplicate-warning-title {
        color: #9a3412;
        font-size: 16px;
        font-weight: 900;
        margin-bottom: 8px;
    }

    .duplicate-warning-row {
        border-top: 1px solid #fed7aa;
        padding: 8px 0;
    }

    .duplicate-warning-row strong,
    .duplicate-warning-row span {
        display: block;
    }

    .duplicate-warning-row span {
        color: #7c2d12;
        font-size: 14px;
        font-weight: 700;
    }
</style>
@stop
