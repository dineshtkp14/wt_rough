@php
  // English dateलाई साफ "YYYY-MM-DD" बनाउने
  $adDate = \Carbon\Carbon::parse($item->date)->format('Y-m-d');
@endphp


<div id="itemsBlock">
    <div class="card-body">
      <table>
        <thead>
          <tr>
            <th>Date</th>
         
            <th>Bill No</th>
            {{-- <th>Name</th> --}}
            <th>Items Name</th>
            <th>Unstocked Name</th>
            <th>Quantity</th>
            <th>Cost Price</th>
            <th>Sold Price</th>
          </tr>
        </thead>
        <tbody>
          @if ($cus->isNotEmpty())
          @foreach ($cus as $item)
          @php
              // Force clean AD date as YYYY-MM-DD (handles timestamps too)
              $adDate = \Carbon\Carbon::parse($item->date)->format('Y-m-d');
          @endphp
          <tr @if (date('Y-m-d', strtotime($item->date)) === date('Y-m-d')) style="font-weight:bold;color:white;background:red;" @endif>
            <td class="bs-date" data-ad="{{ $adDate }}">{{ $adDate }}</td>
        
            <td>{{ $item->invoiceid }}</td>
            <td>{{ $item->itemname ?: '-' }}</td>
            <td>{{ $item->unstockedname ?: '-' }}</td>
            <td>{{ $item->quantity }}-{{ $item->unit }}</td>
            <td>{{ $item->itemdlp }}</td>
            <td><button class="btn btn-dark">{{ $item->price }}</button></td>
          </tr>
        @endforeach
        
          @else
            <tr>
              <td colspan="13" class="text-center py-4"><h3>No Record Found.</h3></td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
  
    <div class="card-footer text-muted" id="itemsPaginationWrap">
      {{ $cus->appends(request()->only(['customerid','date1','date2','searchxx']))->links() }}
    </div>
  </div>
  
  <script>
    function convertBsDates() {
      const conv = new NepaliDateConverter();
      const nepDigits = {'0':'०','1':'१','2':'२','3':'३','4':'४','5':'५','6':'६','7':'७','8':'८','9':'९'};
      const pad = n => String(n).padStart(2,'0');
    
      document.querySelectorAll('.bs-date').forEach(el => {
        const ad = el.getAttribute('data-ad'); // "YYYY-MM-DD"
        if (!ad) return;
        const [y,m,d] = ad.split('-').map(Number);
        const bs = conv.adToBs(y, m, d);
        let out = `${bs.year}-${pad(bs.month)}-${pad(bs.day)}`;
        out = out.replace(/[0-9]/g, d => nepDigits[d]); // Nepali digits
        el.textContent = out;
      });
    }
    
    document.addEventListener('DOMContentLoaded', convertBsDates);
    </script>
    