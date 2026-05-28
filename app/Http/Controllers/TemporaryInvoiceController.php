<?php

namespace App\Http\Controllers;

use App\Models\TemporaryInvoice;
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
        ]);

        $temporaryInvoice = DB::transaction(function () use ($validated) {
            $subtotal = collect($validated['items'])->sum(function ($item) {
                return (float) $item['quantity'] * (float) $item['price'];
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
                    'subtotal' => (float) $item['quantity'] * (float) $item['price'],
                ]);
            }

            return $temporaryInvoice;
        });

        return redirect()
            ->route('temporaryinvoice.show', $temporaryInvoice)
            ->with('success', 'Temporary invoice created successfully.');
    }

    public function show(TemporaryInvoice $temporaryinvoice)
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
