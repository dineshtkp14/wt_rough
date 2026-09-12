<?php
namespace App\Http\Controllers;
use App\Models\CompanyBill;
use App\Models\VatStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class CompanyBillController extends Controller
{
    public function __construct() { $this->middleware('auth'); }
    public function index() { $bills=CompanyBill::with('items')->latest('bill_date')->latest('id')->paginate(15); return view('vat-system.company-bills.index',compact('bills')); }
    public function create() { return view('vat-system.company-bills.form'); }
    public function store(Request $request) { $data=$this->validated($request); $bill=$this->saveBill($data); return redirect()->route('vat-system.company-bills.index')->with('success','Company purchase bill saved successfully.'); }
    public function show(CompanyBill $companyBill) { $companyBill->load('items'); return view('vat-system.company-bills.show', ['bill'=>$companyBill]); }
    public function edit(CompanyBill $companyBill) { $companyBill->load('items'); return view('vat-system.company-bills.form',['bill'=>$companyBill]); }
    public function update(Request $request, CompanyBill $companyBill) { $data=$this->validated($request,[Rule::unique('company_bills','bill_no')->ignore($companyBill->id)]); $this->saveBill($data,$companyBill); return redirect()->route('vat-system.company-bills.index')->with('success','Company purchase bill updated successfully.'); }
    public function destroy(CompanyBill $companyBill) { $companyBill->load('items'); DB::transaction(function()use($companyBill){foreach($companyBill->items as $item)$this->changeStock($item->item_name,$item->unit,-(float)$item->quantity,(float)$item->rate,'Purchase bill #'.$companyBill->bill_no.' deleted');$companyBill->delete();}); return back()->with('success','Company purchase bill deleted successfully.'); }
    private function validated(Request $request,array $billRules=[]): array { return $request->validate(['company_name'=>['required','string','max:150'],'company_vat_no'=>['nullable','string','max:50'],'company_pan_no'=>['nullable','string','max:50'],'company_address'=>['nullable','string','max:255'],'company_phone'=>['nullable','string','max:30'],'bill_no'=>array_merge(['required','string','max:50'],$billRules),'bill_date'=>['required','date'],'payment_mode'=>['nullable','string','max:50'],'discount'=>['nullable','numeric','min:0'],'notes'=>['nullable','string','max:1000'],'items'=>['required','array','min:1'],'items.*.item_name'=>['required','string','max:200'],'items.*.hs_code'=>['nullable','string','max:50'],'items.*.unit'=>['required','string','max:30'],'items.*.quantity'=>['required','numeric','gt:0'],'items.*.rate'=>['required','numeric','min:0'],'items.*.is_taxable'=>['nullable','boolean']]); }
    private function saveBill(array $data,?CompanyBill $bill=null): CompanyBill { return DB::transaction(function()use($data,$bill){ $bill ??= new CompanyBill; if($bill->exists)$bill->load('items'); if($bill->exists)foreach($bill->items as $old)$this->changeStock($old->item_name,$old->unit,-(float)$old->quantity,(float)$old->rate,'Purchase bill edit reversal'); $bill->fill(collect($data)->except('items')->merge(['discount'=>0,'added_by'=>$bill->exists?$bill->added_by:(session('user_email') ?: auth()->user()?->email)])->all()); $bill->save(); $bill->items()->delete(); foreach($data['items'] as $item){$saved=$bill->items()->create($item+['is_taxable'=>!array_key_exists('is_taxable',$item)||!empty($item['is_taxable'])]);$this->changeStock($saved->item_name,$saved->unit,(float)$saved->quantity,(float)$saved->rate,'Purchase bill #'.$bill->bill_no); } return $bill; }); }
    private function changeStock(string $name,string $unit,float $quantity,float $rate,string $reference): void { $stock=VatStock::firstOrCreate(['item_name'=>trim($name),'unit'=>trim($unit)],['quantity'=>0,'purchase_rate'=>$rate,'sale_rate'=>$rate,'reorder_level'=>0]); $stock->increment('quantity',$quantity); $stock->update(['purchase_rate'=>$rate]); $stock->movements()->create(['movement_type'=>$quantity>=0?'purchase':'purchase_reversal','quantity'=>abs($quantity),'rate'=>$rate,'reference'=>$reference]); }
}
