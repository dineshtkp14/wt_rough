<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\purchaseorder; // Add this line
use Illuminate\Support\Facades\DB; // Add this line
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class Purchse_controller extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $breadcrumb = [
                'subtitle' => 'View',
                'title' => 'View Make Orders',
                'link' => 'View Make Orders'
            ];

            $purchaseOrders = PurchaseOrder::all();

            return view('purchase_order.list', ['breadcrumb' => $breadcrumb, 'purchaseOrders' => $purchaseOrders]);
        }

        return redirect('/login');
    }

    // Method to show the form for creating a new purchase order
    public function create()
    {
        if (Auth::check()) {
            $breadcrumb = [
                'subtitle' => 'Add',
                'title' => 'Add New make Order',
                'link' => 'Add New make Order'
            ];

            return view('purchase_order.create', ['breadcrumb' => $breadcrumb]);
        }

        return redirect('/login');
    }

    // Method to store the form data
    public function store(Request $request)
    {
       
        $validator=Validator::make($request->all(),[

            'date' => 'required|date',
            'orderlist' => 'required|string',
            'notes' => 'nullable|string', 
               
        ]);

        if($validator->passes()){
        // Create a new PurchaseOrder instance with the validated data
        $purchaseOrder = new PurchaseOrder();
        $purchaseOrder->date = $request->date;
        $purchaseOrder->orderlist = $request->orderlist;
        $purchaseOrder->notes = $request->notes;
        // Set other fields accordingly

        // Save the purchase order to the database
        $purchaseOrder->save();

        // Redirect the user back or to a specific route
        return redirect()->route('purorder.index')->with('success', 'Purchase order created successfully!');
    }
    else{
        return redirect()->back()->withErrors($validator)->withInput();

    }
}

    // Method to show the form for editing a purchase order
    public function edit($id)
    {
        if (Auth::check()) {
            $breadcrumb = [
                'subtitle' => 'Edit',
                'title' => 'Edit make Order',
                'link' => 'Edit make Order'
            ];

            $purchaseOrder = PurchaseOrder::findOrFail($id);

            return view('purchase_order.edit', ['breadcrumb' => $breadcrumb, 'purchaseOrder' => $purchaseOrder]);
        }

        return redirect('/login');
    }

    // Method to update the purchase order in the database
    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'orderlist' => 'required|string',
            'notes' => 'nullable|string',
            // Add other validation rules for existing fields here
        ]);

      
        
    if($validator->passes()){
        // Proceed with updating the data
        $purchaseOrder = PurchaseOrder::find($id);
        $purchaseOrder->date = $request->date;
        $purchaseOrder->orderlist = $request->orderlist;
        $purchaseOrder->notes = $request->notes;
        $purchaseOrder->save();

        // Redirect the user back or to a specific route
        return redirect()->route('purorder.index')->with('success', 'Purchase order updated successfully!');
    }
    else{
        return redirect()->back()->withErrors($validator)->withInput();

    }

}

    // Method to delete a purchase order
    public function destroy($id)
    {
        // Find the purchase order by id and delete it
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $purchaseOrder->delete();

        // Redirect the user back or to a specific route
        return redirect()->route('purorder.index')->with('success', 'Purchase order deleted successfully!');
    }


}
