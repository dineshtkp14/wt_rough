<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\invoice;
use App\Models\customerinfo;
use App\Models\item;
use App\Models\salesitem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Invoicecontroller extends Controller
{
    private function canEditInvoice(invoice $invoice): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        if ($user->email === 'dineshtkp14@gmail.com') {
            return true;
        }

        return $invoice->created_at && $invoice->created_at->gte(now()->subMinute());
    }

    public function index()
    {
        if(Auth::check()){

            $breadcrumb= [
                'subtitle'=>'View',
                'title'=>'View Invoices',
                'link'=>'View Invoices'
            ];
       
         $alldata=invoice::orderBy('id','DESC')->get();
       

         return view('invoice.list',['all'=>$alldata,'breadcrumb'=>$breadcrumb]);

       
    }

    return redirect('/login');
}
    public function create()
    {
        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Create',
            'title'=>'Invoice',
            'link'=>'Invoice'
        ];

        return view('itemssales.create',['breadcrumb'=>$breadcrumb]);
    }
    return redirect('/login');
}

    public function store()
    {
        if(Auth::check()){

        return view('itemssales.create');
    }

    return redirect('/login');
 }








    public function edit($id)

{
    if(Auth::check()){
    $breadcrumb= [
        'subtitle'=>'Edit',
        'title'=>'Edit Invoice Details',
        'link'=>'Edit Invoice Details'
    ];

    $invoices=invoice::findOrfail($id);

    if (!$this->canEditInvoice($invoices)) {
        return redirect()->route('onlyviewbillafterbill', ['invoiceid' => $invoices->id])
            ->with('error', 'Invoice edit is allowed only within 1 minute after creation.');
    }

    $customer = customerinfo::find($invoices->customerid);
    $ledger = DB::table('customerledgerdetails')->where('invoiceid', $invoices->id)->first();
    $salesRows = salesitem::where('invoiceid', $invoices->id)->orderBy('id')->get();
    $editRows = $salesRows->map(function ($row) {
        $itemInfo = $row->itemid ? item::select('id', 'itemsname', 'quantity')->find($row->itemid) : null;
        $currentQuantity = (float) ($row->quantity ?? 0);

        return [
            'product' => $row->itemid ? (string) $row->itemid : '',
            'item_name' => $itemInfo ? $itemInfo->itemsname : '',
            'unstocked' => $row->itemid ? '' : ($row->unstockedname ?? ''),
            'quantity' => (string) $row->quantity,
            'unit' => $row->unit ?? '',
            'price' => (string) $row->price,
            'subtotal' => (string) $row->subtotal,
            'max_quantity' => $itemInfo ? ((float) $itemInfo->quantity + $currentQuantity) : null,
        ];
    })->values();

    $editData = [
        'customer' => (string) $invoices->customerid,
        'customer_name' => $customer ? $customer->name : '',
        'customer_address' => $customer ? $customer->address : '',
        'customer_phone' => $customer ? trim(($customer->phoneno ?? '') . (!empty($customer->alternate_phoneno) ? ', ' . $customer->alternate_phoneno : '')) : '',
        'invoice_type' => $invoices->inv_type,
        'date' => $invoices->inv_date,
        'subtotal' => (string) $invoices->subtotal,
        'discount' => (string) $invoices->discount,
        'total' => (string) $invoices->total,
        'note' => $invoices->notes ?? '',
        'credit_days' => $ledger ? (string) ($ledger->credit_limit_days ?? '') : '',
        'rows' => $editRows,
    ];

    return view('invoice.edit',['invoice'=>$invoices,'breadcrumb'=>$breadcrumb,'editData'=>$editData]);   
    
    return redirect('/login');
}
}

public function update($id, Request $req)
{
    if (Auth::check()) {
        $validator = Validator::make($req->all(), [
            'sales_arr' => 'required',
            'final_arr' => 'required',
            'invoice_type' => 'required|in:cash,credit',
            'date' => 'required|date',
            'credit_days' => 'required_if:invoice_type,credit|nullable|integer|min:1',
        ]);

        if ($validator->passes()) {
            $salesArr = json_decode($req->sales_arr);
            $finalArr = json_decode($req->final_arr);

            if (!is_array($salesArr) || empty($salesArr) || !is_array($finalArr) || empty($finalArr)) {
                return redirect()->route('invoice.edit', $id)->with('error', 'Please verify invoice details before saving.');
            }

            $existingInvoice = invoice::findOrFail($id);
            if (!$this->canEditInvoice($existingInvoice)) {
                return redirect()->route('onlyviewbillafterbill', ['invoiceid' => $existingInvoice->id])
                    ->with('error', 'Invoice edit is allowed only within 1 minute after creation.');
            }

            $invoice = DB::transaction(function () use ($id, $req, $salesArr, $finalArr) {
                $invoice = invoice::findOrFail($id);
                $final = $finalArr[0];

                $oldSalesRows = salesitem::where('invoiceid', $invoice->id)->get();
                foreach ($oldSalesRows as $oldRow) {
                    if ($oldRow->itemid) {
                        DB::table('items')->where('id', $oldRow->itemid)->increment('quantity', $oldRow->quantity);
                    }
                }

                salesitem::where('invoiceid', $invoice->id)->delete();

                $invoice->customerid = $final->customer;
                $invoice->subtotal = $final->subtotal;
                $invoice->discount = $final->discount == "" ? 0.00 : $final->discount;
                $invoice->total = $final->total;
                $invoice->notes = $final->note ?? $req->notes;
                $invoice->inv_type = $req->invoice_type;
                $invoice->inv_date = $req->date;
                $invoice->added_by = Auth::user()->name;
                $invoice->save();

                foreach ($salesArr as $value) {
                    $data = new salesitem();
                    $data->invoiceid = $invoice->id;
                    $data->itemid = $value->product == "" ? null : $value->product;
                    $data->unstockedname = $value->unstocked;
                    $data->date = $req->date;
                    $data->quantity = $value->quantity;
                    $data->unit = $value->unit;
                    $data->price = $value->price;
                    $data->subtotal = $value->subtotal;
                    $data->added_by = session('user_email');
                    $data->save();

                    if ($data->itemid) {
                        DB::table('items')->where('id', $data->itemid)->decrement('quantity', $value->quantity);
                    }
                }

                DB::table('customerledgerdetails')
                    ->where('invoiceid', $invoice->id)
                    ->update([
                        'customerid' => $invoice->customerid,
                        'date' => $invoice->inv_date,
                        'invoicetype' => $invoice->inv_type,
                        'debit' => $invoice->total,
                        'credit_limit_days' => $req->invoice_type === 'credit' ? $req->credit_days : null,
                        'updated_at' => now(),
                    ]);

                return $invoice;
            });

            return redirect()->route('onlyviewbillafterbill', ['invoiceid' => $invoice->id])
                ->with('success', 'Invoice Updated Successfully!');
        } else {
            return redirect()->route('invoice.edit', $id)->withErrors($validator)->withInput();
        }
    }

    return redirect('/login');
}

}
