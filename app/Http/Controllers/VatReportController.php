<?php
namespace App\Http\Controllers;

use App\Models\VatCustomer;
use App\Models\VatSystemBill;
use App\Models\VatFirm;
use App\Models\CompanyBill;
use App\Support\NepaliDate;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class VatReportController extends Controller
{
    public function __construct() { $this->middleware('auth'); }
    public function customers(Request $request) {
        $firmId = session('vat_firm_id');
        if (!$firmId) return redirect()->route('vat-system.firm.select', ['next' => 'workspace']);
        $firm = VatFirm::find($firmId);
        $fiscalYear = $request->query('fiscal_year', $this->currentFiscalYear());
        session(['vat_report_fiscal_year' => $fiscalYear]);
        [$from, $to] = $this->fiscalDates($fiscalYear);
        $customers = VatCustomer::whereHas('salesInvoices', fn($query) => $query->where('firm_id', $firmId)->whereDate('bill_date', '>=', $from)->whereDate('bill_date', '<=', $to))->withCount(['salesInvoices' => fn($query) => $query->where('firm_id', $firmId)->whereDate('bill_date', '>=', $from)->whereDate('bill_date', '<=', $to)])->orderBy('name')->get();
        $fiscalYears = $this->fiscalYears();
        return view('vat-system.reports.customers', compact('customers', 'firm', 'fiscalYears', 'fiscalYear'));
    }
    public function printAll(Request $request) {
        $firm = VatFirm::find(session('vat_firm_id'));
        if (!$firm) return redirect()->route('vat-system.firm.select', ['next' => 'workspace']);
        $fiscalYear = $request->query('fiscal_year', session('vat_report_fiscal_year', $this->currentFiscalYear()));
        $customers = VatCustomer::orderBy('name')->get();
        $reports = $customers->map(function ($customer) use ($request) {
            $bills = $this->bills($request, $customer)->get();
            return ['customer' => $customer, 'bills' => $bills, 'total' => $bills->sum(fn($bill) => $this->billAmount($bill))];
        })->filter(fn($report) => $report['bills']->isNotEmpty())->values();
        return view('vat-system.reports.print-all', compact('firm', 'reports', 'fiscalYear'));
    }
    public function ledger(Request $request, VatCustomer $customer) { $firm=VatFirm::find(session('vat_firm_id')); if(!$firm)return redirect()->route('vat-system.firm.select',['next'=>'workspace']); $fiscalYear=$request->query('fiscal_year',session('vat_report_fiscal_year',$this->currentFiscalYear()));session(['vat_report_fiscal_year'=>$fiscalYear]);$bills=$this->bills($request,$customer)->get();$rows=$bills->map(fn($bill)=>['bill'=>$bill,'amount'=>$this->billAmount($bill)]);$total=$rows->sum('amount');$fiscalYears=$this->fiscalYears();return view('vat-system.reports.ledger',compact('customer','rows','total','firm','fiscalYears','fiscalYear')); }
    public function confirmation(Request $request, VatCustomer $customer) {
        $firm = VatFirm::find(session('vat_firm_id'));
        $bills = $this->bills($request, $customer)->when($firm, fn($query) => $query->where('firm_id', $firm->id))->get();
        $taxableSales = $bills->sum(fn($bill) => $bill->items->where('is_taxable', true)->sum(fn($item) => (float) $item->quantity * (float) $item->rate));
        $nonTaxableSales = $bills->sum(fn($bill) => $bill->items->where('is_taxable', false)->sum(fn($item) => (float) $item->quantity * (float) $item->rate));
        $vatAmount = round($taxableSales * .13, 2);
        $total = $bills->sum(fn($bill) => $this->billAmount($bill));
        $periodFrom = $request->date_from ?: optional($bills->min('bill_date'))->format('Y-m-d');
        $periodTo = $request->date_to ?: optional($bills->max('bill_date'))->format('Y-m-d');
        [$periodFrom, $periodTo] = $this->fiscalDates($request->query('fiscal_year', $this->currentFiscalYear()));
        $periodFromBs = NepaliDate::adToBsString($periodFrom, 'en');
        $periodToBs = NepaliDate::adToBsString($periodTo, 'en');
        $bsDate = NepaliDate::adToBsString(now()->toDateString(), 'en');
        $balanceSessionKey = 'vat_confirmation_balance_'.$customer->id;
        $savedBalances = session($balanceSessionKey, []);

        if ($request->has('opening_balance') || $request->has('closing_balance')) {
            $savedBalances = [
                'opening' => $request->has('opening_balance') ? (float) $request->opening_balance : ($savedBalances['opening'] ?? 0),
                'closing' => $request->has('closing_balance') ? (float) $request->closing_balance : ($savedBalances['closing'] ?? $total),
            ];
            session([$balanceSessionKey => $savedBalances]);
        }

        $openingBalance = $savedBalances['opening'] ?? 0;
        $closingBalance = $savedBalances['closing'] ?? $total;
        $fiscalYear = $request->query('fiscal_year', session('vat_report_fiscal_year', $this->currentFiscalYear()));
        return view('vat-system.reports.confirmation', compact('customer','bills','total','firm','taxableSales','nonTaxableSales','vatAmount','periodFrom','periodTo','periodFromBs','periodToBs','bsDate','openingBalance','closingBalance','fiscalYear'));
    }
    public function confirmationPdf(Request $request, VatCustomer $customer) {
        $view = $this->confirmation($request, $customer);
        return Pdf::setOptions(['dpi' => 150, 'defaultFont' => 'DejaVu Sans'])
            ->loadView('vat-system.reports.confirmation-pdf', $view->getData())
            ->setPaper('a4', 'portrait')
            ->download('balance-confirmation-'.$customer->id.'.pdf');
    }
    public function purchaseCompanies(Request $request) {
        $firm = $this->reportFirm('purchase');
        $fiscalYear = $request->query('fiscal_year', $this->currentFiscalYear());
        session(['vat_report_fiscal_year' => $fiscalYear]);
        [$from, $to] = $this->fiscalDates($fiscalYear);
        $bills = CompanyBill::with('items')->where('firm_id', $firm->id)->whereDate('bill_date', '>=', $from)->whereDate('bill_date', '<=', $to)->get();
        $companies = $bills->groupBy('company_name')->map(fn($rows, $name) => (object) ['name' => $name, 'bill_count' => $rows->count(), 'total' => $rows->sum(fn($bill) => $this->companyBillAmount($bill))])->sortBy('name')->values();
        $fiscalYears = $this->fiscalYears();
        return view('vat-system.reports.purchase-companies', compact('companies', 'firm', 'fiscalYears', 'fiscalYear'));
    }
    public function purchasePrintAll(Request $request) {
        $firm = $this->reportFirm('purchase');
        $fiscalYear = $request->query('fiscal_year', session('vat_report_fiscal_year', $this->currentFiscalYear()));
        [$from, $to] = $this->fiscalDates($fiscalYear);
        $companies = CompanyBill::where('firm_id', $firm->id)->whereDate('bill_date', '>=', $from)->whereDate('bill_date', '<=', $to)->whereNotNull('company_name')->where('company_name', '<>', '')->select('company_name')->distinct()->orderBy('company_name')->pluck('company_name')->values();
        $reports = $companies->map(function ($company) use ($request, $firm) {
            $bills = $this->purchaseBills($request, $firm, $company)->get();
            return ['company' => $company, 'bills' => $bills, 'total' => $bills->sum(fn($bill) => $this->companyBillAmount($bill))];
        });
        return view('vat-system.reports.purchase-print-all', compact('firm', 'reports', 'fiscalYear'));
    }
    public function purchaseLedger(Request $request, string $company) {
        $firm = $this->reportFirm('purchase');
        $fiscalYear = $request->query('fiscal_year', session('vat_report_fiscal_year', $this->currentFiscalYear()));
        session(['vat_report_fiscal_year' => $fiscalYear]);
        $bills = $this->purchaseBills($request, $firm, $company)->get();
        $rows = $bills->map(fn($bill) => ['bill' => $bill, 'amount' => $this->companyBillAmount($bill)]);
        $total = $rows->sum('amount');
        $fiscalYears = $this->fiscalYears();
        return view('vat-system.reports.purchase-ledger', compact('company', 'rows', 'total', 'firm', 'fiscalYears', 'fiscalYear'));
    }
    public function purchaseConfirmation(Request $request, string $company) {
        $firm = $this->reportFirm('purchase');
        $fiscalYear = $request->query('fiscal_year', session('vat_report_fiscal_year', $this->currentFiscalYear()));
        [$periodFrom, $periodTo] = $this->fiscalDates($fiscalYear);
        $bills = $this->purchaseBills($request, $firm, $company)->get();
        $taxablePurchases = $bills->sum(fn($bill) => $bill->items->where('is_taxable', true)->sum(fn($item) => (float) $item->quantity * (float) $item->rate));
        $nonTaxablePurchases = $bills->sum(fn($bill) => $bill->items->where('is_taxable', false)->sum(fn($item) => (float) $item->quantity * (float) $item->rate));
        $vatAmount = round($taxablePurchases * .13, 2);
        $total = $bills->sum(fn($bill) => $this->companyBillAmount($bill));
        $periodFromBs = NepaliDate::adToBsString($periodFrom, 'en');
        $periodToBs = NepaliDate::adToBsString($periodTo, 'en');
        $bsDate = NepaliDate::adToBsString(now()->toDateString(), 'en');
        $balances = session('vat_purchase_confirmation_balance_'.md5($company), []);
        if ($request->has('opening_balance') || $request->has('closing_balance')) {
            $balances = ['opening' => (float) $request->input('opening_balance', $balances['opening'] ?? 0), 'closing' => (float) $request->input('closing_balance', $balances['closing'] ?? $total)];
            session(['vat_purchase_confirmation_balance_'.md5($company) => $balances]);
        }
        $openingBalance = $balances['opening'] ?? 0;
        $closingBalance = $balances['closing'] ?? $total;
        return view('vat-system.reports.purchase-confirmation', compact('company','bills','total','firm','taxablePurchases','nonTaxablePurchases','vatAmount','periodFrom','periodTo','periodFromBs','periodToBs','bsDate','openingBalance','closingBalance','fiscalYear'));
    }
    public function purchaseConfirmationPdf(Request $request, string $company) {
        $view = $this->purchaseConfirmation($request, $company);
        return Pdf::setOptions(['dpi' => 150, 'defaultFont' => 'DejaVu Sans'])->loadView('vat-system.reports.purchase-confirmation-pdf', $view->getData())->setPaper('a4', 'portrait')->download('purchase-balance-confirmation-'.md5($company).'.pdf');
    }
    private function reportFirm(string $next): VatFirm { $firm = VatFirm::whereKey(session('vat_firm_id'))->where('is_active', true)->first(); if (!$firm) abort(redirect()->route('vat-system.firm.select', ['next' => $next])); return $firm; }
    private function purchaseBills(Request $request, VatFirm $firm, string $company) { [$from, $to] = $this->fiscalDates($request->query('fiscal_year', session('vat_report_fiscal_year', $this->currentFiscalYear()))); return CompanyBill::with('items')->where('firm_id', $firm->id)->where('company_name', $company)->whereDate('bill_date', '>=', $from)->whereDate('bill_date', '<=', $to)->latest('bill_date')->latest('id'); }
    private function companyBillAmount(CompanyBill $bill): float { $total = $bill->items->sum(fn($item) => (float) $item->quantity * (float) $item->rate); $taxable = $bill->items->where('is_taxable', true)->sum(fn($item) => (float) $item->quantity * (float) $item->rate); return round($total - (float) $bill->discount + round($taxable * .13, 2), 2); }
    private function bills(Request $request,VatCustomer $customer){[$from,$to]=$this->fiscalDates($request->query('fiscal_year',session('vat_report_fiscal_year',$this->currentFiscalYear())));return VatSystemBill::with('items')->where('customer_id',$customer->id)->when(session('vat_firm_id'),fn($q,$firmId)=>$q->where('firm_id',$firmId))->whereDate('bill_date','>=',$from)->whereDate('bill_date','<=',$to)->latest('bill_date')->latest('id');}
    private function currentFiscalYear(): string { $bs=explode('-',NepaliDate::adToBsString(now()->toDateString(),'en')); $year=(int)$bs[0]; return sprintf('%d/%02d',$bs[1] >= 4 ? $year : $year-1, ($bs[1] >= 4 ? $year+1 : $year)%100); }
    private function fiscalDates(string $fiscalYear): array { $startYear=(int)explode('/',$fiscalYear)[0]; $from=NepaliDate::bsToAdString($startYear,4,1); $to=Carbon::parse(NepaliDate::bsToAdString($startYear+1,4,1))->subDay()->toDateString(); return [$from,$to]; }
    private function fiscalYears(): array { $current=(int)explode('/',$this->currentFiscalYear())[0]; $years=[]; for($i=0;$i<6;$i++) $years[sprintf('%d/%02d',$current-$i,($current-$i+1)%100)] = sprintf('FY %d/%02d — Shrawan 1 to Ashad end',$current-$i,($current-$i+1)%100); return $years; }
    private function billAmount(VatSystemBill $bill):float{$total=$bill->items->sum(fn($i)=>(float)$i->quantity*(float)$i->rate);$taxable=$bill->items->where('is_taxable',true)->sum(fn($i)=>(float)$i->quantity*(float)$i->rate);return round($total-(float)$bill->discount+round($taxable*.13,2),2);}
}
