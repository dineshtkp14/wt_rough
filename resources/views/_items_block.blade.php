<div id="itemsBlock">
    <div class="card-body">
      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Created at</th>
            <th>Bill No</th>
            <th>Name</th>
            <th>Invoice Type</th>
            <th>Items Name</th>
            <th>Unstocked Name</th>
            <th>Quantity</th>
            <th>Cost Price</th>
            <th>Original Sell Price</th>
            <th>Sold Price</th>
            <th>Sub-Total</th>
            <th>Profit</th>
          </tr>
        </thead>
        <tbody>
          @if ($cus->isNotEmpty())
            @foreach ($cus as $item)
              <tr @if (date('Y-m-d', strtotime($item->date)) === date('Y-m-d')) style="font-weight:bold;color:white;background:red;" @endif>
                <td>{{ $item->date }}</td>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->invoiceid }}</td>
                <td>{{ $item->customername }}</td>
                <td>{{ $item->inv_type }}</td>
                <td>{{ $item->itemname ?: '-' }}</td>
                <td>{{ $item->unstockedname ?: '-' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->itemdlp }}</td>
                <td>{{ $item->itemprice ?: '-' }}</td>
                <td>{{ $item->price }}</td>
                <td>{{ $item->subtotal }}</td>
                <td>{{ !empty($item->itemdlp) ? ($item->price - $item->itemdlp) * $item->quantity : '-' }}</td>
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
  