@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content">
  @yield('breadcrumb')

  <div class="container">

    {{-- =============== Customer Card (collapsible) =============== --}}
    <div class="card customer-card " id="customerCard" style="display:none;">
      <div class="card-body">
        <h5 class="card-title">Customer Information</h5>
        <p><span>ID: </span><span id="customerId">...</span></p>
        <p class="card-text"><span>Name: </span><span id="customerName">...</span></p>
        <p><span>Address: </span><span id="customerAddress">...</span></p>
        <p><span>E-mail: </span><span id="customerEmail">...</span></p>
        <p><span>PhoneNo: </span><span id="customerPhone">...</span></p>
      </div>
      <div class="toogle-box p-3 d-flex justify-content-center align-items-center" id="toggleBox" data-toggle="close">
        <i class="fas fa-user"></i>
      </div>
    </div>

    {{-- =============== Customer Picker Form =============== --}}
    <div class="row m-0" style="position:relative; top:-40px;">
        <form action="{{ route('oldpricecheck') }}" method="get" id="chosendatepdfform" class="row g-3">
        <div class="col-auto" style="width: 300px;">
          <div class="search-box position-relative">
            <input id="customerIdInput" name="customerid" type="hidden">

            <input
              type="text"
              id="searchCustomerInput"
              data-api="customer_search"
              autocomplete="off"
              class="search-input form-control @error('customerid') is-invalid @enderror"
              placeholder="Search Customer">
            @error('customerid')
              <p class="invalid-feedback m-0" style="position:absolute;bottom:-24px;left:0;">{{ $message }}</p>
            @enderror

            <i class="fas fa-search search-icon"></i>

            <div class="result-wrapper" id="customerResultWrapper" style="display:none;">
              <div class="result-box d-flex align-items-center" id="customerLoadingResultBox">
                <i class="fas fa-spinner" id="spinnerIcon"></i>
                <h1 class="m-0 px-2">Loading</h1>
              </div>

              <div class="result-box d-flex align-items-center d-none" id="customerNotFoundResultBox">
                <i class="fas fa-triangle-exclamation"></i>
                <h1 class="m-0 px-2">Record Not Found</h1>
              </div>

              <div id="customerResultList"></div>
            </div>
          </div>
        </div>

        <div class="col-auto">
          <button type="submit" class="btn btn-dark">
            <i class="fas fa-search"></i> Search
          </button>
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-4">
            @foreach ($cusinfoforpdfok as $i)
              <div class="mb-3">
                <span class="fw-bold fs-4">{{ $i->name }}</span>
              --<span class="fw-bold">{{ $i->address }}</span>
               -- <span class="fw-bold">{{ $i->phoneno }}, {{ $i->alternate_phoneno }}</span>
              </div>
            @endforeach
          </div>
      </form>
{{-- momommo --}}


    </div>

    {{-- =============== Selected Customer Info (from $cusinfoforpdfok) =============== --}}
  

    {{-- =============== Items Table Card =============== --}}
    <div class="card mt-0">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col-md-6">
            <a href="{{ route('itemsales.create') }}" class="btn btn-primary ms-3" style="background-color:#FF0066;border-color:#0be813;">
              <i class="fas fa-file-invoice"></i> ADD NEW INVOICE
            </a>
          </div>

          <div class="col-md-6">
            <form method="get" action="{{ route('oldpricecheck') }}" id="tableSearchForm" class="d-flex justify-content-end">
              {{-- preserve current filters --}}
              @if(!empty($cid))  <input type="hidden" name="customerid" value="{{ $cid }}"> @endif
              @if(!empty($from)) <input type="hidden" name="date1" value="{{ $from }}">   @endif
              @if(!empty($to))   <input type="hidden" name="date2" value="{{ $to }}">     @endif

              <input
                type="text"
                name="searchxx"
                id="filtertext"
                class="form-control p-2 fs-3"
                placeholder="Search Items Name here ....."
                style="max-width: 400px;border:10px solid orange;"
                value="{{ request('searchxx') }}"
                autocomplete="off" />
            </form>
          </div>
        </div>
      </div>

      {{-- initial table + pagination (AJAX will replace this block) --}}
      @include('customerledgerhistory._items_block', ['cus' => $cus, 'searchxx' => $searchxx ?? ''])
    </div>

  </div> {{-- /container --}}
</div>    {{-- /main-content --}}

{{-- ======= Utilities ======= --}}
<script>
  function openPdfInNewTab(event, url) {
    event.preventDefault();
    var newTab = window.open(url, '_blank');
    if (newTab) newTab.focus();
  }
</script>

{{-- ======= AJAX live search + pagination ======= --}}
<script>
(function(){
  const input = document.getElementById('filtertext');
  const form  = document.getElementById('tableSearchForm');
  if (!input || !form) return;

  // Build URL with form params + ajax=1
  function buildAjaxUrl(base) {
    const params = new URLSearchParams(new FormData(form));
    params.set('ajax', '1');
    return base + '?' + params.toString();
  }

  async function fetchBlock(url) {
    try {
      const res  = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const json = await res.json();
      if (json.html) {
        const container = document.getElementById('itemsBlock');
        if (container) container.outerHTML = json.html;

        // Clean the URL (remove ajax=1) but keep query
        const clean = new URL(url, window.location.origin);
        clean.searchParams.delete('ajax');
        window.history.replaceState({}, '', clean.toString());

        bindPagination();   // re-bind after DOM replace
        input.focus();      // keep focus & caret
        const v = input.value || '';
        try { input.setSelectionRange(v.length, v.length); } catch(e){}
      }
    } catch (e) {
      console.error('AJAX load failed:', e);
    }
  }

  // Debounced search-as-you-type
  let t = null;
  input.addEventListener('input', () => {
    clearTimeout(t);
    t = setTimeout(() => fetchBlock(buildAjaxUrl(form.action)), 250);
  });

  // Enter submits immediately via AJAX
  input.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') { e.preventDefault(); fetchBlock(buildAjaxUrl(form.action)); }
  });

  // Intercept pagination links to load via AJAX
  function bindPagination() {
    const wrap = document.getElementById('itemsPaginationWrap');
    if (!wrap) return;

    wrap.querySelectorAll('.pagination a, a.page-link').forEach(a => {
      a.addEventListener('click', (e) => {
        e.preventDefault();
        const href = a.getAttribute('href');
        if (!href) return;

        const url = new URL(href, window.location.origin);
        // Merge current filters with the page param in href
        const params = new URLSearchParams(new FormData(form));
        params.forEach((v,k) => url.searchParams.set(k, v));
        url.searchParams.set('ajax','1');

        fetchBlock(url.toString());
      });
    });
  }

  // Bind on first load
  bindPagination();
})();
</script>
@endsection
