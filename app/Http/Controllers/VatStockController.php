<?php
namespace App\Http\Controllers;
use App\Models\VatStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class VatStockController extends Controller
{
    public function __construct(){ $this->middleware('auth'); }
    public function index(Request $request){$stocks=VatStock::query()->when($request->search,fn($q,$s)=>$q->where('item_name','like','%'.$s.'%'))->orderBy('item_name')->paginate(20)->withQueryString();if($request->expectsJson())return response()->json(['total'=>$stocks->total(),'items'=>$stocks->getCollection()->map(fn($s)=>['id'=>$s->id,'item_name'=>$s->item_name,'unit'=>$s->unit,'quantity'=>(float)$s->quantity,'purchase_rate'=>(float)$s->purchase_rate,'sale_rate'=>(float)$s->sale_rate,'reorder_level'=>(float)$s->reorder_level])]);return view('vat-system.stock.index',compact('stocks'));}
    public function adjust(Request $request,VatStock $stock){$data=$request->validate(['type'=>'required|in:in,out','quantity'=>'required|numeric|gt:0','rate'=>'nullable|numeric|min:0','notes'=>'nullable|string|max:500']);if($data['type']==='out'&&$stock->quantity<$data['quantity'])return back()->withErrors(['quantity'=>'Insufficient stock. Available: '.$stock->quantity]);DB::transaction(function()use($stock,$data){$qty=(float)$data['quantity'];$stock->decrement('quantity',$data['type']==='out'?$qty:-$qty);$stock->movements()->create(['movement_type'=>$data['type']==='out'?'adjustment_out':'adjustment_in','quantity'=>$qty,'rate'=>$data['rate']??0,'reference'=>'Manual adjustment','notes'=>$data['notes']??null]);});return back()->with('success','Stock adjusted successfully.');}
}
