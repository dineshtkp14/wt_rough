<?php
namespace App\Http\Controllers;

use App\Models\VatCustomer;
use App\Models\VatSystemBill;
use App\Models\VatFirm;
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
    private function bills(Request $request,VatCustomer $customer){[$from,$to]=$this->fiscalDates($request->query('fiscal_year',session('vat_report_fiscal_year',$this->currentFiscalYear())));return VatSystemBill::with('items')->where('customer_id',$customer->id)->when(session('vat_firm_id'),fn($q,$firmId)=>$q->where('firm_id',$firmId))->whereDate('bill_date','>=',$from)->whereDate('bill_date','<=',$to)->latest('bill_date')->latest('id');}
    private function currentFiscalYear(): string { $bs=explode('-',NepaliDate::adToBsString(now()->toDateString(),'en')); $year=(int)$bs[0]; return sprintf('%d/%02d',$bs[1] >= 4 ? $year : $year-1, ($bs[1] >= 4 ? $year+1 : $year)%100); }
    private function fiscalDates(string $fiscalYear): array { $startYear=(int)explode('/',$fiscalYear)[0]; $from=NepaliDate::bsToAdString($startYear,4,1); $to=Carbon::parse(NepaliDate::bsToAdString($startYear+1,4,1))->subDay()->toDateString(); return [$from,$to]; }
    private function fiscalYears(): array { $current=(int)explode('/',$this->currentFiscalYear())[0]; $years=[]; for($i=0;$i<6;$i++) $years[sprintf('%d/%02d',$current-$i,($current-$i+1)%100)] = sprintf('FY %d/%02d — Shrawan 1 to Ashad end',$current-$i,($current-$i+1)%100); return $years; }
    private function billAmount(VatSystemBill $bill):float{$total=$bill->items->sum(fn($i)=>(float)$i->quantity*(float)$i->rate);$taxable=$bill->items->where('is_taxable',true)->sum(fn($i)=>(float)$i->quantity*(float)$i->rate);return round($total-(float)$bill->discount+round($taxable*.13,2),2);}
}
