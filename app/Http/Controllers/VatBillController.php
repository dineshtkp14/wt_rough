<?php

namespace App\Http\Controllers;

use App\Models\invoice;
use App\Models\VatBill;
use App\Models\customerledgerdetails;
use App\Services\CustomerLedgerBalance;
use App\Support\NepaliDate;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VatBillController extends Controller
{
    public const FIRMS = [
        'Malika & Nav Durga Traders',
        'Durga And Dinesh Traders',
    ];

    public const FIRM_VAT_NUMBERS = [
        'Malika & Nav Durga Traders' => '302761801',
        'Durga And Dinesh Traders' => '601064191',
    ];

    public const FIRM_CONTACT_NUMBERS = [
        'Malika & Nav Durga Traders' => '9812656284, 9860378262',
        'Durga And Dinesh Traders' => '9812656284, 9860378262',
    ];

    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $search = trim((string) $request->query('search'));

        $partyLedgers = VatBill::query()
            ->join('invoices', 'vat_bills.invoice_id', '=', 'invoices.id')
            ->join('customerinfos', 'invoices.customerid', '=', 'customerinfos.id')
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';
                $query->where(function ($searchQuery) use ($like) {
                    $searchQuery->where('customerinfos.name', 'like', $like)
                        ->orWhere('customerinfos.address', 'like', $like)
                        ->orWhere('customerinfos.vat_no', 'like', $like)
                        ->orWhere('customerinfos.phoneno', 'like', $like)
                        ->orWhere('vat_bills.firm_type', 'like', $like);
                });
            })
            ->select([
                'customerinfos.id as customer_id',
                'customerinfos.name as party_name',
                'customerinfos.address',
                'customerinfos.vat_no',
                'customerinfos.phoneno',
                'vat_bills.firm_type',
                DB::raw('MAX(invoices.id) as invoice_id'),
                DB::raw('COUNT(vat_bills.id) as bill_count'),
                DB::raw('MAX(vat_bills.date) as latest_bill_date'),
                DB::raw('SUM(vat_bills.amount_without_tax) as taxable_total'),
            ])
            ->groupBy([
                'customerinfos.id',
                'customerinfos.name',
                'customerinfos.address',
                'customerinfos.vat_no',
                'customerinfos.phoneno',
                'vat_bills.firm_type',
            ])
            ->orderBy('customerinfos.name')
            ->orderBy('vat_bills.firm_type')
            ->paginate(25)
            ->withQueryString();

        $breadcrumb = [
            'subtitle' => 'View',
            'title' => 'VAT Party Ledgers',
            'link' => 'VAT Party Ledgers',
        ];

        return view('vatbills.index', compact('partyLedgers', 'search', 'breadcrumb'));
    }

    public function create(invoice $invoice)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $invoice->load('customer');
        abort_unless(strtolower((string) optional($invoice->customer)->type) === 'shop', 403);

        if ($invoice->vatBill) {
            return redirect()
                ->route('onlyviewbillafterbill', ['invoiceid' => $invoice->id])
                ->with('error', 'A VAT bill has already been added for this invoice.');
        }

        $breadcrumb = [
            'subtitle' => 'Create',
            'title' => 'Add VAT Bill',
            'link' => 'Add VAT Bill',
        ];

        return view('vatbills.create', [
            'invoice' => $invoice,
            'firms' => self::FIRMS,
            'firmVatNumbers' => self::FIRM_VAT_NUMBERS,
            'firmContactNumbers' => self::FIRM_CONTACT_NUMBERS,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    public function show(Request $request, invoice $invoice)
    {
        $payload = $this->partyLedgerPayload($request, $invoice);

        $breadcrumb = [
            'subtitle' => 'View',
            'title' => 'VAT Party Ledger',
            'link' => 'VAT Party Ledger',
        ];

        return view('vatbills.show', $payload + ['breadcrumb' => $breadcrumb]);
    }

    public function pdf(Request $request, invoice $invoice)
    {
        $payload = $this->partyLedgerPayload($request, $invoice);

        return FacadePdf::setOptions([
                'dpi' => 150,
                'defaultFont' => 'dejavu sans',
            ])
            ->setPaper('a4', 'landscape')
            ->loadView('vatbills.pdf', $payload)
            ->stream('vat-party-ledger-' . $invoice->customerid . '.pdf');
    }

    public function confirmation(Request $request, invoice $invoice)
    {
        return view('vatbills.confirmation', $this->confirmationLetterPayload($request, $invoice));
    }

    public function confirmationPdf(Request $request, invoice $invoice)
    {
        $payload = $this->confirmationLetterPayload($request, $invoice);

        return FacadePdf::setOptions([
                'dpi' => 150,
                'defaultFont' => 'dejavu sans',
            ])
            ->setPaper('a4', 'portrait')
            ->loadView('vatbills.confirmation-pdf', $payload)
            ->stream('vat-confirmation-letter-' . $invoice->customerid . '.pdf');
    }

    public function store(Request $request, invoice $invoice)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $invoice->load('customer');
        abort_unless(strtolower((string) optional($invoice->customer)->type) === 'shop', 403);

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'bill_no' => ['required', 'string', 'max:100'],
            'amount_without_tax' => ['required', 'numeric', 'min:0'],
            'firm_type' => ['required', Rule::in(self::FIRMS)],
        ]);

        if (VatBill::where('invoice_id', $invoice->id)->exists()) {
            return redirect()
                ->route('onlyviewbillafterbill', ['invoiceid' => $invoice->id])
                ->with('error', 'A VAT bill has already been added for this invoice.');
        }

        $vatBill = $invoice->vatBill()->create($validated + [
            'added_by' => Auth::user()->name,
        ]);

        return redirect()
            ->route('vat-bills.show', [
                'invoice' => $invoice->id,
                'firm_type' => $vatBill->firm_type,
            ])
            ->with('vat_success', 'VAT bill #' . $vatBill->bill_no . ' confirmed and added to the party ledger.');
    }

    private function partyLedgerPayload(Request $request, invoice $invoice): array
    {
        if (!Auth::check()) {
            abort(401);
        }

        $invoice->load(['customer', 'vatBill']);
        abort_unless(strtolower((string) optional($invoice->customer)->type) === 'shop', 403);
        abort_unless($invoice->vatBill, 404);

        $filters = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'firm_type' => ['nullable', Rule::in(self::FIRMS)],
        ]);

        $firmType = $filters['firm_type'] ?? $invoice->vatBill->firm_type;

        $vatBills = VatBill::query()
            ->where('firm_type', $firmType)
            ->whereHas('invoice', function ($query) use ($invoice) {
                $query->where('customerid', $invoice->customerid);
            })
            ->when($filters['from_date'] ?? null, function ($query, $fromDate) {
                $query->whereDate('date', '>=', $fromDate);
            })
            ->when($filters['to_date'] ?? null, function ($query, $toDate) {
                $query->whereDate('date', '<=', $toDate);
            })
            ->orderBy('date')
            ->orderBy('bill_no')
            ->get();

        $rows = $vatBills->map(function (VatBill $vatBill) {
            $taxableAmount = round((float) $vatBill->amount_without_tax, 2);
            $vatAmount = round($taxableAmount * 0.13, 2);

            return [
                'date_bs' => NepaliDate::adToBsString($vatBill->date, 'en'),
                'bill_no' => $vatBill->bill_no,
                'taxable_amount' => $taxableAmount,
                'vat_amount' => $vatAmount,
                'total_amount' => round($taxableAmount + $vatAmount, 2),
            ];
        });

        return [
            'invoice' => $invoice,
            'customer' => $invoice->customer,
            'partyVatNo' => trim((string) $invoice->customer->vat_no) ?: '-',
            'rows' => $rows,
            'firms' => self::FIRMS,
            'firmType' => $firmType,
            'firmVatNo' => self::FIRM_VAT_NUMBERS[$firmType],
            'firmContactNumbers' => self::FIRM_CONTACT_NUMBERS[$firmType],
            'fromDate' => $filters['from_date'] ?? null,
            'toDate' => $filters['to_date'] ?? null,
            'totalTaxable' => $rows->sum('taxable_amount'),
            'totalVat' => $rows->sum('vat_amount'),
            'grandTotal' => $rows->sum('total_amount'),
        ];
    }

    private function confirmationLetterPayload(Request $request, invoice $invoice): array
    {
        $payload = $this->partyLedgerPayload($request, $invoice);

        $query = VatBill::query()
            ->where('firm_type', $payload['firmType'])
            ->whereHas('invoice', function ($query) use ($invoice) {
                $query->where('customerid', $invoice->customerid);
            });

        $firstDate = $payload['fromDate'] ?: (clone $query)->min('date');
        $lastDate = $payload['toDate'] ?: (clone $query)->max('date');
        $firstDate = $firstDate ?: now()->toDateString();
        $lastDate = $lastDate ?: $firstDate;

        $fromBs = NepaliDate::adToBsString($firstDate, 'en');
        $toBs = NepaliDate::adToBsString($lastDate, 'en');
        [$toBsYear, $toBsMonth] = array_map('intval', explode('-', $toBs));
        $fiscalStartYear = $toBsMonth >= 4 ? $toBsYear : $toBsYear - 1;

        $payload['letterDateBs'] = str_replace('-', '.', NepaliDate::adToBsString(now()->toDateString(), 'en'));
        $payload['periodFromBs'] = str_replace('-', '.', $fromBs);
        $payload['periodToBs'] = str_replace('-', '.', $toBs);
        $payload['fiscalYear'] = $fiscalStartYear . '-' . substr((string) ($fiscalStartYear + 1), -2);
        $payload['openingBalance'] = $this->customerBalanceThrough(
            (int) $invoice->customerid,
            Carbon::parse($firstDate)->subDay()->toDateString()
        );
        $payload['closingBalance'] = $this->customerBalanceThrough(
            (int) $invoice->customerid,
            Carbon::parse($lastDate)->toDateString()
        );

        return $payload;
    }

    private function customerBalanceThrough(int $customerId, string $toDate): float
    {
        $ledgerRows = customerledgerdetails::where('customerid', $customerId)
            ->whereDate('date', '<=', $toDate)
            ->get();

        $debit = (float) $ledgerRows->where('invoicetype', '!=', 'cash')->sum('debit');
        $credit = (float) $ledgerRows->sum('credit');
        $creditNotes = (new CustomerLedgerBalance())
            ->creditNoteRowsForLedger($customerId, '1943-04-13', $toDate)
            ->sum(function ($row) {
                $debit = (float) ($row->debit ?? 0);
                $credit = (float) ($row->credit ?? 0);

                return abs($debit) > 0.009 ? $debit : $credit;
            });

        return round($debit - $credit - $creditNotes, 2);
    }
}
