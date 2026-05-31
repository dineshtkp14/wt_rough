@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content old-price-page">
  @yield('breadcrumb')

  <div class="container-fluid px-3 px-xl-4">
    <div class="card customer-card" id="customerCard" style="display:none;">
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

    <div class="old-price-top-grid {{ $cusinfoforpdfok->isEmpty() ? 'only-search' : '' }}">
      <section class="old-price-panel old-price-search-panel">
        <div class="old-price-section-title">
          <i class="fa-solid fa-magnifying-glass"></i>
          Find Customer Price History
        </div>

        <form action="{{ route('oldpricecheck') }}" method="get" id="chosendatepdfform">
          <div class="search-box old-price-customer-search">
            <input id="customerIdInput" name="customerid" type="hidden">
            <input
              type="text"
              id="searchCustomerInput"
              data-api="customer_search"
              autocomplete="off"
              class="search-input form-control @error('customerid') is-invalid @enderror"
              placeholder="Search Customer">
            @error('customerid')
              <p class="invalid-feedback m-0">{{ $message }}</p>
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

          <button type="submit" class="old-price-search-btn">
            <i class="fas fa-search"></i>
            Search Customer
          </button>
        </form>
      </section>

      @foreach ($cusinfoforpdfok as $customer)
        <section class="old-price-panel old-price-summary-panel">
          <div class="old-price-section-title">
            <i class="fa-solid fa-user"></i>
            Customer Summary
          </div>

          <h3>{{ $customer->name }}</h3>
          <p>{{ $customer->address ?: 'No address selected' }}</p>

          <div class="old-price-meta">
            <div><span>Phone</span><b>{{ $customer->phoneno ?: '-' }}</b></div>
            <div><span>Alternate Phone</span><b>{{ $customer->alternate_phoneno ?: '-' }}</b></div>
            <div><span>Customer ID</span><b>{{ $customer->id }}</b></div>
          </div>
        </section>
      @endforeach
    </div>

    <div class="old-price-toolbar">
      <div>
        <h4>Old Price Entries</h4>
        <span>{{ $cus->isNotEmpty() ? $cus->total() . ' records found' : 'No records found' }}</span>
      </div>

      <div class="old-price-toolbar-actions">
        <a href="{{ route('itemsales.create') }}" class="old-price-invoice-btn">
          <i class="fas fa-file-invoice"></i>
          Add New Invoice
        </a>

        <div class="old-price-due-card {{ ($allnotcash - $cts) < 0 ? 'is-negative' : '' }}">
          <span>Total Due Amount</span>
          <strong>Rs {{ number_format($allnotcash - $cts, 2) }}</strong>
        </div>

        <form method="get" action="{{ route('oldpricecheck') }}" id="tableSearchForm">
          @if(!empty($cid))  <input type="hidden" name="customerid" value="{{ $cid }}"> @endif
          @if(!empty($from)) <input type="hidden" name="date1" value="{{ $from }}"> @endif
          @if(!empty($to))   <input type="hidden" name="date2" value="{{ $to }}"> @endif

          <input
            type="text"
            name="searchxx"
            id="filtertext"
            class="form-control old-price-item-search"
            placeholder="Search item name"
            value="{{ request('searchxx') }}"
            autocomplete="off">
        </form>
      </div>
    </div>

    @include('customerledgerhistory._items_block', ['cus' => $cus, 'searchxx' => $searchxx ?? ''])
  </div>
</div>

<script>
  function openPdfInNewTab(event, url) {
    event.preventDefault();
    var newTab = window.open(url, '_blank');
    if (newTab) newTab.focus();
  }
</script>

<script>
(function(){
  const input = document.getElementById('filtertext');
  const form  = document.getElementById('tableSearchForm');
  if (!input || !form) return;

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

        const clean = new URL(url, window.location.origin);
        clean.searchParams.delete('ajax');
        window.history.replaceState({}, '', clean.toString());

        bindPagination();
        input.focus();
        const v = input.value || '';
        try { input.setSelectionRange(v.length, v.length); } catch(e){}
      }
    } catch (e) {
      console.error('AJAX load failed:', e);
    }
  }

  let t = null;
  input.addEventListener('input', () => {
    clearTimeout(t);
    t = setTimeout(() => fetchBlock(buildAjaxUrl(form.action)), 250);
  });

  input.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      fetchBlock(buildAjaxUrl(form.action));
    }
  });

  function bindPagination() {
    const wrap = document.getElementById('itemsPaginationWrap');
    if (!wrap) return;

    wrap.querySelectorAll('.pagination a, a.page-link').forEach(a => {
      a.addEventListener('click', (e) => {
        e.preventDefault();
        const href = a.getAttribute('href');
        if (!href) return;

        const url = new URL(href, window.location.origin);
        const params = new URLSearchParams(new FormData(form));
        params.forEach((v,k) => url.searchParams.set(k, v));
        url.searchParams.set('ajax','1');

        fetchBlock(url.toString());
      });
    });
  }

  bindPagination();
})();
</script>

<style>
  .old-price-page {
    flex: 1 1 auto;
    width: 100%;
  }

  .old-price-top-grid {
    display: grid;
    grid-template-columns: minmax(360px, 520px) minmax(420px, 1fr);
    gap: 18px;
    margin-bottom: 22px;
  }

  .old-price-top-grid.only-search {
    grid-template-columns: minmax(360px, 760px);
  }

  .old-price-panel {
    padding: 18px;
    border: 1px solid #dbe3ef;
    border-radius: 8px;
    background: #ffffff;
    box-shadow: 0 12px 28px rgba(15, 23, 42, .07);
  }

  .old-price-search-panel {
    border-top: 5px solid #0f8f5f;
  }

  .old-price-summary-panel {
    border-top: 5px solid #5d5ced;
  }

  .old-price-section-title {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    color: #64748b;
    font-size: 13px;
    font-weight: 900;
    letter-spacing: .02em;
    text-transform: uppercase;
  }

  .old-price-customer-search {
    position: relative;
    width: 100%;
  }

  .old-price-customer-search .search-input {
    width: 100%;
    min-height: 54px;
    padding-left: 46px;
    border-color: #cbd5e1;
    font-size: 20px;
  }

  .old-price-search-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    width: 100%;
    min-height: 62px;
    margin-top: 16px;
    border: 0;
    border-radius: 6px;
    background: #1f2933;
    color: #ffffff;
    font-size: 24px;
    font-weight: 900;
  }

  .old-price-search-btn:hover {
    background: #111827;
  }

  .old-price-summary-panel h3 {
    margin: 0 0 4px;
    color: #111827;
    font-size: 26px;
    font-weight: 900;
    line-height: 1.05;
  }

  .old-price-summary-panel p {
    margin: 0;
    color: #475569;
    font-size: 18px;
  }

  .old-price-meta {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
    margin-top: 14px;
  }

  .old-price-meta div {
    padding: 10px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    background: #f8fafc;
  }

  .old-price-meta span {
    display: block;
    color: #64748b;
    font-size: 12px;
    font-weight: 900;
    text-transform: uppercase;
  }

  .old-price-meta b {
    display: block;
    margin-top: 3px;
    color: #111827;
    font-size: 16px;
    overflow-wrap: anywhere;
  }

  .old-price-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin: 18px 0 10px;
  }

  .old-price-toolbar h4 {
    margin: 0;
    color: #111827;
    font-size: 22px;
    font-weight: 900;
  }

  .old-price-toolbar span {
    color: #64748b;
    font-size: 14px;
    font-weight: 800;
  }

  .old-price-toolbar-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
    justify-content: flex-end;
    gap: 10px;
  }

  .old-price-invoice-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 50px;
    padding: 10px 14px;
    border-radius: 6px;
    background: #db2777;
    color: #ffffff !important;
    font-weight: 900;
    text-decoration: none;
  }

  .old-price-due-card {
    min-width: 180px;
    padding: 8px 12px;
    border-radius: 8px;
    background: #138c55;
    color: #ffffff;
  }

  .old-price-due-card.is-negative {
    background: #dc2626;
  }

  .old-price-due-card span {
    display: block;
    color: rgba(255,255,255,.86);
    font-size: 11px;
    font-weight: 900;
    text-transform: uppercase;
  }

  .old-price-due-card strong {
    display: block;
    margin-top: 2px;
    font-size: 19px;
    line-height: 1.1;
  }

  .old-price-item-search {
    min-height: 50px;
    max-width: 300px;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px;
    font-size: 18px;
    font-weight: 800;
  }

  @media (max-width: 1100px) {
    .old-price-top-grid,
    .old-price-top-grid.only-search {
      grid-template-columns: 1fr;
    }

    .old-price-toolbar {
      align-items: stretch;
      flex-direction: column;
    }

    .old-price-toolbar-actions {
      justify-content: flex-start;
    }
  }

  @media (max-width: 768px) {
    .old-price-meta {
      grid-template-columns: 1fr;
    }

    .old-price-toolbar-actions,
    .old-price-toolbar-actions form,
    .old-price-item-search {
      width: 100%;
      max-width: none;
    }
  }
</style>
@endsection
