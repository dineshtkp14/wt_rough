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
              <tr @if (date('Y-m-d', strtotime($item->date)) === date('Y-m-d')) style="font-weight:bold;color:white;background:red;" @endif>
                <td>{{ $item->date }}</td>
                <td>{{ $item->invoiceid }}</td>
                {{-- <td>{{ $item->customername }}</td> --}}
                <td>{{ $item->itemname ?: '-' }}</td>
                <td>{{ $item->unstockedname ?: '-' }}</td>
                <td>{{ $item->quantity }}-{{ $item->unit }}</td>
                <td>{{ $item->itemdlp }}</td>
                <td ><button class="btn btn-dark">{{ $item->price }}</button></td>
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
  