<?php
namespace App\Http\Controllers;
use Livewire\WithPagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\customerinfo;

class CustomerinfoController extends Controller
{
  

    public function index()
    {
        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'View All Customerssss',
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
        'phoneno'=>'required', 
       
           
    ]);

    if($validator->passes()){

        $cusinfo=new customerinfo();
        $cusinfo->name=$req->name;
        $cusinfo->address=$req->address;
        $cusinfo->email=$req->email;
        $cusinfo->phoneno=$req->phoneno;
        $cusinfo->remarks=$req->remarks;
        $cusinfo->save();

        return redirect()->route('customerinfos.index')->with('success','Customer Added Sucessfully !!');  
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
            'phoneno'=>'required', 
           
               
        ]);
    
        if($validator->passes()){
    
            $cusinfo= customerinfo::find($id);
            $cusinfo->name=$req->name;
            $cusinfo->address=$req->address;
            $cusinfo->email=$req->email;
            $cusinfo->phoneno=$req->phoneno;
            $cusinfo->remarks=$req->remarks;
            $cusinfo->save();
    
            return redirect()->route('customerinfos.index')->with('success','Customer updated Sucessfully !!');  
        }
        else{
            return redirect()->route('customerinfos.create')->withErrors($validator)->withInput();
    
        }
    
        
    }

    public function destroy($id,Request $req){


        $cusiddelete=customerinfo::findOrFail($id);
        $cusiddelete->delete();
  
        return redirect()->route('customerinfos.index')->with('success','Customer Deleted sucessfully'); 
        
  }

}
