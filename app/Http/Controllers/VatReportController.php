<?php
namespace App\Http\Controllers;

use App\Models\VatCustomer;
use App\Models\VatSystemBill;
use Illuminate\Http\Request;

class VatReportController extends Controller
{
    public function __construct() { $this->middleware('auth'); }
    public function customers() { $customers = VatCustomer::withCount('salesInvoices')->orderBy('name')->get(); return view('vat-system.reports.customers', compact('customers')); }
    public function ledger(Request $request, VatCustomer $customer) { $bills=$this->bills($request,$customer)->get();$rows=$bills->map(fn($bill)=>['bill'=>$bill,'amount'=>$this->billAmount($bill)]);$total=$rows->sum('amount');return view('vat-system.reports.ledger',compact('customer','rows','total')); }
    public function confirmation(Request $request, VatCustomer $customer) { $bills=$this->bills($request,$customer)->get();$total=$bills->sum(fn($bill)=>$this->billAmount($bill));return view('vat-system.reports.confirmation',compact('customer','bills','total')); }
    private function bills(Request $request,VatCustomer $customer){return VatSystemBill::with('items')->where('customer_id',$customer->id)->when($request->date_from,fn($q,$date)=>$q->whereDate('bill_date','>=',$date))->when($request->date_to,fn($q,$date)=>$q->whereDate('bill_date','<=',$date))->latest('bill_date')->latest('id');}
    private function billAmount(VatSystemBill $bill):float{$total=$bill->items->sum(fn($i)=>(float)$i->quantity*(float)$i->rate);$taxable=$bill->items->where('is_taxable',true)->sum(fn($i)=>(float)$i->quantity*(float)$i->rate);return round($total-(float)$bill->discount+round($taxable*.13,2),2);}
}
