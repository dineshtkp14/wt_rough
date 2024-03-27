<?php
namespace App\Http\Controllers;
use Livewire\WithPagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\customerinfo;
use Illuminate\Support\Facades\Auth;

class CustomerinfoController extends Controller
{
  

    public function index()
    {
        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'View All Customer',
            'link'=>'View All Customers'
        ];
   

     return view('customerinfo.list',['breadcrumb'=>$breadcrumb]);   

    }
    return redirect('/login');
}

    

    public function create()
    {
        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Add',
            'title'=>'Add New Customers',
            'link'=>'Add New Customers'
        ]; 
     
        return view('customerinfo.create',['breadcrumb'=>$breadcrumb]);   
    }

    return redirect('/login');
 }

 public function returncustomersforsalesitems()
{

    if(Auth::check()){
    $cus=customerinfo::orderBy('id','DESC')->get();
    return view('itemssales.create',['all'=>$cus]);   
  
}
return redirect('/login');
}

    public function store(Request $req)
   {
    if(Auth::check()){
    $validator=Validator::make($req->all(),[

        'name'=>'required',
        'address'=>'required',
        'phoneno' => 'required|unique:customerinfos,phoneno',
       
           
    ]);

    if($validator->passes()){

        $cusinfo=new customerinfo();
        $cusinfo->name=$req->name;
        $cusinfo->address=$req->address;
        $cusinfo->email=$req->email;
        $cusinfo->phoneno=$req->phoneno;
        $cusinfo->alternate_phoneno=$req->alternate_phoneno;

        $cusinfo->remarks=$req->remarks;
        $cusinfo->added_by = session('user_email');

        $cusinfo->save();

        return redirect()->route('customerinfos.index')->with('success','Customer Added Sucessfully !!');  
    }

    else{
        return redirect()->route('customerinfos.create')->withErrors($validator)->withInput();

    }

   }
   return redirect('/login');
}

   public function edit($id)

    {
        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Edit',
            'title'=>'Edit Customers Details',
            'link'=>'Edit Customers Details'
        ];
   
        $customers=customerinfo::findOrfail($id);

        return view('customerinfo.edit',['cus'=>$customers,'breadcrumb'=>$breadcrumb]);   
        
    }
    return redirect('/login');
 }
    public function update($id, Request $req)
    {
        if(Auth::check()){
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
            $cusinfo->added_by = session('user_email');

            $cusinfo->save();
    
            return redirect()->route('customerinfos.index')->with('success','Customer updated Sucessfully !!');  
        }
        else{
            return redirect()->route('customerinfos.create')->withErrors($validator)->withInput();
    
        }
    
        
    }
    return redirect('/login');
 }
    public function destroy($id,Request $req){


        $cusiddelete=customerinfo::findOrFail($id);
        $cusiddelete->delete();
  
        return redirect()->route('customerinfos.index')->with('success','Customer Deleted sucessfully'); 
        
  }

}
