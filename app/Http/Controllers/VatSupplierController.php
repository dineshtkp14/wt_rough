<?php

namespace App\Http\Controllers;

use App\Models\VatFirm;
use App\Models\VatSupplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VatSupplierController extends Controller
{
    public function index(Request $request)
    {
        $firm = VatFirm::findOrFail(session('vat_firm_id'));
        $search = trim((string) $request->query('search'));
        $suppliers = VatSupplier::query()
            ->when($search !== '', fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('vat_no', 'like', "%{$search}%")
                    ->orWhere('pan_no', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%");
            }))->orderBy('name')->paginate(20)->withQueryString();
        return view('vat-system.suppliers.index', compact('suppliers', 'firm', 'search'));
    }

    public function create()
    {
        return view('vat-system.suppliers.form', ['supplier' => null, 'firm' => VatFirm::findOrFail(session('vat_firm_id'))]);
    }

    public function store(Request $request)
    {
        $firm = VatFirm::findOrFail(session('vat_firm_id'));
        VatSupplier::create($this->validated($request) + ['firm_id' => null]);
        return redirect()->route('vat-system.suppliers.index')->with('success', 'Supplier added successfully.');
    }

    public function edit(VatSupplier $supplier)
    {
        return view('vat-system.suppliers.form', ['supplier' => $supplier, 'firm' => VatFirm::findOrFail(session('vat_firm_id'))]);
    }

    public function update(Request $request, VatSupplier $supplier)
    {
        $supplier->update($this->validated($request, null, $supplier->id));
        return redirect()->route('vat-system.suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(VatSupplier $supplier)
    {
        $supplier->delete();
        return back()->with('success', 'Supplier deleted successfully.');
    }

    private function validated(Request $request, ?int $firmId = null, ?int $ignore = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150', Rule::unique('vat_suppliers', 'name')->ignore($ignore)],
            'vat_no' => ['nullable', 'string', 'max:50'], 'pan_no' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:30'], 'address' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
