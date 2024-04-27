<?php
namespace App\Http\Controllers;
use Livewire\WithPagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\customerinfo;
use App\Models\Trackcustomerinfos;

use Illuminate\Support\Facades\DB; //

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
        // 'phoneno' => 'required|unique:customerinfos,phoneno',
        'phoneno' => 'required|size:10|unique:customerinfos,phoneno',

           
    ]);

    if($validator->passes()){

        $cusinfo=new customerinfo();
        $cusinfo->name=strtoupper($req->name);
        $cusinfo->address=strtoupper($req->address);
        $cusinfo->email=$req->email;
        $cusinfo->phoneno=$req->phoneno;
        $cusinfo->alternate_phoneno=$req->alternate_phoneno;

        $cusinfo->remarks=$req->remarks;
        $cusinfo->added_by = session('user_email');

        $cusinfo->save();

        session()->put('lastInsertedId', $cusinfo->id);

        $notes = "Name: " . $cusinfo->name . ", Address: " . $cusinfo->address . ", Email: " . $cusinfo->email . ", Phoneno: " . $cusinfo->phoneno . ", Alternate Phoneno: " . $cusinfo->alternate_phoneno . ", Remarks: " . $cusinfo->remarks . ", Added by: " . session('user_email');

                    // Insert into track table
                    Trackcustomerinfos::create([
                        'title' => 'Insert',
                        'updated_by' => session('user_email'),
                        'notes' => $notes
                    ]);

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

            $oldCusinfo = customerinfo::find($id);

    
            $cusinfo= customerinfo::find($id);
            $cusinfo->name=strtoupper($req->name);
             $cusinfo->address=strtoupper($req->address);
            $cusinfo->email=$req->email;
            $cusinfo->phoneno=$req->phoneno;
            $cusinfo->remarks=$req->remarks;
            $cusinfo->added_by = session('user_email');

            $cusinfo->save();

                        
            // Construct a message with old and new values
            $notes = "Customer ID: " . $cusinfo->id . " updated by " . session('user_email') . ". Old values: ";
            $notes .= "Name: " . $oldCusinfo->name . ", Address: " . $oldCusinfo->address . ", Email: " . $oldCusinfo->email;
            $notes .= ", Phoneno: " . $oldCusinfo->phoneno . ", Remarks: " . $oldCusinfo->remarks;
            $notes .= ". New values: ";
            $notes .= "Name: " . $cusinfo->name . ", Address: " . $cusinfo->address . ", Email: " . $cusinfo->email;
            $notes .= ", Phoneno: " . $cusinfo->phoneno . ", Remarks: " . $cusinfo->remarks;

            // Insert into track table
         
                    // Insert into track table
                    trackcustomerinfos::create([
                'title' => "Update",
                'updated_by' => session('user_email'),
                'notes' => $notes
            ]);
    
            return redirect()->route('customerinfos.index')->with('success','Customer updated Sucessfully !!');  
        }
        else{
            // return redirect()->route('customerinfos.edit')->withErrors($validator)->withInput();
            return redirect()->route('customerinfos.edit', ['customerinfo' => $id])->withErrors($validator)->withInput();

    
        }
    
        
    }
    return redirect('/login');
 }
    public function destroy($id,Request $req){

                // Retrieve the customer information before deleting
                $cusinfo = customerinfo::findOrFail($id);
                // Delete the customer
                $cusinfo->delete();


                // Construct a message with the old values
                $notes = "Customer ID: " . $cusinfo->id . " deleted by " . session('user_email') . ". Values: ";
                $notes .= "Name: " . $cusinfo->name . ", Address: " . $cusinfo->address . ", Email: " . $cusinfo->email;
                $notes .= ", Phoneno: " . $cusinfo->phoneno . ", Remarks: " . $cusinfo->remarks;

                // Insert into track table
              
                    // Insert into track table
                    trackcustomerinfos::create([
                    'title' => "Delete",
                    'updated_by' => session('user_email'),
                    'notes' => $notes
                ]);


  
        return redirect()->route('customerinfos.index')->with('success','Customer Deleted sucessfully'); 
        
  }

}
