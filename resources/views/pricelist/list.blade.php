@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
   

<div class="main-content price-list-page">
   
@yield('breadcrumb')

        @if (Session::has('success'))
                <div class="alert alert-success w-50">
                {{ Session::get('success') }}
                </div>
         @endif

<livewire:pricelistliveware/>


@livewireScripts
</div>
<style>
    .price-list-page > .container { max-width: 1680px; padding: 0; width: 100%; }
    .price-list-card { background: #fff; border: 1px solid #dbe3ef; border-radius: 14px; box-shadow: 0 14px 32px rgba(15,23,42,.08); overflow: hidden; }
    .price-list-toolbar { align-items: center; background: linear-gradient(135deg,#f8fafc,#eef4ff); border-bottom: 1px solid #dbe3ef; display: flex; gap: 16px; justify-content: space-between; padding: 18px 20px; }
    .price-list-heading { align-items: center; display: flex; gap: 12px; }
    .price-list-heading-icon { align-items: center; background: #4f46e5; border-radius: 10px; color: #fff; display: inline-flex; flex: 0 0 42px; font-size: 17px; height: 42px; justify-content: center; }
    .price-list-heading small { color: #64748b; display: block; font-size: 11px; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; }
    .price-list-heading strong { color: #172033; display: block; font-size: 21px; font-weight: 900; line-height: 1.2; }
    .price-list-toolbar-right { align-items: center; display: flex; gap: 10px; }
    .price-list-add-btn { align-items: center; background: #16a34a; border-radius: 9px; color: #fff; display: inline-flex; font-size: 14px; font-weight: 900; gap: 8px; min-height: 40px; padding: 0 14px; text-decoration: none; }
    .price-list-add-btn:hover { background: #15803d; color: #fff; }
    .price-list-search-wrap { position: relative; }
    .price-list-search-wrap i { color: #64748b; left: 13px; position: absolute; top: 13px; }
    .price-list-search { background: #fff; border: 1px solid #cbd5e1; border-radius: 9px; font-size: 14px; min-height: 40px; padding: 8px 12px 8px 38px; width: min(300px, 32vw); }
    .price-list-search:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.14); outline: 0; }
    .price-list-table-wrap { overflow-x: auto; padding: 18px 20px 20px; }
    .price-list-table { border-collapse: separate !important; border-spacing: 0; margin: 0 !important; min-width: 940px; table-layout: fixed; width: 100%; }
    .price-list-table th { background: #293b9e !important; border: 0 !important; color: #fff; font-size: 12px; font-weight: 900; letter-spacing: .04em; padding: 13px 14px !important; text-transform: uppercase; }
    .price-list-table th:first-child { border-radius: 8px 0 0 0; }
    .price-list-table th:last-child { border-radius: 0 8px 0 0; }
    .price-list-table td { background: #fff; border: 0 !important; border-bottom: 1px solid #e2e8f0 !important; color: #172033; font-size: 14px; font-weight: 700; padding: 15px 14px !important; vertical-align: middle; }
    .price-list-table tbody tr:nth-child(even) td { background: #f8fafc; }
    .price-list-table tbody tr:hover td { background: #eef4ff; }
    .price-list-table td:first-child { color: #4f46e5; font-weight: 900; }
    .price-list-table td:nth-child(5) { color: #64748b; font-weight: 600; }
    .price-list-action-cell { display: flex; flex-wrap: wrap; gap: 7px; }
    .price-list-table .btn { border: 0; border-radius: 8px; font-size: 12px; font-weight: 900; padding: 9px 12px; }
    .price-list-table .btn-primary { background: #2563eb; }
    .price-list-table .btn-info { background: #3b9bea; color: #fff; }
    .price-list-table .btn-danger { background: #dc3545; }
    .price-list-footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 13px 20px; }
    .price-list-footer nav { display: flex; justify-content: flex-end; }
    .price-list-footer .pagination { margin: 0; }
    .price-list-empty { color: #64748b; padding: 38px !important; text-align: center; }
    @media (max-width: 700px) { .price-list-toolbar { align-items: stretch; flex-direction: column; } .price-list-toolbar-right { flex-direction: column; } .price-list-search { width: 100%; } .price-list-add-btn { justify-content: center; width: 100%; } .price-list-table-wrap { padding: 12px; } }
</style>
@stop
