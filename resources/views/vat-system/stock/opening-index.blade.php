@extends('layouts.master')

@section('content')
<div class="main-content" style="background:#f1f5fb;min-height:calc(100vh - 70px)">
    <div class="container-fluid p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div><h1 class="fw-bold text-primary mb-1"><i class="fa fa-list-check me-2"></i>Opening Stock List</h1><p class="text-muted mb-0">View, edit, or delete opening stock for <strong>{{ $firm->name }}</strong>.</p></div>
            <div class="d-flex gap-2">
                <a href="{{ route('vat-system.stock.opening.create') }}" class="btn btn-primary"><i class="fa fa-plus me-1"></i>Add Opening Stock</a>
                <a href="{{ route('vat-system.stock.index') }}" class="btn btn-outline-secondary"><i class="fa fa-arrow-left me-1"></i>Back to Stock</a>
            </div>
        </div>
        @if(session('success'))<div class="alert alert-success shadow-sm">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger shadow-sm">{{ $errors->first() }}</div>@endif
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h4 class="mb-0 fw-bold">Saved Opening Stock</h4></div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 align-middle">
                    <thead class="table-primary"><tr><th>Item</th><th>HSN</th><th>Unit</th><th>Opening Quantity</th><th>Purchase Rate</th><th>Sale Rate</th><th>Reorder Level</th><th>Added Date</th><th>Actions</th></tr></thead>
                    <tbody>
                    @forelse($openingMovements as $movement)
                        <tr>
                            <td><strong>{{ $movement->stock?->item_name }}</strong></td>
                            <td>{{ $movement->stock?->hs_code ?: '-' }}</td>
                            <td>{{ $movement->stock?->unit }}</td>
                            <td>{{ number_format((float)$movement->quantity,3) }}</td>
                            <td>Rs {{ number_format((float)$movement->rate,2) }}</td>
                            <td>Rs {{ number_format((float)($movement->stock?->sale_rate ?? 0),2) }}</td>
                            <td>{{ number_format((float)($movement->stock?->reorder_level ?? 0),3) }}</td>
                            <td>{{ optional($movement->created_at)->format('Y-m-d H:i') }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('vat-system.stock.opening.edit',$movement) }}" class="btn btn-sm btn-outline-primary"><i class="fa fa-pen me-1"></i>Edit</a>
                                <form method="post" action="{{ route('vat-system.stock.opening.destroy',$movement) }}" class="d-inline" onsubmit="return confirm('Delete this opening stock entry? The stock quantity will be reduced.');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash me-1"></i>Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-5">No opening stock entries saved yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
