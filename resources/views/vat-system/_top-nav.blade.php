<nav class="vat-top-nav" aria-label="VAT System navigation">
    <div class="vat-top-nav-brand">
        <i class="fa fa-receipt"></i>
        <span>VAT System</span>
    </div>
    <div class="vat-top-nav-links">
        <a href="{{ route('vat-system.index') }}" class="{{ request()->routeIs('vat-system.index') ? 'active' : '' }}"><i class="fa fa-house"></i><span>Dashboard</span></a>
        <a href="{{ route('vat-system.create') }}" class="{{ request()->routeIs('vat-system.create', 'vat-system.bills.*') ? 'active' : '' }}"><i class="fa fa-file-invoice"></i><span>Sales Bills</span></a>
        <a href="{{ route('vat-system.company-bills.index') }}" class="{{ request()->routeIs('vat-system.company-bills.*', 'vat-system.suppliers.*') ? 'active' : '' }}"><i class="fa fa-building"></i><span>Purchase Bills</span></a>
        @if(Route::has('vat-system.purchase-party-ledger.companies'))
            <a href="{{ route('vat-system.purchase-party-ledger.companies') }}" class="{{ request()->routeIs('vat-system.purchase-party-ledger*', 'vat-system.purchase-balance-confirmation*') ? 'active' : '' }}"><i class="fa fa-book-open"></i><span>Purchase Ledger</span></a>
        @endif
        <a href="{{ route('vat-system.customers.index') }}" class="{{ request()->routeIs('vat-system.customers.*') ? 'active' : '' }}"><i class="fa fa-users"></i><span>Customers</span></a>
        <a href="{{ route('vat-system.stock.index') }}" class="{{ request()->routeIs('vat-system.stock.*') ? 'active' : '' }}"><i class="fa fa-boxes-stacked"></i><span>Stock</span></a>
        <a href="{{ route('vat-system.party-ledger.customers') }}" class="{{ request()->routeIs('vat-system.party-ledger*', 'vat-system.balance-confirmation*') ? 'active' : '' }}"><i class="fa fa-book"></i><span>Party Ledger</span></a>
    </div>
    <a href="{{ route('vat-system.firm.switch', ['next' => 'workspace']) }}" class="vat-top-nav-firm"><i class="fa fa-repeat"></i><span>Change Firm</span></a>
</nav>
