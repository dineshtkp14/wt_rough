<?php

namespace App\Http\Controllers;

use App\Models\invoice;
use App\Models\VatBill;
use App\Models\VatConfirmationDetail;
use App\Models\customerledgerdetails;
use App\Services\CustomerLedgerBalance;
use App\Support\NepaliDate;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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

        $partyLedgers = $this->partyLedgerGroupsQuery($search)
            ->paginate(25)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'rows' => view('vatbills._party_ledger_rows', compact('partyLedgers'))->render(),
                'pagination' => $partyLedgers->hasPages() ? $partyLedgers->links()->render() : '',
                'count' => $partyLedgers->total(),
            ]);
        }

        $breadcrumb = [
            'subtitle' => 'View',
            'title' => 'VAT Party Ledgers',
            'link' => 'VAT Party Ledgers',
        ];

        return view('vatbills.index', compact('partyLedgers', 'search', 'breadcrumb'));
    }

    public function standaloneCreate()
    {
        if (!Auth::check()) return redirect('/login');

        return view('vatbills.standalone-form', [
            'vatBill' => null,
            'firms' => self::FIRMS,
            'firmVatNumbers' => self::FIRM_VAT_NUMBERS,
            'firmContactNumbers' => self::FIRM_CONTACT_NUMBERS,
        ]);
    }

    public function standaloneStore(Request $request)
    {
        if (!Auth::check()) return redirect('/login');

        $validated = $this->validateStandaloneVatBill($request);
        $vatBill = VatBill::create($validated + ['added_by' => Auth::user()->name]);

        return redirect()->route('vat-party-ledgers.show', $vatBill)
            ->with('vat_success', 'VAT bill #' . $vatBill->bill_no . ' created successfully.');
    }

    public function entryEdit(VatBill $vatBill)
    {
        if (!Auth::check()) return redirect('/login');

        return view('vatbills.standalone-form', [
            'vatBill' => $vatBill,
            'firms' => self::FIRMS,
            'firmVatNumbers' => self::FIRM_VAT_NUMBERS,
            'firmContactNumbers' => self::FIRM_CONTACT_NUMBERS,
        ]);
    }

    public function entryUpdate(Request $request, VatBill $vatBill)
    {
        if (!Auth::check()) return redirect('/login');
        $vatBill->update($this->validateStandaloneVatBill($request, false));

        return redirect()->route('vat-party-ledgers.show', $vatBill)
            ->with('vat_success', 'VAT bill #' . $vatBill->bill_no . ' updated successfully.');
    }

    public function entryDestroy(VatBill $vatBill)
    {
        if (!Auth::check()) return redirect('/login');
        $billNo = $vatBill->bill_no;
        $vatBill->delete();

        return redirect()->route('vat-bills.index')->with('vat_success', 'VAT bill #' . $billNo . ' deleted.');
    }

    public function printAllLedgers(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $ledgers = $this->bulkLedgerPayloads(trim((string) $request->query('search')), false);

        return view('vatbills.print-all-ledgers', compact('ledgers'));
    }

    public function printAllConfirmations(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $letters = $this->bulkLedgerPayloads(trim((string) $request->query('search')), true);

        return view('vatbills.print-all-confirmations', compact('letters'));
    }

    public function missing(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $search = trim((string) $request->query('search'));

        $invoices = invoice::query()
            ->with('customer')
            ->where(function ($query) {
                $query->where('inv_type', 'cash')
                    ->orWhereHas('customer', function ($customerQuery) {
                        $customerQuery->whereRaw('LOWER(TRIM(COALESCE(type, ""))) = ?', ['shop']);
                    });
            })
            ->doesntHave('vatBill')
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';
                $query->where(function ($searchQuery) use ($search, $like) {
                    if (ctype_digit($search)) {
                        $searchQuery->orWhere('id', (int) $search);
                    }

                    $searchQuery->orWhereHas('customer', function ($customerQuery) use ($like) {
                        $customerQuery->where('name', 'like', $like)
                            ->orWhere('address', 'like', $like)
                            ->orWhere('vat_no', 'like', $like)
                            ->orWhere('phoneno', 'like', $like);
                    });
                });
            })
            ->orderByDesc('inv_date')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'rows' => view('vatbills._missing_rows', compact('invoices'))->render(),
                'pagination' => $invoices->hasPages() ? $invoices->links()->render() : '',
                'count' => $invoices->total(),
            ]);
        }

        $breadcrumb = [
            'subtitle' => 'View',
            'title' => 'Invoices Missing VAT Bill',
            'link' => 'Invoices Missing VAT Bill',
        ];

        return view('vatbills.missing', compact('invoices', 'search', 'breadcrumb'));
    }

    private function partyLedgerGroupsQuery(string $search = '')
    {
        $partyKey = "COALESCE(NULLIF(TRIM(vat_bills.party_vat_no), ''), CONCAT('NAME:', LOWER(TRIM(vat_bills.party_name)), '|PHONE:', COALESCE(vat_bills.party_phone, '')))";

        return VatBill::query()
            ->leftJoin('invoices', 'vat_bills.invoice_id', '=', 'invoices.id')
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';
                $query->where(function ($searchQuery) use ($like) {
                    $searchQuery->where('vat_bills.party_name', 'like', $like)
                        ->orWhere('vat_bills.party_address', 'like', $like)
                        ->orWhere('vat_bills.party_vat_no', 'like', $like)
                        ->orWhere('vat_bills.party_phone', 'like', $like)
                        ->orWhere('vat_bills.firm_type', 'like', $like);
                });
            })
            ->select([
                'vat_bills.firm_type',
                DB::raw('MAX(vat_bills.party_name) as party_name'),
                DB::raw('MAX(vat_bills.party_address) as address'),
                DB::raw('MAX(vat_bills.party_vat_no) as vat_no'),
                DB::raw('MAX(vat_bills.party_phone) as phoneno'),
                DB::raw('MAX(vat_bills.id) as vat_bill_id'),
                DB::raw('COUNT(vat_bills.id) as bill_count'),
                DB::raw('MAX(vat_bills.date) as latest_bill_date'),
                DB::raw('SUM(vat_bills.amount_without_tax) as taxable_total'),
            ])
            ->groupBy([
                'vat_bills.firm_type',
                DB::raw($partyKey),
            ])
            ->orderByRaw('MAX(vat_bills.party_name)')
            ->orderBy('vat_bills.firm_type');
    }

    private function bulkLedgerPayloads(string $search, bool $confirmationLetters)
    {
        return $this->partyLedgerGroupsQuery($search)
            ->get()
            ->map(function ($group) use ($confirmationLetters) {
                $vatBill = VatBill::findOrFail($group->vat_bill_id);
                $ledgerRequest = Request::create('/', 'GET', [
                    'firm_type' => $group->firm_type,
                ]);

                return $confirmationLetters
                    ? $this->confirmationLetterPayload($ledgerRequest, $vatBill)
                    : $this->partyLedgerPayload($ledgerRequest, $vatBill);
            });
    }

    public function create(invoice $invoice)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $invoice->load('customer');
        $this->ensureVatEligible($invoice);

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
            'vatBill' => null,
            'firms' => self::FIRMS,
            'firmVatNumbers' => self::FIRM_VAT_NUMBERS,
            'firmContactNumbers' => self::FIRM_CONTACT_NUMBERS,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    public function edit(invoice $invoice)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $invoice->load(['customer', 'vatBill']);
        $this->ensureVatEligible($invoice, true);

        $breadcrumb = [
            'subtitle' => 'Edit',
            'title' => 'Edit VAT Bill',
            'link' => 'Edit VAT Bill',
        ];

        return view('vatbills.create', [
            'invoice' => $invoice,
            'vatBill' => $invoice->vatBill,
            'firms' => self::FIRMS,
            'firmVatNumbers' => self::FIRM_VAT_NUMBERS,
            'firmContactNumbers' => self::FIRM_CONTACT_NUMBERS,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    public function show(Request $request, invoice $invoice)
    {
        $invoice->load('vatBill');
        abort_unless($invoice->vatBill, 404);
        $payload = $this->partyLedgerPayload($request, $invoice->vatBill);

        $breadcrumb = [
            'subtitle' => 'View',
            'title' => 'VAT Party Ledger',
            'link' => 'VAT Party Ledger',
        ];

        return view('vatbills.show', $payload + ['breadcrumb' => $breadcrumb]);
    }

    public function pdf(Request $request, invoice $invoice)
    {
        $invoice->load('vatBill');
        abort_unless($invoice->vatBill, 404);
        $payload = $this->partyLedgerPayload($request, $invoice->vatBill);

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
        $invoice->load('vatBill');
        abort_unless($invoice->vatBill, 404);
        return view('vatbills.confirmation', $this->confirmationLetterPayload($request, $invoice->vatBill));
    }

    public function confirmationPdf(Request $request, invoice $invoice)
    {
        $invoice->load('vatBill');
        abort_unless($invoice->vatBill, 404);
        $payload = $this->confirmationLetterPayload($request, $invoice->vatBill);

        return FacadePdf::setOptions([
                'dpi' => 150,
                'defaultFont' => 'dejavu sans',
            ])
            ->setPaper('a4', 'portrait')
            ->loadView('vatbills.confirmation-pdf', $payload)
            ->stream('vat-confirmation-letter-' . $invoice->customerid . '.pdf');
    }

    public function partyShow(Request $request, VatBill $vatBill)
    {
        $payload = $this->partyLedgerPayload($request, $vatBill);
        $breadcrumb = ['subtitle' => 'View', 'title' => 'VAT Party Ledger', 'link' => 'VAT Party Ledger'];
        return view('vatbills.show', $payload + ['breadcrumb' => $breadcrumb]);
    }

    public function partyPdf(Request $request, VatBill $vatBill)
    {
        return FacadePdf::setOptions(['dpi' => 150, 'defaultFont' => 'dejavu sans'])
            ->setPaper('a4', 'landscape')
            ->loadView('vatbills.pdf', $this->partyLedgerPayload($request, $vatBill))
            ->stream('vat-party-ledger-' . $vatBill->id . '.pdf');
    }

    public function partyConfirmation(Request $request, VatBill $vatBill)
    {
        return view('vatbills.confirmation', $this->confirmationLetterPayload($request, $vatBill));
    }

    public function partyConfirmationPdf(Request $request, VatBill $vatBill)
    {
        return FacadePdf::setOptions(['dpi' => 150, 'defaultFont' => 'dejavu sans'])
            ->setPaper('a4', 'portrait')
            ->loadView('vatbills.confirmation-pdf', $this->confirmationLetterPayload($request, $vatBill))
            ->stream('vat-confirmation-letter-' . $vatBill->id . '.pdf');
    }

    public function saveConfirmationDetails(Request $request, VatBill $vatBill)
    {
        if (!Auth::check()) return redirect('/login');

        $validated = $request->validate([
            'firm_type' => ['required', Rule::in(self::FIRMS)],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'purchase_exempted' => ['nullable', 'numeric', 'min:0'],
            'purchase_taxable' => ['nullable', 'numeric', 'min:0'],
            'purchase_return_exempted' => ['nullable', 'numeric', 'min:0'],
            'purchase_return_taxable' => ['nullable', 'numeric', 'min:0'],
            'sales_exempted' => ['nullable', 'numeric', 'min:0'],
            'sales_return_exempted' => ['nullable', 'numeric', 'min:0'],
            'sales_return_taxable' => ['nullable', 'numeric', 'min:0'],
            'opening_balance_amount' => ['nullable', 'numeric', 'min:0'],
            'opening_balance_side' => ['required', Rule::in(['nil', 'dr', 'cr'])],
            'closing_balance_amount' => ['nullable', 'numeric', 'min:0'],
            'closing_balance_side' => ['required', Rule::in(['nil', 'dr', 'cr'])],
        ]);

        foreach (['purchase_exempted', 'purchase_taxable', 'purchase_return_exempted', 'purchase_return_taxable', 'sales_exempted', 'sales_return_exempted', 'sales_return_taxable'] as $field) {
            $validated[$field] = $validated[$field] ?? 0;
        }

        VatConfirmationDetail::updateOrCreate([
            'party_key' => $this->partyKey($vatBill),
            'firm_type' => $validated['firm_type'],
            'from_date' => $validated['from_date'],
            'to_date' => $validated['to_date'],
        ], $validated + ['added_by' => Auth::user()->name]);

        return redirect()->route('vat-party-ledgers.confirmation', [
            'vatBill' => $vatBill->id,
            'firm_type' => $validated['firm_type'],
            'from_date' => $validated['from_date'],
            'to_date' => $validated['to_date'],
        ])->with('confirmation_success', 'Confirmation details updated successfully.');
    }

    public function store(Request $request, invoice $invoice)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $invoice->load('customer');
        $this->ensureVatEligible($invoice);
        $this->normalizeVatBillNumber($request);

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'bill_no' => ['required', 'string', 'max:100', $this->uniqueVatBillNumberRule((string) $request->input('firm_type'))],
            'amount_without_tax' => ['required', 'numeric', 'min:0'],
            'firm_type' => ['required', Rule::in(self::FIRMS)],
            'party_name' => ['required', 'string', 'max:255'],
            'party_address' => ['nullable', 'string', 'max:255'],
            'party_vat_no' => ['nullable', 'string', 'max:50'],
            'party_phone' => ['nullable', 'string', 'max:50'],
        ], $this->vatBillValidationMessages());

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

    public function storeFromPartyLedger(Request $request, VatBill $vatBill)
    {
        if (!Auth::check()) return redirect('/login');
        $this->normalizeVatBillNumber($request);

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'bill_no' => ['required', 'string', 'max:100', $this->uniqueVatBillNumberRule($vatBill->firm_type)],
            'amount_without_tax' => ['required', 'numeric', 'min:0'],
        ], $this->vatBillValidationMessages());

        $newVatBill = VatBill::create($validated + [
            'invoice_id' => null,
            'firm_type' => $vatBill->firm_type,
            'party_name' => $vatBill->party_name,
            'party_address' => $vatBill->party_address,
            'party_vat_no' => $vatBill->party_vat_no,
            'party_phone' => $vatBill->party_phone,
            'added_by' => Auth::user()->name,
        ]);

        return redirect()->route('vat-party-ledgers.show', $newVatBill)
            ->with('vat_success', 'VAT bill #' . $newVatBill->bill_no . ' added to this party ledger.');
    }

    public function update(Request $request, invoice $invoice)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $invoice->load(['customer', 'vatBill']);
        $this->ensureVatEligible($invoice, true);
        $this->normalizeVatBillNumber($request);

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'bill_no' => ['required', 'string', 'max:100', $this->uniqueVatBillNumberRule(
                (string) $request->input('firm_type'),
                $invoice->vatBill->id
            )],
            'amount_without_tax' => ['required', 'numeric', 'min:0'],
            'firm_type' => ['required', Rule::in(self::FIRMS)],
            'party_name' => ['required', 'string', 'max:255'],
            'party_address' => ['nullable', 'string', 'max:255'],
            'party_vat_no' => ['nullable', 'string', 'max:50'],
            'party_phone' => ['nullable', 'string', 'max:50'],
        ], $this->vatBillValidationMessages());

        $invoice->vatBill->update($validated);

        return redirect()
            ->route('vat-bills.show', [
                'invoice' => $invoice->id,
                'firm_type' => $invoice->vatBill->firm_type,
            ])
            ->with('vat_success', 'VAT bill #' . $invoice->vatBill->bill_no . ' updated successfully.');
    }

    public function destroy(invoice $invoice)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $invoice->load(['customer', 'vatBill']);
        $this->ensureVatEligible($invoice, true);

        $billNo = $invoice->vatBill->bill_no;
        $invoice->vatBill->delete();

        return redirect()
            ->route('vat-bills.index')
            ->with('vat_success', 'VAT bill #' . $billNo . ' deleted. The sales invoice was not deleted.');
    }

    private function partyLedgerPayload(Request $request, VatBill $anchor): array
    {
        if (!Auth::check()) {
            abort(401);
        }

        $anchor->load('invoice.customer');
        $invoice = $anchor->invoice;

        foreach (['from_date_bs' => 'from_date', 'to_date_bs' => 'to_date'] as $bsField => $adField) {
            $bsDate = trim((string) $request->query($bsField));

            if ($bsDate === '') {
                continue;
            }

            if (!preg_match('/^(\d{4})[-\/.](\d{1,2})[-\/.](\d{1,2})$/', $bsDate, $parts)) {
                throw ValidationException::withMessages([
                    $bsField => 'Enter the B.S. date in YYYY-MM-DD format.',
                ]);
            }

            try {
                $request->merge([
                    $adField => NepaliDate::bsToAdString((int) $parts[1], (int) $parts[2], (int) $parts[3]),
                ]);
            } catch (\InvalidArgumentException $exception) {
                throw ValidationException::withMessages([
                    $bsField => 'The entered B.S. date is invalid or outside the supported range.',
                ]);
            }
        }

        $filters = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'from_date_bs' => ['nullable', 'string'],
            'to_date_bs' => ['nullable', 'string'],
            'firm_type' => ['nullable', Rule::in(self::FIRMS)],
        ]);

        $firmType = $filters['firm_type'] ?? $anchor->firm_type;

        $vatBills = $this->vatBillsForParty($anchor)
            ->where('firm_type', $firmType)
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
                'invoice_id' => $vatBill->invoice_id,
                'vat_bill_id' => $vatBill->id,
                'date_bs' => NepaliDate::adToBsString($vatBill->date, 'en'),
                'bill_no' => $vatBill->bill_no,
                'taxable_amount' => $taxableAmount,
                'vat_amount' => $vatAmount,
                'total_amount' => round($taxableAmount + $vatAmount, 2),
            ];
        });

        $party = (object) [
            'name' => $anchor->party_name,
            'address' => $anchor->party_address,
            'vat_no' => $anchor->party_vat_no,
            'phoneno' => $anchor->party_phone,
        ];
        $sameAccountParty = $invoice && $invoice->inv_type !== 'cash' && $invoice->customer
            && (
                ($party->vat_no && trim((string) $invoice->customer->vat_no) === trim((string) $party->vat_no))
                || (!$party->vat_no && strcasecmp(trim($invoice->customer->name), trim($party->name)) === 0)
            );

        return [
            'invoice' => $invoice,
            'anchorVatBill' => $anchor,
            'customer' => $party,
            'partyVatNo' => trim((string) $party->vat_no) ?: '-',
            'hasLedgerAccount' => $sameAccountParty,
            'rows' => $rows,
            'firms' => self::FIRMS,
            'firmType' => $firmType,
            'firmVatNo' => self::FIRM_VAT_NUMBERS[$firmType],
            'firmContactNumbers' => self::FIRM_CONTACT_NUMBERS[$firmType],
            'fromDate' => $filters['from_date'] ?? null,
            'toDate' => $filters['to_date'] ?? null,
            'fromDateBs' => !empty($filters['from_date']) ? NepaliDate::adToBsString($filters['from_date'], 'en') : null,
            'toDateBs' => !empty($filters['to_date']) ? NepaliDate::adToBsString($filters['to_date'], 'en') : null,
            'totalTaxable' => $rows->sum('taxable_amount'),
            'totalVat' => $rows->sum('vat_amount'),
            'grandTotal' => $rows->sum('total_amount'),
        ];
    }

    private function confirmationLetterPayload(Request $request, VatBill $anchor): array
    {
        $payload = $this->partyLedgerPayload($request, $anchor);
        $invoice = $anchor->invoice;

        $query = $this->vatBillsForParty($anchor)
            ->where('firm_type', $payload['firmType'])
            ;

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
        $payload['openingBalance'] = $payload['hasLedgerAccount'] && $invoice
            ? $this->customerBalanceThrough((int) $invoice->customerid, Carbon::parse($firstDate)->subDay()->toDateString())
            : 0;
        $payload['closingBalance'] = $payload['hasLedgerAccount'] && $invoice
            ? $this->customerBalanceThrough((int) $invoice->customerid, Carbon::parse($lastDate)->toDateString())
            : 0;

        $confirmationDetail = VatConfirmationDetail::where([
            'party_key' => $this->partyKey($anchor),
            'firm_type' => $payload['firmType'],
            'from_date' => $firstDate,
            'to_date' => $lastDate,
        ])->first();

        if ($confirmationDetail) {
            $payload['openingBalance'] = $this->signedBalance(
                $confirmationDetail->opening_balance_amount,
                $confirmationDetail->opening_balance_side
            );
            $payload['closingBalance'] = $this->signedBalance(
                $confirmationDetail->closing_balance_amount,
                $confirmationDetail->closing_balance_side
            );
        }

        $payload['periodFromAd'] = $firstDate;
        $payload['periodToAd'] = $lastDate;
        $payload['confirmationDetail'] = $confirmationDetail;
        $payload['purchaseRow'] = $this->confirmationTransactionRow(
            $confirmationDetail?->purchase_exempted,
            $confirmationDetail?->purchase_taxable
        );
        $payload['purchaseReturnRow'] = $this->confirmationTransactionRow(
            $confirmationDetail?->purchase_return_exempted,
            $confirmationDetail?->purchase_return_taxable
        );
        $payload['salesReturnRow'] = $this->confirmationTransactionRow(
            $confirmationDetail?->sales_return_exempted,
            $confirmationDetail?->sales_return_taxable
        );
        $salesExempted = round((float) ($confirmationDetail?->sales_exempted ?? 0), 2);
        $payload['salesRow'] = [
            'exempted' => $salesExempted,
            'taxable' => round((float) $payload['totalTaxable'], 2),
            'vat' => round((float) $payload['totalVat'], 2),
            'total' => round($salesExempted + (float) $payload['grandTotal'], 2),
        ];

        return $payload;
    }

    private function confirmationTransactionRow($exempted, $taxable): array
    {
        $exempted = round((float) ($exempted ?? 0), 2);
        $taxable = round((float) ($taxable ?? 0), 2);
        $vat = round($taxable * 0.13, 2);

        return [
            'exempted' => $exempted,
            'taxable' => $taxable,
            'vat' => $vat,
            'total' => round($exempted + $taxable + $vat, 2),
        ];
    }

    private function signedBalance($amount, ?string $side): float
    {
        $amount = round(abs((float) ($amount ?? 0)), 2);

        return match ($side) {
            'dr' => $amount,
            'cr' => -$amount,
            default => 0,
        };
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

    private function vatBillsForParty(VatBill $anchor)
    {
        $vatNo = trim((string) $anchor->party_vat_no);

        if ($vatNo !== '') {
            return VatBill::query()->where('party_vat_no', $vatNo);
        }

        return VatBill::query()
            ->where('party_name', $anchor->party_name)
            ->where('party_phone', $anchor->party_phone);
    }

    private function partyKey(VatBill $vatBill): string
    {
        $vatNo = trim((string) $vatBill->party_vat_no);

        if ($vatNo !== '') {
            return 'VAT:' . strtoupper($vatNo);
        }

        $name = strtolower(trim((string) $vatBill->party_name));
        $phone = preg_replace('/\D+/', '', (string) $vatBill->party_phone);

        return 'NAME:' . $name . '|PHONE:' . $phone;
    }

    private function ensureVatEligible(invoice $invoice, bool $mustExist = false): void
    {
        if ($mustExist) {
            abort_unless($invoice->vatBill, 404);
            return;
        }

        $isShop = strtolower(trim((string) optional($invoice->customer)->type)) === 'shop';
        abort_unless($isShop || $invoice->inv_type === 'cash', 403);
    }

    private function validateStandaloneVatBill(Request $request, bool $includePartyDetails = true): array
    {
        $this->normalizeVatBillNumber($request);
        $currentVatBill = $request->route('vatBill');

        $rules = [
            'date' => ['required', 'date'],
            'bill_no' => ['required', 'string', 'max:100', $this->uniqueVatBillNumberRule(
                (string) $request->input('firm_type'),
                $currentVatBill instanceof VatBill ? $currentVatBill->id : null
            )],
            'amount_without_tax' => ['required', 'numeric', 'min:0'],
            'firm_type' => ['required', Rule::in(self::FIRMS)],
        ];

        if ($includePartyDetails) {
            $rules += [
                'party_name' => ['required', 'string', 'max:255'],
                'party_address' => ['nullable', 'string', 'max:255'],
                'party_vat_no' => ['nullable', 'string', 'max:50'],
                'party_phone' => ['nullable', 'string', 'max:50'],
            ];
        }

        return $request->validate($rules, $this->vatBillValidationMessages());
    }

    private function uniqueVatBillNumberRule(string $firmType, ?int $ignoreVatBillId = null)
    {
        $rule = Rule::unique('vat_bills', 'bill_no')
            ->where(fn ($query) => $query->where('firm_type', $firmType));

        return $ignoreVatBillId ? $rule->ignore($ignoreVatBillId) : $rule;
    }

    private function normalizeVatBillNumber(Request $request): void
    {
        if ($request->has('bill_no')) {
            $request->merge(['bill_no' => trim((string) $request->input('bill_no'))]);
        }
    }

    private function vatBillValidationMessages(): array
    {
        return [
            'bill_no.unique' => 'This VAT bill number already exists for the selected firm. Please enter a different bill number.',
        ];
    }
}
