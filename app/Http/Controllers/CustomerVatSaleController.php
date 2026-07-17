<?php

namespace App\Http\Controllers;

use App\Models\customerinfo;
use App\Models\CustomerVatSale;
use App\Models\CustomerVatSaleItem;
use App\Models\Myfirm;
use App\Models\SupplierVatBill;
use App\Models\VatStockItem;
use App\Support\NepaliDate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CustomerVatSaleController extends Controller
{
    private const VAT_STOCK_FIRMS = ['Durga', 'malika'];

    private const FIRM_VAT_NUMBERS = [
        'Durga' => '601064191',
        'malika' => '302761801',
    ];

    private const SUPPLIER_FIRM_TYPES = [
        'Durga' => 'Durga And Dinesh Traders',
        'malika' => 'Malika & Nav Durga Traders',
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $firmId = $request->integer('myfirm_id') ?: null;

        $sales = CustomerVatSale::query()
            ->with(['firm', 'customer'])
            ->when($firmId, fn ($query) => $query->where('myfirm_id', $firmId))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('bill_no', 'like', '%'.$search.'%')
                        ->orWhere('customer_name', 'like', '%'.$search.'%')
                        ->orWhereHas('customer', fn ($customerQuery) => $customerQuery->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->latest('bill_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $firms = $this->vatFirms()->get();

        return view('customer-vat-sales.index', compact('sales', 'firms', 'firmId', 'search'));
    }

    public function create()
    {
        return view('customer-vat-sales.form', [
            'sale' => null,
            'firms' => $this->vatFirms()->get(),
            'selectedCustomer' => old('customer_id') ? customerinfo::find(old('customer_id')) : null,
        ]);
    }

    public function stock(Request $request)
    {
        $firms = $this->vatFirms()->get();
        $firmId = $request->integer('myfirm_id') ?: $firms->first()?->id;
        $firm = $firms->firstWhere('id', $firmId);

        if (! $firm) {
            return redirect()->route('customer-vat-sales.stock');
        }

        $search = trim((string) $request->query('search'));
        $status = $request->query('status');
        if (! in_array($status, [null, '', 'available', 'low', 'out'], true)) {
            $status = null;
        }

        $stockQuery = VatStockItem::query()
            ->where('myfirm_id', $firm->id)
            ->when($search !== '', fn ($query) => $query->where(function ($subQuery) use ($search) {
                $subQuery->where('item_name', 'like', '%'.$search.'%')
                    ->orWhere('id', 'like', '%'.$search.'%');
            }));

        $summary = (clone $stockQuery)
            ->toBase()
            ->selectRaw('COUNT(*) as item_count')
            ->selectRaw('COALESCE(SUM(quantity), 0) as stock_quantity')
            ->selectRaw('SUM(CASE WHEN quantity <= 0 THEN 1 ELSE 0 END) as out_count')
            ->selectRaw('SUM(CASE WHEN quantity > 0 AND quantity <= warning_quantity THEN 1 ELSE 0 END) as low_count')
            ->first();

        $vatSales = DB::table('customer_vat_sale_items as sale_items')
            ->join('customer_vat_sales as sales', 'sales.id', '=', 'sale_items.customer_vat_sale_id')
            ->where('sales.myfirm_id', $firm->id)
            ->whereNotNull('sale_items.vat_stock_item_id')
            ->groupBy('sale_items.vat_stock_item_id')
            ->select('sale_items.vat_stock_item_id')
            ->selectRaw('SUM(sale_items.quantity) as vat_sold_quantity')
            ->selectRaw('SUM(sale_items.amount) as vat_sales_amount')
            ->selectRaw('MAX(sales.bill_date) as last_vat_sale_date');

        $stockItems = $stockQuery
            ->leftJoinSub($vatSales, 'vat_sales', fn ($join) => $join->on('vat_stock_items.id', '=', 'vat_sales.vat_stock_item_id'))
            ->when($status === 'available', fn ($query) => $query->where('vat_stock_items.quantity', '>', 0))
            ->when($status === 'low', fn ($query) => $query->where('vat_stock_items.quantity', '>', 0)->whereColumn('vat_stock_items.quantity', '<=', 'vat_stock_items.warning_quantity'))
            ->when($status === 'out', fn ($query) => $query->where('vat_stock_items.quantity', '<=', 0))
            ->orderBy('vat_stock_items.item_name')
            ->select('vat_stock_items.*')
            ->selectRaw('COALESCE(vat_sales.vat_sold_quantity, 0) as vat_sold_quantity')
            ->selectRaw('COALESCE(vat_sales.vat_sales_amount, 0) as vat_sales_amount')
            ->addSelect('vat_sales.last_vat_sale_date')
            ->paginate(25)
            ->withQueryString();

        $recentMovements = CustomerVatSaleItem::query()
            ->with(['sale.customer'])
            ->whereHas('sale', fn ($query) => $query->where('myfirm_id', $firm->id))
            ->latest('id')
            ->limit(12)
            ->get();

        return view('customer-vat-sales.stock', compact(
            'firms',
            'firm',
            'firmId',
            'search',
            'status',
            'summary',
            'stockItems',
            'recentMovements'
        ));
    }

    public function monthlyBook(Request $request)
    {
        $currentBs = array_map('intval', explode('-', NepaliDate::adToBsString(now()->toDateString(), 'en')));
        $request->validate([
            'myfirm_id' => ['nullable', 'integer', $this->vatFirmExistsRule()],
            'bs_year' => ['nullable', 'integer', 'between:2000,2089'],
            'bs_month' => ['nullable', 'integer', 'between:1,12'],
        ]);

        $firms = $this->vatFirms()->get();
        $firmId = $request->integer('myfirm_id') ?: $firms->first()?->id;
        $firm = $firms->firstWhere('id', $firmId);
        abort_unless($firm, 404);

        $bsYear = $request->integer('bs_year') ?: $currentBs[0];
        $bsMonth = $request->integer('bs_month') ?: $currentBs[1];
        $yearStart = NepaliDate::bsToAdString($bsYear, 1, 1);
        $yearEnd = Carbon::parse(NepaliDate::bsToAdString($bsYear + 1, 1, 1))->subDay()->toDateString();
        $monthStart = NepaliDate::bsToAdString($bsYear, $bsMonth, 1);
        $nextMonthStart = $bsMonth === 12
            ? NepaliDate::bsToAdString($bsYear + 1, 1, 1)
            : NepaliDate::bsToAdString($bsYear, $bsMonth + 1, 1);
        $monthEnd = Carbon::parse($nextMonthStart)->subDay()->toDateString();
        $supplierFirmType = self::SUPPLIER_FIRM_TYPES[$firm->nick_name];

        $purchaseYearRows = SupplierVatBill::query()
            ->where('firm_type', $supplierFirmType)
            ->whereBetween('bill_date', [$yearStart, $yearEnd])
            ->get(['bill_date', 'taxable_amount', 'vat_amount', 'total_amount']);
        $salesYearRows = CustomerVatSale::query()
            ->where('myfirm_id', $firm->id)
            ->whereBetween('bill_date', [$yearStart, $yearEnd])
            ->get(['bill_date', 'taxable_amount', 'vat_amount', 'total_amount']);

        $monthlySummary = collect(range(1, 12))->map(function (int $month) use ($bsYear, $purchaseYearRows, $salesYearRows) {
            $purchases = $purchaseYearRows->filter(fn ($bill) => (int) explode('-', NepaliDate::adToBsString($bill->bill_date, 'en'))[1] === $month);
            $sales = $salesYearRows->filter(fn ($bill) => (int) explode('-', NepaliDate::adToBsString($bill->bill_date, 'en'))[1] === $month);

            return [
                'month' => $month,
                'month_name' => NepaliDate::formatBS($bsYear, $month, 1, 'MMMM', 'en'),
                'purchase_count' => $purchases->count(),
                'purchase_taxable' => (float) $purchases->sum('taxable_amount'),
                'purchase_vat' => (float) $purchases->sum('vat_amount'),
                'purchase_total' => (float) $purchases->sum('total_amount'),
                'sales_count' => $sales->count(),
                'sales_taxable' => (float) $sales->sum('taxable_amount'),
                'sales_vat' => (float) $sales->sum('vat_amount'),
                'sales_total' => (float) $sales->sum('total_amount'),
                'vat_difference' => (float) $sales->sum('vat_amount') - (float) $purchases->sum('vat_amount'),
            ];
        });

        $purchaseBills = SupplierVatBill::query()
            ->with('company')
            ->withCount('items')
            ->where('firm_type', $supplierFirmType)
            ->whereBetween('bill_date', [$monthStart, $monthEnd])
            ->latest('bill_date')
            ->latest('id')
            ->paginate(20, ['*'], 'purchase_page')
            ->withQueryString();
        $salesBills = CustomerVatSale::query()
            ->with('customer')
            ->withCount('items')
            ->where('myfirm_id', $firm->id)
            ->whereBetween('bill_date', [$monthStart, $monthEnd])
            ->latest('bill_date')
            ->latest('id')
            ->paginate(20, ['*'], 'sales_page')
            ->withQueryString();

        return view('customer-vat-sales.monthly-book', compact(
            'firms',
            'firm',
            'firmId',
            'bsYear',
            'bsMonth',
            'monthStart',
            'monthEnd',
            'monthlySummary',
            'purchaseBills',
            'salesBills'
        ));
    }

    public function stockCreate()
    {
        return view('customer-vat-sales.stock-form', [
            'stockItem' => null,
            'firms' => $this->vatFirms()->get(),
        ]);
    }

    public function stockStore(Request $request)
    {
        $validated = $this->validateStockItem($request);
        VatStockItem::create($validated + [
            'added_by' => session('user_email') ?: auth()->user()?->email,
        ]);

        return redirect()->route('customer-vat-sales.stock', ['myfirm_id' => $validated['myfirm_id']])
            ->with('success', 'VAT stock item added successfully.');
    }

    public function stockEdit(VatStockItem $vatStockItem)
    {
        abort_unless(in_array($vatStockItem->firm?->nick_name, self::VAT_STOCK_FIRMS, true), 404);

        return view('customer-vat-sales.stock-form', [
            'stockItem' => $vatStockItem,
            'firms' => $this->vatFirms()->get(),
        ]);
    }

    public function stockUpdate(Request $request, VatStockItem $vatStockItem)
    {
        abort_unless(in_array($vatStockItem->firm?->nick_name, self::VAT_STOCK_FIRMS, true), 404);
        $validated = $this->validateStockItem($request, $vatStockItem);
        $vatStockItem->update($validated);

        return redirect()->route('customer-vat-sales.stock', ['myfirm_id' => $validated['myfirm_id']])
            ->with('success', 'VAT stock item updated successfully.');
    }

    public function stockDestroy(VatStockItem $vatStockItem)
    {
        abort_unless(in_array($vatStockItem->firm?->nick_name, self::VAT_STOCK_FIRMS, true), 404);

        if ($vatStockItem->saleLines()->exists()) {
            return back()->with('error', 'This VAT stock item has VAT sale history and cannot be deleted. Set its quantity to zero instead.');
        }

        $firmId = $vatStockItem->myfirm_id;
        $vatStockItem->delete();

        return redirect()->route('customer-vat-sales.stock', ['myfirm_id' => $firmId])
            ->with('success', 'VAT stock item deleted successfully.');
    }

    public function stockItems(Request $request)
    {
        $validated = $request->validate([
            'myfirm_id' => ['required', 'integer', $this->vatFirmExistsRule()],
            'search' => ['required', 'string', 'max:255'],
        ]);

        $search = trim($validated['search']);

        $items = VatStockItem::query()
            ->where('myfirm_id', $validated['myfirm_id'])
            ->where('quantity', '>', 0)
            ->where(function ($query) use ($search) {
                $query->where('item_name', 'like', '%'.$search.'%')
                    ->orWhere('id', 'like', '%'.$search.'%');
            })
            ->orderByRaw('CASE WHEN item_name LIKE ? THEN 0 ELSE 1 END', [$search.'%'])
            ->orderBy('item_name')
            ->limit(20)
            ->get(['id', 'item_name', 'quantity', 'unit', 'sale_rate']);

        return response()->json($items->map(fn ($stockItem) => [
            'id' => $stockItem->id,
            'name' => $stockItem->item_name,
            'available_quantity' => (float) $stockItem->quantity,
            'unit' => $stockItem->unit ?: 'pcs',
            'suggested_rate' => (float) $stockItem->sale_rate,
        ]));
    }

    public function store(Request $request)
    {
        $validated = $this->validateSale($request);

        $sale = DB::transaction(function () use ($validated) {
            $lines = $this->takeStock($validated);
            $sale = CustomerVatSale::create($this->saleValues($validated, $lines) + [
                'added_by' => session('user_email') ?: auth()->user()?->email,
            ]);
            $sale->items()->createMany($lines->all());

            return $sale;
        });

        return redirect()->route('customer-vat-sales.show', $sale)
            ->with('success', 'Customer VAT sale created and stock updated successfully.');
    }

    public function show(CustomerVatSale $customerVatSale)
    {
        $customerVatSale->load(['firm', 'customer', 'items.vatStockItem']);

        return view('customer-vat-sales.show', [
            'sale' => $customerVatSale,
            'firmVatNo' => self::FIRM_VAT_NUMBERS[$customerVatSale->firm->nick_name] ?? '-',
        ]);
    }

    public function edit(CustomerVatSale $customerVatSale)
    {
        $customerVatSale->load(['firm', 'customer', 'items.vatStockItem']);

        return view('customer-vat-sales.form', [
            'sale' => $customerVatSale,
            'firms' => $this->vatFirms()->get(),
            'selectedCustomer' => old('customer_id') ? customerinfo::find(old('customer_id')) : $customerVatSale->customer,
        ]);
    }

    public function update(Request $request, CustomerVatSale $customerVatSale)
    {
        $validated = $this->validateSale($request, $customerVatSale);

        DB::transaction(function () use ($validated, $customerVatSale) {
            $customerVatSale->load('items');
            $this->restoreStock($customerVatSale->items);
            $lines = $this->takeStock($validated);

            $customerVatSale->update($this->saleValues($validated, $lines));
            $customerVatSale->items()->delete();
            $customerVatSale->items()->createMany($lines->all());
        });

        return redirect()->route('customer-vat-sales.show', $customerVatSale)
            ->with('success', 'Customer VAT sale and stock updated successfully.');
    }

    public function destroy(CustomerVatSale $customerVatSale)
    {
        DB::transaction(function () use ($customerVatSale) {
            $customerVatSale->load('items');
            $this->restoreStock($customerVatSale->items);
            $customerVatSale->delete();
        });

        return redirect()->route('customer-vat-sales.index')
            ->with('success', 'Customer VAT sale deleted and its stock restored.');
    }

    private function validateSale(Request $request, ?CustomerVatSale $sale = null): array
    {
        $billRule = Rule::unique('customer_vat_sales', 'bill_no')
            ->where(fn ($query) => $query->where('myfirm_id', $request->input('myfirm_id')));

        if ($sale) {
            $billRule->ignore($sale->id);
        }

        return $request->validate([
            'myfirm_id' => ['required', 'integer', $this->vatFirmExistsRule()],
            'customer_id' => ['nullable', 'integer', 'exists:customerinfos,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_vat_no' => ['nullable', 'string', 'max:100'],
            'bill_no' => ['required', 'string', 'max:100', $billRule],
            'bill_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.vat_stock_item_id' => ['required', 'integer', 'distinct', 'exists:vat_stock_items,id'],
            'items.*.item_name' => ['nullable', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0', 'max:999999999'],
            'items.*.unit' => ['required', 'string', 'max:30'],
            'items.*.rate' => ['required', 'numeric', 'min:0', 'max:999999999999'],
        ], [
            'bill_no.unique' => 'This VAT bill number already exists for the selected firm.',
            'items.*.vat_stock_item_id.distinct' => 'The same VAT stock item cannot be selected twice.',
        ]);
    }

    private function takeStock(array $validated): Collection
    {
        $requestedItems = collect($validated['items']);
        $stockItems = VatStockItem::query()
            ->whereIn('id', $requestedItems->pluck('vat_stock_item_id'))
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        return $requestedItems->map(function (array $line, int $index) use ($validated, $stockItems) {
            $stockItem = $stockItems->get((int) $line['vat_stock_item_id']);
            $quantity = round((float) $line['quantity'], 3);
            $rate = round((float) $line['rate'], 2);

            if (! $stockItem || (int) $stockItem->myfirm_id !== (int) $validated['myfirm_id']) {
                throw ValidationException::withMessages([
                    "items.$index.vat_stock_item_id" => 'The selected VAT stock item does not belong to the chosen firm.',
                ]);
            }

            if ((float) $stockItem->quantity < $quantity) {
                throw ValidationException::withMessages([
                    "items.$index.quantity" => $stockItem->item_name.' has only '.number_format((float) $stockItem->quantity, 3).' in VAT stock.',
                ]);
            }

            $stockItem->quantity = round((float) $stockItem->quantity - $quantity, 3);
            $stockItem->save();

            return [
                'vat_stock_item_id' => $stockItem->id,
                'item_name' => $stockItem->item_name,
                'quantity' => $quantity,
                'unit' => trim($line['unit']),
                'rate' => $rate,
                'amount' => round($quantity * $rate, 2),
            ];
        });
    }

    private function restoreStock(Collection $saleItems): void
    {
        $quantities = $saleItems->whereNotNull('vat_stock_item_id')->groupBy('vat_stock_item_id')->map->sum('quantity');
        $stockItems = VatStockItem::query()
            ->whereIn('id', $quantities->keys())
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($quantities as $itemId => $quantity) {
            $stockItem = $stockItems->get((int) $itemId);
            if ($stockItem) {
                $stockItem->quantity = round((float) $stockItem->quantity + (float) $quantity, 3);
                $stockItem->save();
            }
        }
    }

    private function validateStockItem(Request $request, ?VatStockItem $stockItem = null): array
    {
        $nameRule = Rule::unique('vat_stock_items', 'item_name')
            ->where(fn ($query) => $query->where('myfirm_id', $request->input('myfirm_id')));

        if ($stockItem) {
            $nameRule->ignore($stockItem->id);
        }

        return $request->validate([
            'myfirm_id' => ['required', 'integer', $this->vatFirmExistsRule()],
            'item_name' => ['required', 'string', 'max:255', $nameRule],
            'unit' => ['required', 'string', 'max:30'],
            'quantity' => ['required', 'numeric', 'min:0', 'max:999999999999'],
            'cost_price' => ['required', 'numeric', 'min:0', 'max:999999999999'],
            'sale_rate' => ['required', 'numeric', 'min:0', 'max:999999999999'],
            'warning_quantity' => ['required', 'numeric', 'min:0', 'max:999999999999'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'item_name.unique' => 'This item already exists in the selected firm’s VAT stock.',
        ]);
    }

    private function saleValues(array $validated, Collection $lines): array
    {
        $taxable = round($lines->sum('amount'), 2);
        $vat = round($taxable * 0.13, 2);
        $customer = ! empty($validated['customer_id'])
            ? customerinfo::find($validated['customer_id'])
            : null;
        $customerName = trim($validated['customer_name']);
        $customerVatNo = trim((string) ($validated['customer_vat_no'] ?? ''));

        return [
            'myfirm_id' => $validated['myfirm_id'],
            'customer_id' => $validated['customer_id'] ?? null,
            'customer_name' => $customer?->name ?: $customerName,
            'customer_vat_no' => $customerVatNo !== '' ? $customerVatNo : null,
            'bill_no' => trim($validated['bill_no']),
            'bill_date' => $validated['bill_date'],
            'taxable_amount' => $taxable,
            'vat_rate' => 13,
            'vat_amount' => $vat,
            'total_amount' => round($taxable + $vat, 2),
            'notes' => $validated['notes'] ?? null,
        ];
    }

    private function vatFirms()
    {
        return Myfirm::query()
            ->whereIn('nick_name', self::VAT_STOCK_FIRMS)
            ->orderBy('firm_name');
    }

    private function vatFirmExistsRule()
    {
        return Rule::exists('myfirm', 'id')
            ->where(fn ($query) => $query->whereIn('nick_name', self::VAT_STOCK_FIRMS));
    }
}
