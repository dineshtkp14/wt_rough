<?php

namespace App\Http\Controllers;

use App\Models\VatCustomer;
use App\Models\VatFirm;
use Illuminate\Http\Request;

class VatCustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));
        if ($request->expectsJson()) {
            return response()->json(VatCustomer::query()
                ->when($search !== '', fn ($query) => $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('pan_no', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%");
                }))->latest()->limit(100)->get(['id', 'name', 'address', 'pan_no', 'phone']));
        }
        $customers = VatCustomer::query()
            ->when($search !== '', fn ($query) => $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('pan_no', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('vat-system.customers.index', compact('customers', 'search'));
    }

    public function create()
    {
        return view('vat-system.customers.form', ['customer' => null]);
    }

    public function store(Request $request)
    {
        $customer = VatCustomer::create($this->validated($request) + ['firm_id' => null,
            'added_by' => session('user_email') ?: auth()->user()?->email,
        ]);

        return redirect()->route('vat-system.customers.index')->with('success', 'VAT customer added successfully.');
    }

    public function edit(VatCustomer $customer)
    {
        return view('vat-system.customers.form', compact('customer'));
    }

    public function update(Request $request, VatCustomer $customer)
    {
        $customer->update($this->validated($request));

        return redirect()->route('vat-system.customers.index')->with('success', 'VAT customer updated successfully.');
    }

    public function destroy(VatCustomer $customer)
    {
        if ($customer->salesInvoices()->exists()) {
            return redirect()->route('vat-system.customers.index')->with('error', 'This customer cannot be deleted because sales invoices are saved against them. Delete those invoices first.');
        }

        $customer->delete();

        return redirect()->route('vat-system.customers.index')->with('success', 'VAT customer deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'pan_no' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
