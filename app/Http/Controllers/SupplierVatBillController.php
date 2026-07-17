<?php

namespace App\Http\Controllers;

use App\Models\company;
use App\Models\Myfirm;
use App\Models\SupplierVatBill;
use App\Models\VatStockItem;
use App\Support\NepaliDate;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SupplierVatBillController extends Controller
{
    private const FIRMS = [
        'Malika & Nav Durga Traders',
        'Durga And Dinesh Traders',
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->only([
            'supplier_name',
            'company_id',
            'firm_type',
            'bill_no',
            'from_date_bs',
            'to_date_bs',
        ]), [
            'supplier_name' => ['nullable', 'string', 'max:255'],
            'company_id' => ['nullable', 'required_with:supplier_name', 'integer', 'exists:companies,id'],
            'firm_type' => ['nullable', Rule::in(self::FIRMS)],
            'bill_no' => ['nullable', 'string', 'max:100'],
            'from_date_bs' => ['nullable', 'regex:/^\d{4}[-\/.]\d{1,2}[-\/.]\d{1,2}$/'],
            'to_date_bs' => ['nullable', 'regex:/^\d{4}[-\/.]\d{1,2}[-\/.]\d{1,2}$/'],
        ], [
            'company_id.required_with' => 'Please select the supplier from the search results.',
            'from_date_bs.regex' => 'Enter the From date in B.S. YYYY-MM-DD format.',
            'to_date_bs.regex' => 'Enter the To date in B.S. YYYY-MM-DD format.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('supplier-vat-bills.index')
                ->withErrors($validator)
                ->withInput();
        }

        $filters = $validator->validated();

        $companyId = $filters['company_id'] ?? null;
        $firmType = $filters['firm_type'] ?? null;
        $billNo = trim((string) ($filters['bill_no'] ?? ''));
        $fromDateBs = trim((string) ($filters['from_date_bs'] ?? ''));
        $toDateBs = trim((string) ($filters['to_date_bs'] ?? ''));

        try {
            $fromDate = $fromDateBs !== '' ? $this->bsDateToAd($fromDateBs) : null;
        } catch (\InvalidArgumentException $exception) {
            return redirect()->route('supplier-vat-bills.index')
                ->withErrors(['from_date_bs' => 'The From date is invalid or outside the supported B.S. range.'])
                ->withInput();
        }

        try {
            $toDate = $toDateBs !== '' ? $this->bsDateToAd($toDateBs) : null;
        } catch (\InvalidArgumentException $exception) {
            return redirect()->route('supplier-vat-bills.index')
                ->withErrors(['to_date_bs' => 'The To date is invalid or outside the supported B.S. range.'])
                ->withInput();
        }

        if ($fromDate && $toDate && $toDate < $fromDate) {
            return redirect()->route('supplier-vat-bills.index')
                ->withErrors(['to_date_bs' => 'The To date must be the same as or later than the From date.'])
                ->withInput();
        }

        $selectedCompany = $companyId
            ? company::query()->find($companyId)
            : null;

        $bills = SupplierVatBill::query()
            ->with('company')
            ->when($firmType, fn ($query) => $query->where('firm_type', $firmType))
            ->when($companyId, fn ($query) => $query->where('company_id', $companyId))
            ->when($billNo !== '', fn ($query) => $query->where('bill_no', 'like', '%'.$billNo.'%'))
            ->when($fromDate, fn ($query) => $query->whereDate('bill_date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('bill_date', '<=', $toDate));

        $ledgerSummary = (clone $bills)
            ->toBase()
            ->selectRaw('COUNT(*) as bill_count')
            ->selectRaw('COALESCE(SUM(taxable_amount), 0) as taxable_total')
            ->selectRaw('COALESCE(SUM(vat_amount), 0) as vat_total')
            ->selectRaw('COALESCE(SUM(total_amount), 0) as grand_total')
            ->first();

        $bills = $bills
            ->latest('bill_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();
        $firms = self::FIRMS;

        return view('supplier-vat-bills.index', compact(
            'bills',
            'selectedCompany',
            'firmType',
            'firms',
            'billNo',
            'fromDate',
            'toDate',
            'fromDateBs',
            'toDateBs',
            'ledgerSummary'
        ));
    }

    public function create()
    {
        $selectedCompany = old('company_id')
            ? company::query()->find(old('company_id'), ['id', 'name', 'address', 'phoneno'])
            : null;

        return view('supplier-vat-bills.create', [
            'bill' => null,
            'firms' => self::FIRMS,
            'selectedCompany' => $selectedCompany,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateBill($request);

        $bill = DB::transaction(function () use ($validated) {
            $items = $this->normalizeItems($validated['items']);

            $bill = SupplierVatBill::create($this->billValues($validated, $items) + [
                'added_by' => session('user_email') ?: auth()->user()?->email,
            ]);

            $bill->items()->createMany($items->all());
            $this->syncVatStock(null, collect(), $validated['firm_type'], $items);

            return $bill;
        });

        return redirect()->route('supplier-vat-bills.show', $bill)
            ->with('success', 'Supplier VAT bill created successfully.');
    }

    public function show(SupplierVatBill $supplierVatBill)
    {
        $supplierVatBill->load(['company', 'items']);

        return view('supplier-vat-bills.show', ['bill' => $supplierVatBill]);
    }

    public function edit(SupplierVatBill $supplierVatBill)
    {
        $supplierVatBill->load(['company', 'items']);
        $selectedCompany = old('company_id')
            ? company::query()->find(old('company_id'), ['id', 'name', 'address', 'phoneno'])
            : $supplierVatBill->company;

        return view('supplier-vat-bills.create', [
            'bill' => $supplierVatBill,
            'firms' => self::FIRMS,
            'selectedCompany' => $selectedCompany,
        ]);
    }

    public function update(Request $request, SupplierVatBill $supplierVatBill)
    {
        $validated = $this->validateBill($request, $supplierVatBill);

        DB::transaction(function () use ($validated, $supplierVatBill) {
            $supplierVatBill->load('items');
            $oldFirmType = $supplierVatBill->firm_type;
            $oldItems = $supplierVatBill->items;
            $items = $this->normalizeItems($validated['items']);

            $supplierVatBill->update($this->billValues($validated, $items));
            $supplierVatBill->items()->delete();
            $supplierVatBill->items()->createMany($items->all());
            $this->syncVatStock($oldFirmType, $oldItems, $validated['firm_type'], $items);
        });

        return redirect()->route('supplier-vat-bills.show', $supplierVatBill)
            ->with('success', 'Supplier VAT bill updated successfully.');
    }

    public function destroy(SupplierVatBill $supplierVatBill)
    {
        $billNo = $supplierVatBill->bill_no;
        DB::transaction(function () use ($supplierVatBill) {
            $supplierVatBill->load('items');
            $this->syncVatStock($supplierVatBill->firm_type, $supplierVatBill->items, null, collect());
            $supplierVatBill->delete();
        });

        return redirect()->route('supplier-vat-bills.index')
            ->with('success', 'Supplier VAT bill #'.$billNo.' deleted successfully.');
    }

    private function validateBill(Request $request, ?SupplierVatBill $bill = null): array
    {
        $billNumberRule = Rule::unique('supplier_vat_bills', 'bill_no')
            ->where(fn ($query) => $query->where('company_id', $request->input('company_id')));

        if ($bill) {
            $billNumberRule->ignore($bill->id);
        }

        return $request->validate([
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'firm_type' => ['required', Rule::in(self::FIRMS)],
            'bill_no' => ['required', 'string', 'max:100', $billNumberRule],
            'bill_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0', 'max:999999999'],
            'items.*.unit' => ['required', 'string', 'max:30'],
            'items.*.rate' => ['required', 'numeric', 'min:0', 'max:999999999999'],
        ], [
            'bill_no.unique' => 'This bill number is already recorded for the selected supplier.',
            'items.required' => 'Please add at least one item.',
        ]);
    }

    private function normalizeItems(array $items)
    {
        return collect($items)->map(function (array $item) {
            $quantity = round((float) $item['quantity'], 3);
            $rate = round((float) $item['rate'], 2);

            return [
                'item_name' => trim($item['item_name']),
                'quantity' => $quantity,
                'unit' => trim($item['unit']),
                'rate' => $rate,
                'amount' => round($quantity * $rate, 2),
            ];
        });
    }

    private function billValues(array $validated, $items): array
    {
        $taxableAmount = round($items->sum('amount'), 2);
        $vatAmount = round($taxableAmount * 0.13, 2);

        return [
            'company_id' => $validated['company_id'],
            'firm_type' => $validated['firm_type'],
            'bill_no' => trim($validated['bill_no']),
            'bill_date' => $validated['bill_date'],
            'taxable_amount' => $taxableAmount,
            'vat_rate' => 13,
            'vat_amount' => $vatAmount,
            'total_amount' => round($taxableAmount + $vatAmount, 2),
            'notes' => $validated['notes'] ?? null,
        ];
    }

    private function syncVatStock(?string $oldFirmType, Collection $oldItems, ?string $newFirmType, Collection $newItems): void
    {
        $changes = collect();

        $addChanges = function (?string $firmType, Collection $items, float $direction) use ($changes): void {
            if (! $firmType) {
                return;
            }

            $firmId = $this->vatFirmId($firmType);
            foreach ($items as $line) {
                $name = trim((string) $line['item_name']);
                $key = $firmId.'|'.mb_strtolower($name);
                $current = $changes->get($key, [
                    'myfirm_id' => $firmId,
                    'item_name' => $name,
                    'delta' => 0,
                    'new_line' => null,
                ]);
                $current['delta'] += $direction * (float) $line['quantity'];
                if ($direction > 0) {
                    $current['new_line'] = $line;
                }
                $changes->put($key, $current);
            }
        };

        $addChanges($oldFirmType, $oldItems, -1);
        $addChanges($newFirmType, $newItems, 1);

        foreach ($changes as $change) {
            $stockItem = VatStockItem::query()
                ->where('myfirm_id', $change['myfirm_id'])
                ->where('item_name', $change['item_name'])
                ->lockForUpdate()
                ->first();

            if (! $stockItem && $change['delta'] <= 0) {
                throw ValidationException::withMessages([
                    'items' => $change['item_name'].' is not available in independent VAT stock, so this supplier bill cannot be changed.',
                ]);
            }

            if (! $stockItem) {
                $line = $change['new_line'];
                $stockItem = new VatStockItem([
                    'myfirm_id' => $change['myfirm_id'],
                    'item_name' => $change['item_name'],
                    'unit' => trim((string) $line['unit']),
                    'quantity' => 0,
                    'cost_price' => $line['rate'],
                    'sale_rate' => $line['rate'],
                    'warning_quantity' => 0,
                    'added_by' => session('user_email') ?: auth()->user()?->email,
                ]);
            }

            $newQuantity = round((float) $stockItem->quantity + (float) $change['delta'], 3);
            if ($newQuantity < 0) {
                throw ValidationException::withMessages([
                    'items' => $change['item_name'].' has already been sold from VAT stock. This supplier bill change would make VAT stock negative.',
                ]);
            }

            if ($change['new_line']) {
                $stockItem->unit = trim((string) $change['new_line']['unit']);
                $stockItem->cost_price = $change['new_line']['rate'];
            }
            $stockItem->quantity = $newQuantity;
            $stockItem->save();
        }
    }

    private function vatFirmId(string $firmType): int
    {
        $nickName = str_contains(mb_strtolower($firmType), 'malika') ? 'malika' : 'Durga';

        return (int) Myfirm::query()->where('nick_name', $nickName)->firstOrFail(['id'])->id;
    }

    private function bsDateToAd(string $date): string
    {
        preg_match('/^(\d{4})[-\/.](\d{1,2})[-\/.](\d{1,2})$/', $date, $parts);

        return NepaliDate::bsToAdString((int) $parts[1], (int) $parts[2], (int) $parts[3]);
    }
}
