<?php

namespace App\Http\Controllers;

use App\Models\VatCustomer;
use App\Models\VatExtraCustomer;
use App\Models\VatSystemBill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
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

    public function storeExtraCustomer(Request $request)
    {
        $firm = $this->firmOrRedirect('sales');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
        $extra = VatExtraCustomer::create($data + ['firm_id' => $firm->id, 'added_by' => session('user_email') ?: auth()->user()?->email]);
        return response()->json(['id' => $extra->id, 'name' => $extra->name, 'address' => $extra->address, 'contact_name' => $extra->contact_name]);
    }

    public function updateExtraCustomer(Request $request, VatExtraCustomer $extraCustomer)
    {
        abort_unless((int) $extraCustomer->firm_id === (int) session('vat_firm_id'), 404);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
        $extraCustomer->update($data);
        return response()->json(['id' => $extraCustomer->id, 'name' => $extraCustomer->name, 'address' => $extraCustomer->address, 'contact_name' => $extraCustomer->contact_name]);
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
        $month = preg_match('/^\d{4}-\d{2}$/', (string) $request->query('month')) ? $request->query('month') : '';
        $bsMonth = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', (string) $request->query('bs_month')) ? $request->query('bs_month') : '';
        $bsMonthRange = $this->bsMonthRange($bsMonth);
        $bsFrom = preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', (string) $request->query('bs_from')) ? $request->query('bs_from') : '';
        $bsTo = preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', (string) $request->query('bs_to')) ? $request->query('bs_to') : '';
        $bsDateRange = $this->bsDateRange($bsFrom, $bsTo);
        $query = VatSystemBill::where('firm_id', $firm->id)->when($customer, fn($query) => $query->where('customer_id', $customer->id))->with(['customer', 'extraCustomer', 'firm', 'items'])->when($search !== '', fn($q) => $q->where(function ($q) use ($search) {
            $q->where('bill_no', 'like', "%{$search}%")
                ->orWhere('bill_date', 'like', "%{$search}%")
                ->orWhere('seller_name', 'like', "%{$search}%")
                ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('pan_no', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"))
                ->orWhereHas('extraCustomer', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }))->when($month, fn($q) => $q->whereBetween('bill_date', [Carbon::createFromFormat('Y-m', $month)->startOfMonth()->toDateString(), Carbon::createFromFormat('Y-m', $month)->endOfMonth()->toDateString()]))->when($bsMonthRange, fn($q) => $q->whereBetween('bill_date', $bsMonthRange))->when($bsDateRange, fn($q) => $q->whereBetween('bill_date', $bsDateRange))->latest('bill_date')->latest('id');
        if ($request->expectsJson()) return response()->json(['items' => $query->limit(100)->get()->map(fn($bill) => ['id' => $bill->id, 'bill_no' => $bill->bill_no, 'date' => $bill->bill_date->format('Y-m-d'), 'firm' => $bill->firm->name ?? $bill->seller_name, 'pan' => $bill->seller_pan_no ?: '-', 'customer' => $bill->customer->name ?? $bill->extraCustomer->name ?? '-', 'customer_pan' => $bill->customer->pan_no ?? '', 'created_by' => $bill->added_by ?: '-', 'total' => $bill->items->sum(fn($item) => (float)$item->quantity * (float)$item->rate), 'taxable' => $bill->items->where('is_taxable', true)->sum(fn($item) => (float)$item->quantity * (float)$item->rate), 'discount' => (float)$bill->discount, 'show_url' => route('vat-system.bills.show', $bill), 'edit_url' => route('vat-system.bills.edit', $bill), 'delete_url' => route('vat-system.bills.destroy', $bill)]), 'total' => $query->count()]);
        $grandTotal = (clone $query)->get()->sum(fn ($bill) => $bill->items->sum(fn ($item) => (float) $item->quantity * (float) $item->rate) - (float) $bill->discount + round($bill->items->where('is_taxable', true)->sum(fn ($item) => (float) $item->quantity * (float) $item->rate) * .13, 2));
        $bills = $query->paginate(15)->withQueryString();

        return view('vat-system.bills.index', compact('bills', 'firm', 'customer', 'search', 'month', 'bsMonth', 'bsFrom', 'bsTo', 'grandTotal'));
    }

    public function exportExcel(Request $request)
    {
        $firm = VatFirm::find(session('vat_firm_id'));
        if (!$firm) return redirect()->route('vat-system.firm.select', ['next' => 'sales']);
        $customer = $request->filled('customer_id') ? VatCustomer::find($request->integer('customer_id')) : null;
        $search = trim((string) $request->query('search'));
        $month = preg_match('/^\d{4}-\d{2}$/', (string) $request->query('month')) ? $request->query('month') : '';
        $bsMonth = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', (string) $request->query('bs_month')) ? $request->query('bs_month') : '';
        $bsMonthRange = $this->bsMonthRange($bsMonth);
        $bsFrom = preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', (string) $request->query('bs_from')) ? $request->query('bs_from') : '';
        $bsTo = preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', (string) $request->query('bs_to')) ? $request->query('bs_to') : '';
        $bsDateRange = $this->bsDateRange($bsFrom, $bsTo);
        $bills = VatSystemBill::where('firm_id', $firm->id)
            ->when($customer, fn($query) => $query->where('customer_id', $customer->id))
            ->with(['customer', 'extraCustomer', 'firm', 'items'])
            ->when($search !== '', fn($q) => $q->where(function ($q) use ($search) {
                $q->where('bill_no', 'like', "%{$search}%")
                    ->orWhere('bill_date', 'like', "%{$search}%")
                    ->orWhere('seller_name', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('pan_no', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"));
            }))
            ->when($month, fn($q) => $q->whereBetween('bill_date', [Carbon::createFromFormat('Y-m', $month)->startOfMonth()->toDateString(), Carbon::createFromFormat('Y-m', $month)->endOfMonth()->toDateString()]))
            ->when($bsMonthRange, fn($q) => $q->whereBetween('bill_date', $bsMonthRange))
            ->when($bsDateRange, fn($q) => $q->whereBetween('bill_date', $bsDateRange))
            ->latest('bill_date')->latest('id')->get();
        $html = view('vat-system.bills.excel', compact('bills', 'firm', 'customer', 'search', 'month', 'bsMonth'))->render();
        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="vat-sales-'.$firm->id.'.xls"',
        ]);
    }

    public function exportPanSummaryExcel(Request $request)
    {
        $firm = VatFirm::find(session('vat_firm_id'));
        if (!$firm) return redirect()->route('vat-system.firm.select', ['next' => 'sales']);
        $search = trim((string)$request->query('search'));
        $customer = $request->filled('customer_id') ? VatCustomer::find($request->integer('customer_id')) : null;
        $bsMonth = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', (string)$request->query('bs_month')) ? $request->query('bs_month') : '';
        $bsMonthRange = $this->bsMonthRange($bsMonth);
        $bsFrom = preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', (string)$request->query('bs_from')) ? $request->query('bs_from') : '';
        $bsTo = preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', (string)$request->query('bs_to')) ? $request->query('bs_to') : '';
        $range = $this->bsDateRange($bsFrom, $bsTo);
        $bills = VatSystemBill::where('firm_id',$firm->id)->when($customer,fn($q)=>$q->where('customer_id',$customer->id))->with(['customer','extraCustomer','items'])
            ->when($search !== '', fn($q)=>$q->where(function($q)use($search){$q->where('bill_no','like',"%{$search}%")->orWhereHas('customer',fn($q)=>$q->where('name','like',"%{$search}%")->orWhere('pan_no','like',"%{$search}%"))->orWhereHas('extraCustomer',fn($q)=>$q->where('name','like',"%{$search}%"));}))
            ->when($bsMonthRange,fn($q)=>$q->whereBetween('bill_date',$bsMonthRange))->when($range,fn($q)=>$q->whereBetween('bill_date',$range))->orderBy('bill_date')->orderBy('id')->get();
        $parties=[];
        foreach($bills as $bill){$party=$bill->customer?:$bill->extraCustomer;$pan=trim((string)($bill->customer?->pan_no??''));$norm=strtoupper(preg_replace('/\s+/','',$pan));$key=$norm!==''?'pan:'.$norm:'party:'.($bill->customer_id?:'extra:'.($bill->extra_customer_id?:$bill->id));$tax=$bill->items->where('is_taxable',true)->sum(fn($i)=>(float)$i->quantity*(float)$i->rate);$all=$bill->items->sum(fn($i)=>(float)$i->quantity*(float)$i->rate);$vat=round($tax*.13,2);if(!isset($parties[$key]))$parties[$key]=['names'=>[],'pan'=>$pan,'bills'=>0,'taxable'=>0,'vat'=>0,'grand'=>0];$parties[$key]['names'][trim((string)($party?->name??$bill->seller_name))]=true;$parties[$key]['bills']++;$parties[$key]['taxable']+=$tax;$parties[$key]['vat']+=$vat;$parties[$key]['grand']+=$all-(float)$bill->discount+$vat;}
        $parties=collect($parties)->map(function($p){$p['name']=implode(' / ',array_keys($p['names']));$p['taxable']=round($p['taxable'],2);$p['vat']=round($p['vat'],2);$p['grand']=round($p['grand'],2);return $p;})->sortBy(fn($p)=>strtolower($p['name']))->values();
        $above=$parties->filter(fn($p)=>trim($p['pan'])!==''&&$p['taxable']>=100000)->values();$others=$parties->reject(fn($p)=>trim($p['pan'])!==''&&$p['taxable']>=100000)->values();
        $html=view('vat-system.bills.pan-summary-excel',compact('firm','above','others','bsMonth','bsFrom','bsTo'))->render();
        return response($html,200,['Content-Type'=>'application/vnd.ms-excel; charset=UTF-8','Content-Disposition'=>'attachment; filename="vat-sales-pan-summary-for-final-'.$firm->id.'.xls"']);
    }

    public function exportSalesSummaryExcel(Request $request)
    {
        $firm = VatFirm::find(session('vat_firm_id'));
        if (!$firm) return redirect()->route('vat-system.firm.select', ['next' => 'sales']);

        $customer = $request->filled('customer_id') ? VatCustomer::find($request->integer('customer_id')) : null;
        $search = trim((string) $request->query('search'));
        $month = preg_match('/^\d{4}-\d{2}$/', (string) $request->query('month')) ? $request->query('month') : '';
        $bsMonth = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', (string) $request->query('bs_month')) ? $request->query('bs_month') : '';
        $bsMonthRange = $this->bsMonthRange($bsMonth);
        $bsFrom = preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', (string) $request->query('bs_from')) ? $request->query('bs_from') : '';
        $bsTo = preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', (string) $request->query('bs_to')) ? $request->query('bs_to') : '';
        $bsDateRange = $this->bsDateRange($bsFrom, $bsTo);
        $fiscalYear = preg_match('/^\d{4}\/\d{2}$/', (string) $request->query('fiscal_year')) ? $request->query('fiscal_year') : '';
        $fiscalDateRange = $fiscalYear !== '' ? $this->fiscalDates($fiscalYear) : null;

        $bills = VatSystemBill::where('firm_id', $firm->id)
            ->when($customer, fn ($q) => $q->where('customer_id', $customer->id))
            ->with(['customer', 'extraCustomer', 'items'])
            ->when($search !== '', fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('bill_no', 'like', "%{$search}%")
                    ->orWhere('bill_date', 'like', "%{$search}%")
                    ->orWhere('seller_name', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('pan_no', 'like', "%{$search}%"))
                    ->orWhereHas('extraCustomer', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            }))
            ->when($fiscalDateRange, fn ($q) => $q->whereBetween('bill_date', $fiscalDateRange))
            ->when(!$fiscalDateRange && $month, fn ($q) => $q->whereBetween('bill_date', [Carbon::createFromFormat('Y-m', $month)->startOfMonth()->toDateString(), Carbon::createFromFormat('Y-m', $month)->endOfMonth()->toDateString()]))
            ->when(!$fiscalDateRange && !$month && $bsMonthRange, fn ($q) => $q->whereBetween('bill_date', $bsMonthRange))
            ->when(!$fiscalDateRange && !$month && !$bsMonthRange && $bsDateRange, fn ($q) => $q->whereBetween('bill_date', $bsDateRange))
            ->orderBy('bill_date')->orderBy('id')->get();

        $parties = [];
        foreach ($bills as $bill) {
            $party = $bill->customer ?: $bill->extraCustomer;
            $pan = trim((string) ($bill->customer?->pan_no ?: ''));
            $normalizedPan = strtoupper((string) preg_replace('/\s+/', '', $pan));
            $key = $normalizedPan !== '' ? 'pan:'.$normalizedPan : 'party:'.($bill->customer_id ?: 'extra:'.($bill->extra_customer_id ?: 'bill:'.$bill->id));
            $taxable = (float) $bill->items->where('is_taxable', true)->sum(fn ($item) => (float) $item->quantity * (float) $item->rate);
            $subtotal = (float) $bill->items->sum(fn ($item) => (float) $item->quantity * (float) $item->rate);
            $vat = round($taxable * .13, 2);
            if (!isset($parties[$key])) $parties[$key] = ['names' => [], 'pan' => $pan, 'bills' => 0, 'taxable' => 0.0, 'vat' => 0.0, 'grand' => 0.0];
            $name = trim((string) ($party?->name ?: $bill->seller_name)) ?: 'Unknown Party';
            $parties[$key]['names'][$name] = true;
            $parties[$key]['bills']++;
            $parties[$key]['taxable'] += $taxable;
            $parties[$key]['vat'] += $vat;
            $parties[$key]['grand'] += $subtotal - (float) $bill->discount + $vat;
        }
        $parties = collect($parties)->map(function (array $party) {
            $party['name'] = implode(' / ', array_keys($party['names']));
            $party['taxable'] = round($party['taxable'], 2);
            $party['vat'] = round($party['vat'], 2);
            $party['grand'] = round($party['grand'], 2);
            return $party;
        })->sortBy(fn ($party) => strtolower($party['name']))->values();

        $above = $parties->filter(fn ($party) => $party['pan'] !== '' && $party['taxable'] >= 100000)->values();
        $others = $parties->reject(fn ($party) => $party['pan'] !== '' && $party['taxable'] >= 100000)->values();
        $html = view('vat-system.bills.pan-summary-excel', compact('firm', 'above', 'others', 'bsMonth', 'bsFrom', 'bsTo', 'fiscalYear'))->render();
        return response($html, 200, ['Content-Type' => 'application/vnd.ms-excel; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="vat-sales-summary-'.$firm->id.'.xls"']);
    }

    public function edit(VatSystemBill $bill)
    {
        abort_unless((int) $bill->firm_id === (int) session('vat_firm_id'), 404);
        $bill->load('items');

        return view('vat-system.create', [
            'customers' => VatCustomer::orderBy('name')->get(),
            'extraCustomer' => $bill->extraCustomer,
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
        $purchased = CompanyBillItem::query()->whereHas('companyBill', fn ($query) => $query->where('firm_id', session('vat_firm_id')))->select('item_name', 'hs_code', 'rate', 'unit')->get()->map(fn ($item) => (object) ['itemsname' => $item->item_name, 'mrp' => $item->rate, 'sale_price' => null, 'purchase_price' => $item->rate, 'unit' => $item->unit, 'hs_code' => $item->hs_code, 'stock_quantity' => null]);
        $stock = VatStock::query()->where('firm_id', session('vat_firm_id'))->select('item_name', 'sale_rate', 'purchase_rate', 'unit', 'quantity', 'hs_code')->get()->map(fn ($item) => (object) ['itemsname' => $item->item_name, 'mrp' => $item->sale_rate, 'sale_price' => $item->sale_rate, 'purchase_price' => $item->purchase_rate, 'unit' => $item->unit, 'hs_code' => $item->hs_code, 'stock_quantity' => (float)$item->quantity]);

        return $stock->concat($purchased)->unique(fn ($item) => strtolower(trim($item->itemsname).'|'.strtolower(trim($item->unit))))->sortBy('itemsname')->values();
    }

    private function catalogJson($catalogItems): string
    {
        return $catalogItems->map(fn ($item) => ['name'=>$item->itemsname,'price'=>$item->mrp,'sale_price'=>$item->sale_price ?? $item->mrp,'purchase_price'=>$item->purchase_price ?? null,'unit'=>$item->unit,'hs_code'=>$item->hs_code ?? null,'stock_quantity'=>$item->stock_quantity])->values()->toJson();
    }

    public function store(Request $request)
    {
        $firm = $this->firmOrRedirect('sales');
        $request->merge(['firm_id' => $firm->id]);
        $this->normalizeBillDate($request);
        $data = $request->validate([
            'customer_id' => ['nullable', 'required_without:extra_customer_id', 'exists:vat_customers,id'],
            'extra_customer_id' => ['nullable', 'required_without:customer_id', 'exists:vat_extra_customers,id'],
            'firm_id' => ['required', Rule::exists('vat_firms', 'id')->where(fn ($query) => $query->where('is_active', true))],
            'seller_name' => ['required', 'string', 'max:150'],
            'seller_vat_no' => ['nullable', 'string', 'max:50'],
            'seller_pan_no' => ['nullable', 'string', 'max:50'],
            'seller_phone' => ['nullable', 'string', 'max:30'],
            'seller_address' => ['nullable', 'string', 'max:255'],
            'seller_email' => ['nullable', 'email', 'max:150'],
            'bill_no' => ['required', 'string', 'max:50', Rule::unique('vat_system_bills', 'bill_no')->where(fn($query) => $query->where('firm_id', session('vat_firm_id')))],
            'bill_date' => ['required', 'date'],
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
        $bill->load(['customer', 'extraCustomer', 'firm', 'items']);
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
            'customer_id' => ['nullable', 'required_without:extra_customer_id', 'exists:vat_customers,id'], 'extra_customer_id' => ['nullable', 'required_without:customer_id', 'exists:vat_extra_customers,id'], 'firm_id' => ['required', Rule::exists('vat_firms', 'id')->where(fn ($query) => $query->where('is_active', true))],
            'seller_name' => ['required', 'string', 'max:150'], 'seller_vat_no' => ['nullable', 'string', 'max:50'],
            'seller_pan_no' => ['nullable', 'string', 'max:50'], 'seller_phone' => ['nullable', 'string', 'max:60'], 'seller_address' => ['nullable', 'string', 'max:255'], 'seller_email' => ['nullable', 'email', 'max:150'],
            'bill_no' => array_merge(['required', 'string', 'max:50'], $billNoRules),
            'bill_date' => ['required', 'date'],
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

    private function bsMonthRange(string $bsMonth): ?array
    {
        if ($bsMonth === '') return null;
        [$year, $month] = array_map('intval', explode('-', $bsMonth));
        try {
            $start = Carbon::parse(NepaliDate::bsToAdString($year, $month, 1));
            [$nextYear, $nextMonth] = $month === 12 ? [$year + 1, 1] : [$year, $month + 1];
            $end = Carbon::parse(NepaliDate::bsToAdString($nextYear, $nextMonth, 1))->subDay();
            return [$start->toDateString(), $end->toDateString()];
        } catch (\Throwable $exception) {
            return null;
        }
    }

    private function fiscalDates(string $fiscalYear): array
    {
        $startYear = (int) explode('/', $fiscalYear)[0];
        $from = NepaliDate::bsToAdString($startYear, 4, 1);
        $to = Carbon::parse(NepaliDate::bsToAdString($startYear + 1, 4, 1))->subDay()->toDateString();
        return [$from, $to];
    }

    private function bsDateRange(string $from, string $to): ?array
    {
        if ($from === '' || $to === '') return null;
        try {
            [$fy, $fm, $fd] = array_map('intval', explode('-', $from));
            [$ty, $tm, $td] = array_map('intval', explode('-', $to));
            $start = Carbon::parse(NepaliDate::bsToAdString($fy, $fm, $fd));
            $end = Carbon::parse(NepaliDate::bsToAdString($ty, $tm, $td));
            return $start->lte($end) ? [$start->toDateString(), $end->toDateString()] : null;
        } catch (\Throwable $exception) {
            return null;
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
