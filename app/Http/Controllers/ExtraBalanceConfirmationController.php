<?php

namespace App\Http\Controllers;

use App\Models\VatFirm;
use App\Models\ExtraBalanceConfirmation;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ExtraBalanceConfirmationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        $search = trim((string) request('search'));
        return view('extra-balance-confirmation.form', [
            'firms' => VatFirm::where('is_active', true)->orderBy('name')->get(),
            'todayBs' => \App\Support\NepaliDate::adToBsString(now()->toDateString(), 'en'),
            'confirmation' => null,
            'confirmations' => $this->confirmations($search), 'search' => $search,
        ]);
    }

    public function generate(Request $request)
    {
        $data = $this->validated($request);
        $data['firm'] = VatFirm::findOrFail($data['firm_id']);
        return view('extra-balance-confirmation.letter', compact('data'));
    }

    public function storeFirm(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'pan_no' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
        ]);

        $firm = VatFirm::create($data + ['is_active' => true]);

        return response()->json(['id' => $firm->id, 'name' => $firm->name]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $confirmation = ExtraBalanceConfirmation::create($data);
        return redirect()->route('extra-balance-confirmation.edit', $confirmation)->with('success', 'Balance confirmation saved successfully.');
    }

    public function edit(ExtraBalanceConfirmation $confirmation)
    {
        $search = trim((string) request('search'));
        return view('extra-balance-confirmation.form', [
            'firms' => VatFirm::where('is_active', true)->orderBy('name')->get(),
            'todayBs' => \App\Support\NepaliDate::adToBsString(now()->toDateString(), 'en'),
            'confirmation' => $confirmation,
            'confirmations' => $this->confirmations($search), 'search' => $search,
        ]);
    }

    public function update(Request $request, ExtraBalanceConfirmation $confirmation)
    {
        $data = $this->validated($request);
        $confirmation->update($data);
        return redirect()->route('extra-balance-confirmation.edit', $confirmation)->with('success', 'Balance confirmation updated successfully.');
    }

    public function show(ExtraBalanceConfirmation $confirmation)
    {
        $data = $confirmation->toArray();
        $data['firm'] = $confirmation->firm;
        return view('extra-balance-confirmation.letter', compact('data'));
    }

    public function download(ExtraBalanceConfirmation $confirmation)
    {
        $data = $confirmation->toArray();
        $data['firm'] = $confirmation->firm;

        return Pdf::setOptions(['dpi' => 150, 'defaultFont' => 'DejaVu Sans'])
            ->loadView('extra-balance-confirmation.letter', ['data' => $data, 'pdfMode' => true])
            ->setPaper('a4', 'portrait')
            ->download('balance-confirmation-' . $confirmation->id . '.pdf');
    }

    public function destroy(ExtraBalanceConfirmation $confirmation)
    {
        $confirmation->delete();
        return back()->with('success', 'Balance confirmation deleted successfully.');
    }

    private function confirmations(string $search)
    {
        return ExtraBalanceConfirmation::with('firm')
            ->when($search !== '', fn ($query) => $query->where(function ($q) use ($search) {
                $q->where('party_name', 'like', "%{$search}%")
                    ->orWhere('party_vat_no', 'like', "%{$search}%")
                    ->orWhere('party_address', 'like', "%{$search}%")
                    ->orWhere('party_phone', 'like', "%{$search}%")
                    ->orWhere('date_bs', 'like', "%{$search}%")
                    ->orWhereHas('firm', fn ($firm) => $firm->where('name', 'like', "%{$search}%"));
            }))->latest()->limit(100)->get();
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'firm_id' => ['required', 'exists:vat_firms,id'],
            'party_name' => ['required', 'string', 'max:150'],
            'party_vat_no' => ['nullable', 'string', 'max:50'],
            'party_address' => ['nullable', 'string', 'max:255'],
            'party_phone' => ['nullable', 'string', 'max:30'],
            'date_bs' => ['required', 'regex:/^\d{4}-\d{1,2}-\d{1,2}$/'],
            'period_from_bs' => ['nullable', 'regex:/^\d{4}-\d{1,2}-\d{1,2}$/'],
            'period_to_bs' => ['nullable', 'regex:/^\d{4}-\d{1,2}-\d{1,2}$/'],
            'purchase_exempted' => ['nullable', 'numeric'], 'purchase_taxable' => ['nullable', 'numeric'], 'purchase_vat' => ['nullable', 'numeric'],
            'purchase_return_exempted' => ['nullable', 'numeric'], 'purchase_return_taxable' => ['nullable', 'numeric'], 'purchase_return_vat' => ['nullable', 'numeric'],
            'sales_exempted' => ['nullable', 'numeric'], 'sales_taxable' => ['nullable', 'numeric'], 'sales_vat' => ['nullable', 'numeric'],
            'sales_return_exempted' => ['nullable', 'numeric'], 'sales_return_taxable' => ['nullable', 'numeric'], 'sales_return_vat' => ['nullable', 'numeric'],
            'opening_balance' => ['nullable', 'numeric'], 'closing_balance' => ['nullable', 'numeric'],
        ]);

        foreach ([
            'purchase_exempted', 'purchase_taxable', 'purchase_vat',
            'purchase_return_exempted', 'purchase_return_taxable', 'purchase_return_vat',
            'sales_exempted', 'sales_taxable', 'sales_vat',
            'sales_return_exempted', 'sales_return_taxable', 'sales_return_vat',
            'opening_balance', 'closing_balance',
        ] as $field) {
            $data[$field] = (float) ($data[$field] ?? 0);
        }

        return $data;
    }
}
