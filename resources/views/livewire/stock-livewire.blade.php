<div class="stock-container">

    @auth
        @if (Auth::user()->email === 'dineshtkp14@gmail.com')
            <div class="d-flex justify-content-end mb-3">
                <button wire:click="generatePDF" class="stock-btn stock-btn-secondary">
                    <i class="fas fa-file-pdf"></i> DOWNLOAD PDF
                </button>
            </div>
        @endif
    @endauth

    <!-- Control Panel -->
    <div class="stock-control-panel">
        <form wire:submit.prevent="filterByFirm">
            <div class="stock-control-grid">
                <div class="firm-selector-group">
                    <label class="firm-selector-label">Choose Firm</label>
                    <select wire:model="firm_name" class="firm-selector @error('firm_name') error @enderror"
                        id="firm_name">
                        <option value="">Select Firm</option>
                        @foreach ($allfirmlist as $firm)
                            <option value="{{ $firm->nick_name }}">{{ $firm->firm_name }}</option>
                        @endforeach
                    </select>
                    @error('firm_name')
                        <p class="text-danger" style="font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <a href="{{ route('items.create') }}" class="stock-btn stock-btn-primary w-100"
                        style="text-decoration: none;">
                        <i class="fas fa-plus-circle"></i> Add New Items
                    </a>
                </div>

                <div>
                    <a href="{{ route('purorder.create') }}" class="stock-btn stock-btn-success w-100"
                        style="text-decoration: none;">
                        <i class="fas fa-cart-plus"></i> Make Order
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Stats Section -->
    <div class="stock-stats-section">
        <div class="stock-stats-header">
            <a href="{{ route('stocks.index') }}">
                <img src="https://img.icons8.com/glyph-neue/50/22c55e/plus-2-math.png" alt="Add" />
            </a>
            <span class="stock-stats-title">Stock Overview (Total: {{ $all->total() }})</span>
        </div>

        <div class="stock-stats-grid">
            <div class="stock-stat-card stock">
                <span class="stock-stat-label">Stock Items</span>
                <span class="stock-stat-value">{{ $cou }}</span>
            </div>
            <div class="stock-stat-card out">
                <span class="stock-stat-label">Out of Stock</span>
                <span class="stock-stat-value">{{ $totalnofoutofstock }}</span>
            </div>
            <div class="stock-stat-card warning">
                <span class="stock-stat-label">Warning Items</span>
                <span class="stock-stat-value">{{ $war }}</span>
            </div>
            <div class="stock-search-box">
                <i class="fas fa-search stock-search-icon"></i>
                <input type="text" class="stock-search-input" placeholder="Search items..." wire:model="searchTerm">
            </div>
        </div>

        <div class="stock-search-section">
            <div class="stock-search-hints">
                <span class="stock-hint">
                    Type <code>war</code> for warning items
                </span>
                <span class="stock-hint">
                    Type <code>out</code> for out of stock items
                </span>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="stock-table-container">
        <div class="stock-table-header">
            <h3><i class="fas fa-boxes me-2 text-warning"></i>Inventory Items</h3>
        </div>
        <div class="stock-table-wrapper">

            <table class="stock-table">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Item Id</th>
                        <th>Items Name</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Store Area</th>
                        <th>Firm Name</th>
                        <th>MRP</th>
                        <th>Extra</th>
                        <th>Warning</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sn = ($all->currentPage() - 1) * $all->perPage() + 1;
                    @endphp

                    @if ($all->count())
                        @foreach ($all as $i)
                            <tr>
                                <td><strong>#{{ $sn++ }}</strong></td>
                                <td>{{ $i->id }}</td>
                                <td><span class="stock-item-name">{{ $i->itemsname }}</span></td>
                                <td><span class="stock-quantity">{{ $i->quantity }}</span></td>
                                <td>{{ $i->unit }}</td>
                                <td>{{ $i->item_store_area }}</td>
                                <td><strong>{{ $i->firm_name }}</strong></td>
                                <td><strong style="color: var(--stock-primary);">Rs. {{ $i->mrp }}</strong></td>
                                <td>
                                    <button type="button" class="stock-btn stock-btn-secondary"
                                        style="padding: 0.4rem 0.75rem; font-size: 0.75rem;" data-bs-toggle="modal"
                                        data-bs-target="#exampleModal{{ $i->id }}">
                                        <i class="fas fa-info-circle"></i> Extra
                                    </button>
                                    <!-- Extra Details Modal -->
                                    <div class="modal fade" id="exampleModal{{ $i->id }}" tabindex="-1"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="stock-modal-header bg-primary text-light">
                                                    <h5 class="modal-title" id="exampleModalLabel"><i
                                                            class="fas fa-info-circle me-2"></i>Additional Details
                                                        (Excl. VAT 13%)</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="stock-modal-body">
                                                    <div class="stock-price-row">
                                                        <span class="stock-price-label">Cost Price</span>
                                                        <span class="stock-price-value">Rs. {{ $i->costprice }}</span>
                                                    </div>
                                                    <div class="stock-price-row">
                                                        <span class="stock-price-label">Wholesale Price</span>
                                                        <span class="stock-price-value">Rs.
                                                            {{ $i->wholesale_price }}</span>
                                                    </div>
                                                    <div class="stock-price-row">
                                                        <span class="stock-price-label">Competitive Retail Price</span>
                                                        <span class="stock-price-value">Rs.
                                                            {{ $i->com_Retail_price }}</span>
                                                    </div>
                                                    <div class="stock-price-row">
                                                        <span class="stock-price-label">Competitive Wholesale
                                                            Price</span>
                                                        <span class="stock-price-value success">Rs.
                                                            {{ $i->com_wholesale_price }}</span>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="stock-btn"
                                                        style="background: var(--stock-gray-light); color: white;"
                                                        data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                <td>
                                    <button type="button" class="stock-btn stock-btn-primary"
                                        style="padding: 0.4rem 0.75rem; font-size: 0.75rem;" data-bs-toggle="modal"
                                        data-bs-target="#eexampleModal{{ $i->id }}">
                                        <i class="fas fa-edit"></i> Update Price
                                    </button>

                                    <!-- Price Update Modal -->
                                    <div class="modal fade" id="eexampleModal{{ $i->id }}" tabindex="-1"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="stock-modal-header">
                                                    <h5 class="modal-title"><i class="fas fa-tags me-2"></i>Update
                                                        Prices</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="stock-modal-body">
                                                    <form id="updateForm{{ $i->id }}"
                                                        action="{{ route('stockpriceupdate', $i->id) }}"
                                                        method="post">
                                                        @csrf
                                                        @method('PUT')

                                                        <div class="stock-form-group">
                                                            <label for="wp" class="stock-form-label">Wholesale
                                                                Price Per (PCS/kg)</label>
                                                            <input type="text" id="wp" name="wp"
                                                                class="stock-form-input @error('wp') is-invalid @enderror"
                                                                value="{{ old('wp') ?? $i->wholesale_price }}"
                                                                required>
                                                            @error('wp')
                                                                <div class="text-danger"
                                                                    style="font-size: 0.875rem; margin-top: 0.25rem;">
                                                                    {{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="stock-form-group">
                                                            <label for="competetiveretail"
                                                                class="stock-form-label">Competitive Retail Sale Price
                                                                Per (PCS/kg)</label>
                                                            <input type="text" id="competetiveretail"
                                                                name="competetiveretail"
                                                                class="stock-form-input @error('competetiveretail') is-invalid @enderror"
                                                                value="{{ old('competetiveretail') ?? $i->com_Retail_price }}"
                                                                required>
                                                            @error('competetiveretail')
                                                                <div class="text-danger"
                                                                    style="font-size: 0.875rem; margin-top: 0.25rem;">
                                                                    {{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="stock-form-group">
                                                            <label for="competetivewholesale"
                                                                class="stock-form-label">Competitive Wholesale Sale
                                                                Price Per (PCS/kg)</label>
                                                            <input type="text" id="competetivewholesale"
                                                                name="competetivewholesale"
                                                                class="stock-form-input @error('competetivewholesale') is-invalid @enderror"
                                                                value="{{ old('competetivewholesale') ?? $i->com_wholesale_price }}"
                                                                required>
                                                            @error('competetivewholesale')
                                                                <div class="text-danger"
                                                                    style="font-size: 0.875rem; margin-top: 0.25rem;">
                                                                    {{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <button type="submit"
                                                            class="stock-btn stock-btn-success w-100">
                                                            <i class="fas fa-save me-2"></i>Update Prices
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>


                                <td><strong>{{ $i->showwarning }}</strong></td>
                                <td>
                                    @if ($i->quantity <= $i->showwarning && $i->quantity > 0)
                                        <span class="stock-badge stock-badge-warning">
                                            <i class="fas fa-exclamation-triangle"></i> Warning
                                        </span>
                                    @elseif($i->quantity <= 0)
                                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                            <span class="stock-badge stock-badge-danger">
                                                <i class="fas fa-times-circle"></i> Out of Stock
                                            </span>
                                            @if ($i->quantity < 0)
                                                <span class="stock-badge stock-badge-info">
                                                    <i class="fas fa-minus-circle"></i> Data in Minus
                                                </span>
                                            @endif
                                            <form id="removeForm{{ $i->id }}"
                                                action="{{ route('stocks.updateofs') }}" method="POST"
                                                style="margin-top: 0.25rem;">
                                                @csrf
                                                <input type="hidden" name="item_id" value="{{ $i->id }}">
                                                <button type="submit"
                                                    class="stock-action-btn stock-action-btn-remove"
                                                    onclick="confirmRemove({{ $i->id }})">
                                                    <i class="fas fa-trash"></i> Remove
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($i->quantity < 0)
                                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                            <span class="stock-badge stock-badge-info">
                                                <i class="fas fa-minus-circle"></i> Data in Minus
                                            </span>
                                            <form id="removeForm{{ $i->id }}"
                                                action="{{ route('stocks.updateofs') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="item_id" value="{{ $i->id }}">
                                                <button type="submit"
                                                    class="stock-action-btn stock-action-btn-remove"
                                                    onclick="confirmRemove({{ $i->id }})">
                                                    <i class="fas fa-trash"></i> Remove
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="stock-badge stock-badge-success">
                                            <i class="fas fa-check-circle"></i> Available
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="11">
                                <div class="stock-empty">
                                    <i class="fas fa-box-open"></i>
                                    <p>No records found</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="stock-pagination">
            {{ $all->links() }}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @foreach ($all as $item)
            var updateForm{{ $item->id }} = document.getElementById('updateForm{{ $item->id }}');
            updateForm{{ $item->id }}.addEventListener('submit', function(event) {
                var inputs = this.querySelectorAll('input[type="text"]');
                var isValid = true;

                inputs.forEach(function(input) {
                    if (!input.value.trim()) {
                        isValid = false;
                    }
                });

                if (!isValid) {
                    event.preventDefault();
                    event.stopPropagation();
                }
            });
        @endforeach
    });
</script>
