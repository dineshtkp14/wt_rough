<div id="itemsBlock">
  <div class="old-price-table-wrap">
    <table class="old-price-table">
      <thead>
        <tr>
          <th>#</th>
          @if(Auth::check() && Auth::user()->email == 'dineshtkp14@gmail.com')
            <th>AD Date</th>
          @endif
          <th>Nepali Date</th>
          <th>Bill No</th>
          <th>Item Name</th>
          <th>Unstocked Name</th>
          <th>Quantity</th>
          <th class="text-end">Cost Price</th>
          <th class="text-end">Sold Price</th>
        </tr>
      </thead>
      <tbody>
        @if ($cus->isNotEmpty())
          @foreach ($cus as $item)
            <tr class="{{ date('Y-m-d', strtotime($item->date)) === date('Y-m-d') ? 'old-price-today-row' : '' }}">
              <td>{{ ($cus->currentPage() - 1) * $cus->perPage() + $loop->iteration }}</td>
              @if(Auth::check() && Auth::user()->email == 'dineshtkp14@gmail.com')
                <td>{{ $item->date }}</td>
              @endif
              <td class="label-nep">{{ \App\Support\NepaliDate::adToBsString($item->date ?? now()->toDateString(), 'en') }}</td>
              <td>{{ $item->invoiceid }}</td>
              <td>{{ $item->itemname ?: '-' }}</td>
              <td>{{ $item->unstockedname ?: '-' }}</td>
              <td>{{ $item->quantity }}-{{ $item->unit }}</td>
              <td class="text-end">{{ $item->itemdlp !== null ? number_format((float) $item->itemdlp, 2) : '-' }}</td>
              <td class="text-end"><span class="sold-price-badge">Rs {{ number_format((float) $item->price, 2) }}</span></td>
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="9" class="old-price-empty">No Record Found.</td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>

  <div class="old-price-pagination" id="itemsPaginationWrap">
    {{ $cus->appends(request()->only(['customerid','date1','date2','searchxx']))->links() }}
  </div>

  <style>
    .old-price-table-wrap {
      overflow-x: auto;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      background: #ffffff;
    }

    .old-price-table {
      width: 100%;
      min-width: 1120px;
      margin: 0;
      border-collapse: collapse;
      table-layout: fixed;
    }

    .old-price-table thead {
      display: table-header-group !important;
    }

    .old-price-table tbody {
      display: table-row-group !important;
      height: auto !important;
      overflow: visible !important;
    }

    .old-price-table tr {
      display: table-row !important;
      width: auto !important;
    }

    .old-price-table th,
    .old-price-table td {
      display: table-cell !important;
      padding: 12px 10px;
      border: 1px solid #cbd5e1 !important;
      font-size: 16px;
      vertical-align: middle;
      overflow-wrap: anywhere;
    }

    .old-price-table th {
      position: sticky;
      top: 0;
      z-index: 1;
      background: #5d5ced;
      color: #ffffff;
      font-weight: 900;
      text-transform: uppercase;
    }

    .old-price-table tbody tr:nth-child(even) td {
      background: #f8fafc;
    }

    .old-price-table tbody tr:hover td {
      background: #ecfeff;
    }

    .old-price-table tbody tr.old-price-today-row td {
      background: #dc2626 !important;
      color: #ffffff !important;
      font-weight: 900;
    }

    .sold-price-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 78px;
      padding: 8px 10px;
      border-radius: 8px;
      background: #111827;
      color: #ffffff;
      font-weight: 900;
      white-space: nowrap;
    }

    .old-price-empty {
      padding: 42px !important;
      color: #64748b;
      font-weight: 900;
      text-align: center;
    }

    .old-price-pagination {
      padding: 12px 0;
      color: #64748b;
      font-weight: 800;
    }
  </style>
</div>
