<?php
namespace App\Http\Controllers;
use Livewire\WithPagination;




use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\customerinfo;




class CustomerinfoController extends Controller
{
    use WithPagination;
    public $search = '';

    public function index()
    {
        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'View All Customers',
            'link'=>'View All Customers'
        ];
   

     return view('customerinfo.list',['breadcrumb'=>$breadcrumb]);   

    }

    
    


    public function create()
    {

        $breadcrumb= [
            'subtitle'=>'Add',
            'title'=>'Add New Customers',
            'link'=>'Add New Customers'
        ];
   
        
     
        return view('customerinfo.create',['breadcrumb'=>$breadcrumb]);   
    }


 public function returncustomersforsalesitems()
{
    $cus=customerinfo::orderBy('id','DESC')->get();
       

    return view('itemssales.create',['all'=>$cus]);   
  
}

    public function store(Request $req)
   {
    $validator=Validator::make($req->all(),[

        'name'=>'required',
        'address'=>'required',
        'email'=>'required|email',
       
        'phoneno'=>'required|numeric', 
       
           
    ]);

    if($validator->passes()){

        $disinfoobj=new customerinfo();
        $disinfoobj->name=$req->name;
        $disinfoobj->address=$req->address;
        $disinfoobj->email=$req->email;
        $disinfoobj->phoneno=$req->phoneno;
       
        
        $disinfoobj->remarks=$req->remarks;
       
        $disinfoobj->save();

      

        return redirect()->route('customerinfos.create')->with('success','Items Added Sucessfully !!');  
    }
    else{
        return redirect()->route('customerinfos.create')->withErrors($validator)->withInput();

    }

   }
   
   public function edit($id)

    {
        $breadcrumb= [
            'subtitle'=>'Edit',
            'title'=>'Edit Customers Details',
            'link'=>'Edit Customers Details'
        ];
   
        $customers=customerinfo::findOrfail($id);

        return view('customerinfo.edit',['cus'=>$customers,'breadcrumb'=>$breadcrumb]);   
        
    }

    public function update($id, Request $req)
    {
        $validator=Validator::make($req->all(),[

            'name'=>'required',
            'address'=>'required',
            'email'=>'required',
          
          
            'phoneno'=>'required|numeric', 
           
               
        ]);
    
        if($validator->passes()){
    
          
            $disinfoobj= customerinfo::find($id);
            $disinfoobj->name=$req->name;
            $disinfoobj->address=$req->address;
            $disinfoobj->email=$req->email;
            $disinfoobj->phoneno=$req->phoneno;
           
            
            $disinfoobj->remarks=$req->remarks;
           
            $disinfoobj->save();
    
          
    
            return redirect()->route('customerinfos.create')->with('success','Items updated Sucessfully !!');  
        }
        else{
            return redirect()->route('customerinfos.create')->withErrors($validator)->withInput();
    
        }
    
        
    }

    public function destroy($id,Request $req){

        $cusiddelete=customerinfo::findOrFail($id);
       
  
        $cusiddelete->delete();
  
        return redirect()->route('customerinfos.index')->with('success','Deleted sucessfully'); 
        
  
  }

}
