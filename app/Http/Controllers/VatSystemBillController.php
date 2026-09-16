<?php

namespace App\Http\Controllers;

use App\Models\VatCustomer;
use App\Models\VatSystemBill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Support\NepaliDate;
use App\Models\CompanyBillItem;
use App\Models\VatStock;
use App\Models\VatFirm;

class VatSystemBillController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function create()
    {
        if (!session('vat_firm_id')) return redirect()->route('vat-system.firm.select', ['next' => 'sales']);
        $catalogItems = $this->catalogItems();
        return view('vat-system.create', ['customers' => VatCustomer::orderBy('name')->get(), 'firms' => VatFirm::where('is_active', true)->orderBy('name')->get(), 'activeFirm' => VatFirm::find(session('vat_firm_id')), 'catalogItems' => $catalogItems, 'catalogJson' => $this->catalogJson($catalogItems)]);
    }

    public function selectFirm(Request $request)
    {
        return view('vat-system.firm-select', ['firms' => VatFirm::where('is_active', true)->orderBy('name')->get(), 'next' => $request->query('next', 'workspace')]);
    }

    public function setFirm(Request $request)
    {
        $data = $request->validate(['firm_id' => ['required', Rule::exists('vat_firms', 'id')->where(fn ($query) => $query->where('is_active', true))], 'next' => ['nullable', 'in:workspace,sales,purchase,stock']]);
        session(['vat_firm_id' => (int) $data['firm_id'], 'vat_workspace_ready' => true]);
        return redirect()->route(match($data['next'] ?? 'workspace') { 'purchase' => 'vat-system.company-bills.create', 'stock' => 'vat-system.stock.index', 'sales' => 'vat-system.create', default => 'vat-system.index' });
    }

    public function switchFirm(Request $request)
    {
        $currentId = (int) session('vat_firm_id');
        $firm = VatFirm::where('is_active', true)->whereKeyNot($currentId)->orderBy('id')->first();
        if (!$firm) return back()->with('error', 'No other active VAT firm is available.');
        session(['vat_firm_id' => $firm->id, 'vat_workspace_ready' => true]);
        $next = $request->query('next', 'workspace');
        $route = match($next) { 'purchase' => 'vat-system.company-bills.index', 'sales' => 'vat-system.bills.index', 'stock' => 'vat-system.stock.index', 'ledger' => 'vat-system.party-ledger.customers', default => 'vat-system.index' };
        return redirect()->route($route, $next === 'ledger' && $request->filled('fiscal_year') ? ['fiscal_year' => $request->query('fiscal_year')] : []);
    }

    public function index(Request $request)
    {
        $firm = VatFirm::find(session('vat_firm_id'));
        if (!$firm) return redirect()->route('vat-system.firm.select', ['next' => 'workspace']);
        $customer = $request->filled('customer_id') ? VatCustomer::find($request->integer('customer_id')) : null;
        $search = trim((string) $request->query('search'));
        $query = VatSystemBill::where('firm_id', $firm->id)->when($customer, fn($query) => $query->where('customer_id', $customer->id))->with(['customer', 'firm', 'items'])->when($search !== '', fn($q) => $q->where(function ($q) use ($search) {
            $q->where('bill_no', 'like', "%{$search}%")
                ->orWhere('bill_date', 'like', "%{$search}%")
                ->orWhere('payment_mode', 'like', "%{$search}%")
                ->orWhere('seller_name', 'like', "%{$search}%")
                ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('pan_no', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"));
        }))->latest('bill_date')->latest('id');
        if ($request->expectsJson()) return response()->json(['items' => $query->limit(100)->get()->map(fn($bill) => ['id' => $bill->id, 'bill_no' => $bill->bill_no, 'date' => $bill->bill_date->format('Y-m-d'), 'firm' => $bill->firm->name ?? $bill->seller_name, 'pan' => $bill->seller_pan_no ?: '-', 'customer' => $bill->customer->name ?? '-', 'customer_pan' => $bill->customer->pan_no ?? '', 'payment_mode' => $bill->payment_mode ?: '-', 'created_by' => $bill->added_by ?: '-', 'total' => $bill->items->sum(fn($item) => (float)$item->quantity * (float)$item->rate), 'taxable' => $bill->items->where('is_taxable', true)->sum(fn($item) => (float)$item->quantity * (float)$item->rate), 'discount' => (float)$bill->discount, 'show_url' => route('vat-system.bills.show', $bill), 'edit_url' => route('vat-system.bills.edit', $bill), 'delete_url' => route('vat-system.bills.destroy', $bill)]), 'total' => $query->count()]);
        $bills = $query->paginate(15)->withQueryString();

        return view('vat-system.bills.index', compact('bills', 'firm', 'customer', 'search'));
    }

    public function edit(VatSystemBill $bill)
    {
        abort_unless((int) $bill->firm_id === (int) session('vat_firm_id'), 404);
        $bill->load('items');

        return view('vat-system.create', [
            'customers' => VatCustomer::orderBy('name')->get(),
            'firms' => VatFirm::where('is_active', true)->orderBy('name')->get(),
            'activeFirm' => $bill->firm ?: VatFirm::find(session('vat_firm_id')),
            'catalogItems' => $catalogItems = $this->catalogItems(),
            'catalogJson' => $this->catalogJson($catalogItems),
            'bill' => $bill,
        ]);
    }

    public function itemSuggestions(Request $request)
    {
        $query = strtolower((string) $request->string('q'));
        return response()->json($this->catalogItems()->filter(fn ($item) => str_contains(strtolower($item->itemsname), $query))->take(15)->values());
    }

    private function catalogItems()
    {
        $purchased = CompanyBillItem::query()->whereHas('companyBill', fn ($query) => $query->where('firm_id', session('vat_firm_id')))->select('item_name', 'hs_code', 'rate', 'unit')->get()->map(fn ($item) => (object) ['itemsname' => $item->item_name, 'mrp' => $item->rate, 'unit' => $item->unit, 'hs_code' => $item->hs_code]);

        return $purchased->unique(fn ($item) => strtolower(trim($item->itemsname)))->sortBy('itemsname')->values();
    }

    private function catalogJson($catalogItems): string
    {
        return $catalogItems->map(fn ($item) => ['name'=>$item->itemsname,'price'=>$item->mrp,'unit'=>$item->unit,'hs_code'=>$item->hs_code ?? null])->values()->toJson();
    }

    public function store(Request $request)
    {
        $firm = $this->firmOrRedirect('sales');
        $request->merge(['firm_id' => $firm->id]);
        $this->normalizeBillDate($request);
        $data = $request->validate([
            'customer_id' => ['required', 'exists:vat_customers,id'],
            'firm_id' => ['required', Rule::exists('vat_firms', 'id')->where(fn ($query) => $query->where('is_active', true))],
            'seller_name' => ['required', 'string', 'max:150'],
            'seller_vat_no' => ['nullable', 'string', 'max:50'],
            'seller_pan_no' => ['nullable', 'string', 'max:50'],
            'seller_phone' => ['nullable', 'string', 'max:30'],
            'seller_address' => ['nullable', 'string', 'max:255'],
            'seller_email' => ['nullable', 'email', 'max:150'],
            'bill_no' => ['required', 'string', 'max:50', Rule::unique('vat_system_bills', 'bill_no')->where(fn($query) => $query->where('firm_id', session('vat_firm_id')))],
            'bill_date' => ['required', 'date'],
            'payment_mode' => ['nullable', 'string', 'max:50'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:200'],
            'items.*.hs_code' => ['nullable', 'string', 'max:50'],
            'items.*.unit' => ['required', 'string', 'max:30'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.rate' => ['required', 'numeric', 'min:0'],
            'items.*.is_taxable' => ['nullable', 'boolean'],
        ]);

        $bill = DB::transaction(function () use ($data) {
            $bill = VatSystemBill::create(collect($data)->except('items')->merge(['added_by' => session('user_email') ?: auth()->user()?->email])->all());
            foreach ($data['items'] as $item) { $saved=$bill->items()->create($item + ['is_taxable' => !array_key_exists('is_taxable', $item) || !empty($item['is_taxable'])]); $this->adjustStock($saved->item_name,$saved->unit,-(float)$saved->quantity,(float)$saved->rate,'Sales invoice #'.$bill->bill_no); }
            return $bill;
        });

        return redirect()->route('vat-system.bills.index')->with('success', 'VAT bill saved successfully.');
    }

    public function show(VatSystemBill $bill)
    {
        abort_unless((int) $bill->firm_id === (int) session('vat_firm_id'), 404);
        $bill->load(['customer', 'firm', 'items']);
        $bsDate = NepaliDate::adToBsString($bill->bill_date->format('Y-m-d'), 'en');
        return view('vat-system.bill', compact('bill', 'bsDate'));
    }

    public function update(Request $request, VatSystemBill $bill)
    {
        abort_unless((int) $bill->firm_id === (int) session('vat_firm_id'), 404);
        $request->merge(['firm_id' => $this->firmOrRedirect('sales')->id]);
        $this->normalizeBillDate($request);
        $data = $this->validatedBill($request, [Rule::unique('vat_system_bills', 'bill_no')->where(fn($query) => $query->where('firm_id', session('vat_firm_id')))->ignore($bill->id)]);

        DB::transaction(function () use ($data, $bill) {
            $bill->load('items');
            foreach ($bill->items as $old) $this->adjustStock($old->item_name,$old->unit,(float)$old->quantity,(float)$old->rate,'Sales invoice edit reversal');
            $bill->update(collect($data)->except('items')->all());
            $bill->items()->delete();
            foreach ($data['items'] as $item) { $saved=$bill->items()->create($item + ['is_taxable' => !array_key_exists('is_taxable', $item) || !empty($item['is_taxable'])]); $this->adjustStock($saved->item_name,$saved->unit,-(float)$saved->quantity,(float)$saved->rate,'Sales invoice #'.$bill->bill_no); }
        });

        return redirect()->route('vat-system.bills.index')->with('success', 'VAT bill updated successfully.');
    }

    public function destroy(VatSystemBill $bill)
    {
        abort_unless((int) $bill->firm_id === (int) session('vat_firm_id'), 404);
        DB::transaction(function () use ($bill) { $bill->load('items'); foreach ($bill->items as $item) $this->adjustStock($item->item_name,$item->unit,(float)$item->quantity,(float)$item->rate,'Sales invoice #'.$bill->bill_no.' deleted'); $bill->delete(); });

        return redirect()->route('vat-system.bills.index')->with('success', 'VAT bill deleted successfully.');
    }

    private function validatedBill(Request $request, array $billNoRules = []): array
    {
        return $request->validate([
            'customer_id' => ['required', 'exists:vat_customers,id'], 'firm_id' => ['required', Rule::exists('vat_firms', 'id')->where(fn ($query) => $query->where('is_active', true))],
            'seller_name' => ['required', 'string', 'max:150'], 'seller_vat_no' => ['nullable', 'string', 'max:50'],
            'seller_pan_no' => ['nullable', 'string', 'max:50'], 'seller_phone' => ['nullable', 'string', 'max:60'], 'seller_address' => ['nullable', 'string', 'max:255'], 'seller_email' => ['nullable', 'email', 'max:150'],
            'bill_no' => array_merge(['required', 'string', 'max:50'], $billNoRules),
            'bill_date' => ['required', 'date'], 'payment_mode' => ['nullable', 'string', 'max:50'],
            'discount' => ['nullable', 'numeric', 'min:0'], 'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'], 'items.*.item_name' => ['required', 'string', 'max:200'],
            'items.*.hs_code' => ['nullable', 'string', 'max:50'], 'items.*.unit' => ['required', 'string', 'max:30'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'], 'items.*.rate' => ['required', 'numeric', 'min:0'],
            'items.*.is_taxable' => ['nullable', 'boolean'],
        ]);
    }

    private function normalizeBillDate(Request $request): void
    {
        if ($request->input('bill_date_mode') !== 'bs') {
            return;
        }

        $bsDate = trim((string) $request->input('bill_date_bs'));
        if (!preg_match('/^(\\d{4})-(\\d{1,2})-(\\d{1,2})$/', $bsDate, $matches)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'bill_date_bs' => 'Enter a valid Nepali date in YYYY-MM-DD format.',
            ]);
        }

        try {
            $request->merge([
                'bill_date' => NepaliDate::bsToAdString((int) $matches[1], (int) $matches[2], (int) $matches[3]),
            ]);
        } catch (\Throwable $exception) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'bill_date_bs' => 'The selected Nepali date is not valid.',
            ]);
        }
    }

    private function firmOrRedirect(string $next): VatFirm
    {
        $firm = VatFirm::whereKey(session('vat_firm_id'))->where('is_active', true)->first();
        if (!$firm) {
            abort(redirect()->route('vat-system.firm.select', ['next' => $next])->with('error', 'Please select an active VAT firm before saving.'));
        }

        return $firm;
    }

    private function adjustStock(string $name, string $unit, float $quantity, float $rate, string $reference): void
    {
        $stock = VatStock::firstOrCreate(['firm_id'=>(int) session('vat_firm_id'),'item_name'=>trim($name),'unit'=>trim($unit)], ['quantity'=>0,'purchase_rate'=>0,'sale_rate'=>$rate,'reorder_level'=>0]);
        $before=(float)$stock->quantity;
        $after=$before+$quantity;
        $stock->update(['quantity'=>$after,'sale_rate'=>$rate]);
        $stock->movements()->create(['movement_type'=>$quantity < 0 ? 'sale' : 'sale_reversal','quantity'=>abs($quantity),'rate'=>$rate,'reference'=>$reference,'user_email'=>session('user_email')?:auth()->user()?->email,'quantity_before'=>$before,'quantity_after'=>$after]);
    }
}
