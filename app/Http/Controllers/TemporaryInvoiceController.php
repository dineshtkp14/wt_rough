<?php

namespace App\Http\Controllers;

use App\Models\TemporaryInvoice;
use App\Models\TemporaryInvoiceFixedItemSet;
use App\Models\pricelist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TemporaryInvoiceController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $breadcrumb = [
            'subtitle' => 'View',
            'title' => 'Temporary Invoices',
            'link' => 'Temporary Invoices',
        ];

        $query = TemporaryInvoice::query()->withCount('items')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', '%' . $search . '%')
                    ->orWhere('contact_number', 'like', '%' . $search . '%')
                    ->orWhere('customer_address', 'like', '%' . $search . '%')
                    ->orWhere('id', $search);
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('invoice_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('invoice_date', '<=', $request->to_date);
        }

        $temporaryInvoices = $query->paginate(100)->withQueryString();

        return view('temporaryinvoice.index', compact('temporaryInvoices', 'breadcrumb'));
    }

    public function liveSearch(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['html' => '', 'pagination' => ''], 401);
        }

        if (!$request->ajax() && !$request->expectsJson()) {
            return redirect()->route('temporaryinvoice.index', $request->query());
        }

        $query = TemporaryInvoice::query()->withCount('items')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', '%' . $search . '%')
                    ->orWhere('contact_number', 'like', '%' . $search . '%')
                    ->orWhere('customer_address', 'like', '%' . $search . '%')
                    ->orWhere('invoice_number', 'like', '%' . $search . '%')
                    ->orWhere('id', $search);
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('invoice_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('invoice_date', '<=', $request->to_date);
        }

        $temporaryInvoices = $query
            ->paginate(100)
            ->withPath(route('temporaryinvoice.index'))
            ->appends($request->query());

        return response()->json([
            'html' => view('temporaryinvoice._rows', compact('temporaryInvoices'))->render(),
            'pagination' => $temporaryInvoices->links()->render(),
        ]);
    }

    public function fixedItemSets(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([], 401);
        }

        $search = trim((string) $request->query('q', ''));

        $sets = TemporaryInvoiceFixedItemSet::query()
            ->with('items')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like', '%' . $search . '%')
                        ->orWhereHas('items', function ($itemQuery) use ($search) {
                            $itemQuery->where('item_name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('code')
            ->limit(20)
            ->get();

        return response()->json($sets->map(function ($set) {
            return $this->fixedItemSetPayload($set);
        })->values());
    }

    public function priceListSuggestions(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([], 401);
        }

        $search = trim((string) $request->query('q', ''));

        $items = pricelist::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('itemname', 'like', '%' . $search . '%')
                        ->orWhere('id', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('itemname')
            ->limit(20)
            ->get(['id', 'itemname', 'saleprice', 'wholesaleprice', 'note']);

        return response()->json($items->map(function ($item) {
            return [
                'id' => $item->id,
                'item_name' => $item->itemname,
                'sale_price' => (float) $item->saleprice,
                'wholesale_price' => (float) $item->wholesaleprice,
                'note' => $item->note,
            ];
        })->values());
    }

    public function storeFixedItemSet(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $validated = $this->validateFixedItemSet($request);

        $set = DB::transaction(function () use ($validated) {
            $set = TemporaryInvoiceFixedItemSet::create([
                'code' => strtolower($validated['code']),
                'name' => $validated['name'],
            ]);

            $this->replaceFixedItemSetItems($set, $validated['items']);

            return $set->load('items');
        });

        return response()->json($this->fixedItemSetPayload($set), 201);
    }

    public function updateFixedItemSet(Request $request, TemporaryInvoiceFixedItemSet $fixedItemSet)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $validated = $this->validateFixedItemSet($request, $fixedItemSet->id);

        $set = DB::transaction(function () use ($validated, $fixedItemSet) {
            $fixedItemSet->update([
                'code' => strtolower($validated['code']),
                'name' => $validated['name'],
            ]);

            $this->replaceFixedItemSetItems($fixedItemSet, $validated['items']);

            return $fixedItemSet->fresh('items');
        });

        return response()->json($this->fixedItemSetPayload($set));
    }

    public function destroyFixedItemSet(TemporaryInvoiceFixedItemSet $fixedItemSet)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $fixedItemSet->delete();

        return response()->json(['message' => 'Fixed item set deleted.']);
    }

    public function create()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $breadcrumb = [
            'subtitle' => '',
            'title' => 'Temporary Invoice',
            'link' => '',
        ];

        return view('temporaryinvoice.create', compact('breadcrumb'));
    }

    private function validateFixedItemSet(Request $request, $ignoreId = null): array
    {
        $codeRule = 'required|string|max:80|unique:temporary_invoice_fixed_item_sets,code';
        if ($ignoreId) {
            $codeRule .= ',' . $ignoreId;
        }

        return $request->validate([
            'code' => $codeRule,
            'name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.price' => 'required|numeric|min:0',
        ]);
    }

    private function replaceFixedItemSetItems(TemporaryInvoiceFixedItemSet $set, array $items): void
    {
        $set->items()->delete();

        foreach ($items as $index => $item) {
            $set->items()->create([
                'item_name' => $item['item_name'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'] ?? null,
                'price' => $item['price'],
                'sort_order' => $index + 1,
            ]);
        }
    }

    private function fixedItemSetPayload(TemporaryInvoiceFixedItemSet $set): array
    {
        return [
            'id' => $set->id,
            'code' => $set->code,
            'name' => $set->name,
            'items' => $set->items->map(function ($item) {
                return [
                    'item_name' => $item->item_name,
                    'quantity' => (float) $item->quantity,
                    'unit' => $item->unit,
                    'price' => (float) $item->price,
                ];
            })->values(),
        ];
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'customer_name' => 'required|string|max:255',
            'customer_address' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        $temporaryInvoice = DB::transaction(function () use ($validated) {
            $subtotal = collect($validated['items'])->sum(function ($item) {
                $rowTotal = (float) $item['quantity'] * (float) $item['price'];
                $discountPercent = (float) ($item['discount_percent'] ?? 0);
                return max(0, $rowTotal - ($rowTotal * $discountPercent / 100));
            });
            $discount = (float) ($validated['discount'] ?? 0);
            $total = max(0, $subtotal - $discount);
            $statement = DB::select("SHOW TABLE STATUS LIKE 'temporary_invoices'");
            $nextInvoiceId = $statement[0]->Auto_increment ?? (TemporaryInvoice::max('id') + 1);
            $invoiceNumber = 'temp-' . $nextInvoiceId;

            $temporaryInvoice = TemporaryInvoice::create([
                'invoice_date' => $validated['invoice_date'],
                'customer_name' => $validated['customer_name'],
                'customer_address' => $validated['customer_address'] ?? null,
                'contact_number' => $validated['contact_number'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
                'invoice_number' => $invoiceNumber,
                'added_by' => Auth::user()->name ?? session('user_email'),
            ]);

            foreach ($validated['items'] as $item) {
                $temporaryInvoice->items()->create([
                    'item_name' => $item['item_name'],
                    'unstocked_item' => $item['item_name'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? null,
                    'price' => $item['price'],
                    'subtotal' => max(0, ((float) $item['quantity'] * (float) $item['price']) * (1 - ((float) ($item['discount_percent'] ?? 0) / 100))),
                ]);
            }

            return $temporaryInvoice;
        });

        return redirect()
            ->route('temporaryinvoice.show', $temporaryInvoice)
            ->with('success', 'Temporary invoice created successfully.');
    }

    public function show(Request $request, TemporaryInvoice $temporaryinvoice)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $breadcrumb = [
            'subtitle' => 'View',
            'title' => 'Temporary Invoice Details',
            'link' => 'Temporary Invoice Details',
        ];

        $temporaryinvoice->load('items');

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'invoice_number' => $temporaryinvoice->invoice_number ?? $temporaryinvoice->id,
                'invoice_date' => $temporaryinvoice->invoice_date,
                'customer_name' => $temporaryinvoice->customer_name,
                'customer_address' => $temporaryinvoice->customer_address,
                'contact_number' => $temporaryinvoice->contact_number,
                'subtotal' => (float) $temporaryinvoice->subtotal,
                'discount' => (float) $temporaryinvoice->discount,
                'total' => (float) $temporaryinvoice->total,
                'notes' => $temporaryinvoice->notes,
                'print_url' => route('temporaryinvoice.print', $temporaryinvoice),
                'items' => $temporaryinvoice->items->map(function ($item) {
                    $gross = (float) $item->quantity * (float) $item->price;
                    $discountAmount = max(0, $gross - (float) $item->subtotal);
                    $discountPercent = $gross > 0
                        ? max(0, ($discountAmount / $gross) * 100)
                        : 0;

                    return [
                        'item_name' => $item->item_name,
                        'quantity' => $item->quantity,
                        'unit' => $item->unit,
                        'price' => (float) $item->price,
                        'discount_percent' => $discountPercent,
                        'discount_amount' => $discountAmount,
                        'subtotal' => (float) $item->subtotal,
                    ];
                })->values(),
            ]);
        }

        return view('temporaryinvoice.show', compact('temporaryinvoice', 'breadcrumb'));
    }

    public function printInvoice(TemporaryInvoice $temporaryinvoice)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $temporaryinvoice->load('items');

        return view('temporaryinvoice.print', compact('temporaryinvoice'));
    }

    public function destroy(TemporaryInvoice $temporaryinvoice)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $temporaryinvoice->delete();

        return redirect()
            ->route('temporaryinvoice.index')
            ->with('success', 'Temporary invoice deleted successfully.');
    }
}
