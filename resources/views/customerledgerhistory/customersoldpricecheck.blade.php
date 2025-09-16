@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')

<div class="main-content"> 
	@yield('breadcrumb')
<div class="container">

    <div class="card customer-card mb-4" id="customerCard" style="display: none;" style="">
        <div class="card-body">
            <h5 class="card-title">Customer Information</h5>
            <p>
                <span>ID: </span><span id="customerId">...</span>
            </p>
            <p class="card-text">
                <span>Name: </span><span id="customerName">...</span>
            </p>
            <p>
                <span>Addres: </span><span id="customerAddress">...</span>
            </p>
            <p>
                <span>E-mail: </span><span id="customerEmail">...</span>
            </p>
            <p>
                <span>PhoneNo: </span><span id="customerPhone">...</span>
            </p>
        </div>

        <div class="toogle-box p-3 d-flex justify-content-center align-items-center" id="toggleBox" data-toggle="close">
            <i class="fas fa-user"></i>
        </div>
    </div>
	
	<div class="row">
	  <form action="{{ route('oldpricecheck') }}" method="get" id="chosendatepdfform">

		<div class="row">
			<div class="mb-4" style="width: 300px;">
				<div class="search-box">
					<input id="customerIdInput" name="customerid" hidden>

					<input type="text" class="search-input @error('customerid') is-invalid @enderror" placeholder="Search Customer"
					id="searchCustomerInput" data-api="customer_search" autocomplete="off">
						@error('customerid')
							<p class="invalid-feedback m-0" style="position: absolute; bottom: -24px; left: 0;">{{ $message }}</p>
						@enderror  
						
					<i class="fas fa-search search-icon"> </i>
					<div class="result-wrapper" id="customerResultWrapper" style="display: none;">
						<div class="result-box d-flex justify-content-start align-items-center"
							id="customerLoadingResultBox">
							<i class="fas fa-spinner" id="spinnerIcon"> </i>
							<h1 class="m-0 px-2"> Loading</h1>
						</div>

						<div class="result-box d-flex justify-content-start align-items-center d-none"
							id="customerNotFoundResultBox">
							<i class="fas fa-triangle-exclamation"> </i>
							<h1 class="m-0 px-2"> Record Not Found</h1>
						</div>

						<div id="customerResultList">
						</div>
					</div>
				</div>	
			</div>
			<div class="col-md-3">
				
			</div>
			
			<div class="col-md-6">
				@if (!empty($cid))
				<a href="{{ route('cpayments.create', [
					'customerid' => $cid,
					'amount' => $allnotcash - $cts,
					'totaldueamountfornotclear' => $allnotcash - $cts,
					'cname' => 
						($cusinfoforpdfok[0]->name ?? '') . ' | ' . 
						($cusinfoforpdfok[0]->address ?? '') . ' | ' . 
						($cusinfoforpdfok[0]->phoneno ?? '')
				]) }}" class="">
				</a>
			@endif
				
			</div>
		</div>

		<div class="row">
			
			<div class="col-md-2">
				<button type="submit" class="btn btn-dark mx-2 w-100" name="">
					<i class="fas fa-search"></i> Search 
				</button>
								 </form>
			</div>
			

		</div>

	  </form>
	</div>

<div class="row">
  <div class="col-md-5">
	@foreach ($cusinfoforpdfok as $i)
		<div>
			CUSTOMER ID: <span style="font-size: 1.25rem; font-weight: 500;">{{$i->id}}</span><br>
			NAME: <span style="font-size: 1.25rem; font-weight: 500;">{{$i->name}}</span><br>
			ADDRESS: <span style="font-size: 1.25rem; font-weight: 500;">{{$i->address}}</span><br>
			PHONE NO: <span style="font-size: 1.25rem; font-weight: 500;">{{$i->phoneno}}, {{$i->alternate_phoneno}}</span><br>
			EMAIL: <span style="font-size: 1.25rem; font-weight: 500;">{{$i->email}}</span><br>
			NOTES: <span style="font-size: 1.25rem; font-weight: 500;">{{$i->remarks}}</span><br>
			
			
		</div>
	@endforeach
  </div>






  <div class="col-md-3">
	
	<span> 
		

	
		
		
	</span>
	
  </div>

</div>
	








</div>

<BR>



			{{$all->links()}}


<h2> -------------------------------table finals-----ssssssss   dd  d----------------------------------- </h2>

<div class="container">
    <div class="card ">
        <div class="card-header">
            <div class="row "> 
                <div class="col-md-6 ">     
                    <a href="" style="width: 200px; text-decoration:none" class=" text-center  h3 text-dark"> ITEMSALES  TABLE</a>
                    <a href="{{ route('itemsales.create') }}" class="btn btn-primary ms-5" style="background-color: #FF0066; border-color: #0be813; color: white; transition: background-color 0.3s, border-color 0.3s;"> <i class="fas fa-file-invoice"></i> ADD NEW INVOICE</a>
                </div>
                <div class="col-md-6 float-end">
                    <form method="get" action="{{ route('oldpricecheck') }}" id="tableSearchForm" class="float-end">
                      {{-- keep current filters on submit (optional) --}}
                      @if(!empty($cid))  <input type="hidden" name="customerid" value="{{ $cid }}"> @endif
                      @if(!empty($from)) <input type="hidden" name="date1" value="{{ $from }}">   @endif
                      @if(!empty($to))   <input type="hidden" name="date2" value="{{ $to }}">     @endif
                  
                      <input type="text"
                             name="searchxx"
                             id="filtertext"
                             class="form-control border-warning border-2"
                             placeholder="Search Here"
                             style="width: 250px;"
                             value="{{ request('searchxx') }}"  {{-- keeps value after reload --}}
                             autocomplete="off"
                             autofocus />
                    </form>
                  </div>
                  
            </div>
            @include('customerledgerhistory._items_block', ['cus' => $cus, 'searchxx' => $searchxx ?? ''])
{{-- /okokokoko --}}
        <div class="card-footer text-muted">
            {{ $cus->links() }}
        </div>
    </div>
</div>
</div>




</div>





<script>

	
function openPdfInNewTab(event, url) {
        event.preventDefault();
        var newTab = window.open(url, '_blank');
        newTab.focus();
    }


</script>
 

<script>
    (function(){
      const input = document.getElementById('filtertext');
      const form  = document.getElementById('tableSearchForm');
      const block = document.getElementById('itemsBlock') || document.querySelector('#itemsBlock') || document.body;
      if (!input || !form) return;
    
      // Build URL with form params + ajax=1
      function buildAjaxUrl(base) {
        const params = new URLSearchParams(new FormData(form));
        params.set('ajax', '1');
        return base + '?' + params.toString();
      }
    
      async function fetchBlock(url) {
        try {
          const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
          const json = await res.json();
          if (json.html) {
            // replace the whole block (table + pagination)
            const container = document.getElementById('itemsBlock');
            if (container) container.outerHTML = json.html;
    
            // update URL in address bar (remove ajax=1)
            const clean = new URL(url, window.location.origin);
            clean.searchParams.delete('ajax');
            window.history.replaceState({}, '', clean.toString());
    
            // re-bind pagination clicks after DOM update
            bindPagination();
            // keep focus + caret
            input.focus();
            const v = input.value || '';
            try { input.setSelectionRange(v.length, v.length); } catch(_) {}
          }
        } catch (e) {
          console.error(e);
        }
      }
    
      // Debounced live search
      let t = null;
      input.addEventListener('input', () => {
        clearTimeout(t);
        t = setTimeout(() => {
          fetchBlock(buildAjaxUrl(form.action));
        }, 250);
      });
    
      // Enter submits immediately via AJAX
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') { e.preventDefault(); fetchBlock(buildAjaxUrl(form.action)); }
      });
    
      // AJAX pagination (intercept page links)
      function bindPagination() {
        const wrap = document.getElementById('itemsPaginationWrap');
        if (!wrap) return;
        wrap.querySelectorAll('a.page-link, .pagination a').forEach(a => {
          a.addEventListener('click', (e) => {
            e.preventDefault();
            const href = a.getAttribute('href');
            if (!href) return;
    
            // merge the pagination href with current filters + ajax=1
            const url = new URL(href, window.location.origin);
            const params = new URLSearchParams(new FormData(form));
            // keep page from href, keep filters from form
            url.searchParams.forEach((v,k)=>{}); // no-op, we only need the "page" param that’s already in href
            params.forEach((v,k)=> url.searchParams.set(k,v));
            url.searchParams.set('ajax','1');
    
            fetchBlock(url.toString());
          });
        });
      }
    
      // bind once on first load
      bindPagination();
    })();
    </script>
    
    



</div>

@stop