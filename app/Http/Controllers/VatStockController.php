<?php
namespace App\Http\Controllers;
use App\Models\VatFirm;
use App\Models\VatStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
class VatStockController extends Controller
{
    public function __construct(){ $this->middleware('auth'); }
    public function openingCreate(){
        $firm=VatFirm::find(session('vat_firm_id'));
        if(!$firm)return redirect()->route('vat-system.firm.select',['next'=>'stock']);
        return view('vat-system.stock.opening',compact('firm'));
    }
    public function openingStore(Request $request){
        $firm=VatFirm::find(session('vat_firm_id'));
        if(!$firm)return redirect()->route('vat-system.firm.select',['next'=>'stock']);
        $data=$request->validate([
            'entries'=>'required|array|min:1',
            'entries.*.item_name'=>'required|string|max:200',
            'entries.*.hs_code'=>'nullable|string|max:50',
            'entries.*.unit'=>'required|string|max:30',
            'entries.*.quantity'=>'required|numeric|gt:0',
            'entries.*.purchase_rate'=>'nullable|numeric|min:0',
            'entries.*.sale_rate'=>'nullable|numeric|min:0',
            'entries.*.reorder_level'=>'nullable|numeric|min:0',
            'entries.*.notes'=>'nullable|string|max:500',
        ]);
        DB::transaction(function()use($firm,$data){
            foreach($data['entries'] as $entry){
                $stock=VatStock::firstOrNew(['firm_id'=>$firm->id,'item_name'=>trim($entry['item_name']),'unit'=>trim($entry['unit'])]);
                $before=(float)($stock->exists?$stock->quantity:0);
                $qty=(float)$entry['quantity'];
                $purchaseRate=$entry['purchase_rate'] !== null ? (float)$entry['purchase_rate'] : (float)($stock->purchase_rate ?? 0);
                $stock->fill([
                    'firm_id'=>$firm->id,'item_name'=>trim($entry['item_name']),'hs_code'=>trim((string)($entry['hs_code']??'')) ?: ($stock->hs_code ?? null),'unit'=>trim($entry['unit']),
                    'quantity'=>$before+$qty,'purchase_rate'=>$purchaseRate,
                    'sale_rate'=>$entry['sale_rate'] !== null ? (float)$entry['sale_rate'] : (float)($stock->sale_rate ?? 0),
                    'reorder_level'=>$entry['reorder_level'] !== null ? (float)$entry['reorder_level'] : (float)($stock->reorder_level ?? 0),
                    'notes'=>$entry['notes']??null,
                ]);
                $stock->save();
                $stock->movements()->create([
                    'movement_type'=>'opening_stock','quantity'=>$qty,'rate'=>$purchaseRate,
                    'reference'=>'Opening stock','notes'=>$entry['notes']??null,
                    'user_email'=>session('user_email')?:auth()->user()?->email,
                    'quantity_before'=>$before,'quantity_after'=>$before+$qty,
                ]);
            }
        });
        return redirect()->route('vat-system.stock.index')->with('success',count($data['entries']).' opening stock item(s) saved successfully.');
    }
    public function index(Request $request){$firm=VatFirm::find(session('vat_firm_id'));if(!$firm)return redirect()->route('vat-system.firm.select',['next'=>'stock']);$query=$this->stockQuery($request,$firm->id);$stocks=$query->paginate(20)->withQueryString();if($request->expectsJson())return response()->json(['total'=>$stocks->total(),'items'=>$stocks->getCollection()->map(fn($s)=>['id'=>$s->id,'item_name'=>$s->item_name,'unit'=>$s->unit,'quantity'=>(float)$s->quantity,'purchase_rate'=>(float)$s->purchase_rate,'sale_rate'=>(float)$s->sale_rate,'reorder_level'=>(float)$s->reorder_level,'has_opening_stock'=>(bool)$s->has_opening_stock])]);return view('vat-system.stock.index',compact('stocks','firm'));}
    public function exportPdf(Request $request){$firm=VatFirm::find(session('vat_firm_id'));if(!$firm)return redirect()->route('vat-system.firm.select',['next'=>'stock']);$stocks=$this->stockQuery($request,$firm->id)->get();return Pdf::loadView('vat-system.stock.pdf',compact('stocks','firm'))->setPaper('a4','landscape')->download('vat-stock-'.$firm->id.'.pdf');}
    public function exportExcel(Request $request){$firm=VatFirm::find(session('vat_firm_id'));if(!$firm)return redirect()->route('vat-system.firm.select',['next'=>'stock']);$stocks=$this->stockQuery($request,$firm->id)->get();$html=view('vat-system.stock.excel',compact('stocks','firm'))->render();return response($html,200,['Content-Type'=>'application/vnd.ms-excel; charset=UTF-8','Content-Disposition'=>'attachment; filename="vat-stock-'.$firm->id.'.xls"']);}
    public function adjust(Request $request,VatStock $stock){abort_unless((int)$stock->firm_id===(int)session('vat_firm_id'),404);$data=$request->validate(['type'=>'required|in:in,out','quantity'=>'required|numeric|gt:0','rate'=>'nullable|numeric|min:0','notes'=>'nullable|string|max:500']);if($data['type']==='out'&&$stock->quantity<$data['quantity'])return back()->withErrors(['quantity'=>'Insufficient stock. Available: '.$stock->quantity]);DB::transaction(function()use($stock,$data){$qty=(float)$data['quantity'];$before=(float)$stock->quantity;$after=$before+($data['type']==='in'?$qty:-$qty);$stock->update(['quantity'=>$after]);$stock->movements()->create(['movement_type'=>$data['type']==='out'?'adjustment_out':'adjustment_in','quantity'=>$qty,'rate'=>$data['rate']??0,'reference'=>'Manual adjustment','notes'=>$data['notes']??null,'user_email'=>session('user_email')?:auth()->user()?->email,'quantity_before'=>$before,'quantity_after'=>$after]);});return back()->with('success','Stock adjustment saved and recorded.');}
    public function history(VatStock $stock){abort_unless((int)$stock->firm_id===(int)session('vat_firm_id'),404);$movements=$stock->movements()->latest()->paginate(30);return view('vat-system.stock.history',compact('stock','movements'));}
    private function stockQuery(Request $request,int $firmId){$sort=$request->query('sort','newest')==='oldest'?'asc':'desc';return VatStock::where('firm_id',$firmId)->withExists(['movements as has_opening_stock'=>fn($q)=>$q->where('movement_type','opening_stock')])->when($request->filled('search'),fn($q)=>$q->where('item_name','like','%'.trim($request->query('search')).'%'))->when($request->query('status')==='out',fn($q)=>$q->where('quantity','<=',0))->when($request->query('status')==='low',fn($q)=>$q->where('reorder_level','>',0)->whereColumn('quantity','<=','reorder_level')->where('quantity','>',0))->orderBy('created_at',$sort)->orderBy('id',$sort);}
}
