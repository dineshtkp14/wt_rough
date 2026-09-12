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
        $catalogItems = $this->catalogItems();
        return view('vat-system.create', ['customers' => VatCustomer::orderBy('name')->get(), 'firms' => VatFirm::where('is_active', true)->orderBy('name')->get(), 'catalogItems' => $catalogItems, 'catalogJson' => $this->catalogJson($catalogItems)]);
    }

    public function index()
    {
        $bills = VatSystemBill::with(['customer', 'items'])->latest('bill_date')->latest('id')->paginate(15);

        return view('vat-system.bills.index', compact('bills'));
    }

    public function edit(VatSystemBill $bill)
    {
        $bill->load('items');

        return view('vat-system.create', [
            'customers' => VatCustomer::orderBy('name')->get(),
            'firms' => VatFirm::where('is_active', true)->orderBy('name')->get(),
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
        $purchased = CompanyBillItem::query()->select('item_name', 'hs_code', 'rate', 'unit')->get()->map(fn ($item) => (object) ['itemsname' => $item->item_name, 'mrp' => $item->rate, 'unit' => $item->unit, 'hs_code' => $item->hs_code]);

        return $purchased->unique(fn ($item) => strtolower(trim($item->itemsname)))->sortBy('itemsname')->values();
    }

    private function catalogJson($catalogItems): string
    {
        return $catalogItems->map(fn ($item) => ['name'=>$item->itemsname,'price'=>$item->mrp,'unit'=>$item->unit,'hs_code'=>$item->hs_code ?? null])->values()->toJson();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:vat_customers,id'],
            'firm_id' => ['nullable', 'exists:vat_firms,id'],
            'seller_name' => ['required', 'string', 'max:150'],
            'seller_vat_no' => ['nullable', 'string', 'max:50'],
            'seller_pan_no' => ['nullable', 'string', 'max:50'],
            'seller_phone' => ['nullable', 'string', 'max:30'],
            'seller_address' => ['nullable', 'string', 'max:255'],
            'seller_email' => ['nullable', 'email', 'max:150'],
            'bill_no' => ['required', 'string', 'max:50', 'unique:vat_system_bills,bill_no'],
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
        $bill->load(['customer', 'items']);
        $bsDate = NepaliDate::adToBsString($bill->bill_date->format('Y-m-d'), 'en');
        return view('vat-system.bill', compact('bill', 'bsDate'));
    }

    public function update(Request $request, VatSystemBill $bill)
    {
        $data = $this->validatedBill($request, [Rule::unique('vat_system_bills', 'bill_no')->ignore($bill->id)]);

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
        DB::transaction(function () use ($bill) { $bill->load('items'); foreach ($bill->items as $item) $this->adjustStock($item->item_name,$item->unit,(float)$item->quantity,(float)$item->rate,'Sales invoice #'.$bill->bill_no.' deleted'); $bill->delete(); });

        return redirect()->route('vat-system.bills.index')->with('success', 'VAT bill deleted successfully.');
    }

    private function validatedBill(Request $request, array $billNoRules = []): array
    {
        return $request->validate([
            'customer_id' => ['required', 'exists:vat_customers,id'], 'firm_id' => ['nullable', 'exists:vat_firms,id'],
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

    private function adjustStock(string $name, string $unit, float $quantity, float $rate, string $reference): void
    {
        $stock = VatStock::firstOrCreate(['item_name'=>trim($name),'unit'=>trim($unit)], ['quantity'=>0,'purchase_rate'=>0,'sale_rate'=>$rate,'reorder_level'=>0]);
        $stock->increment('quantity', $quantity);
        $stock->update(['sale_rate'=>$rate]);
        $stock->movements()->create(['movement_type'=>$quantity < 0 ? 'sale' : 'sale_reversal','quantity'=>abs($quantity),'rate'=>$rate,'reference'=>$reference]);
    }
}
