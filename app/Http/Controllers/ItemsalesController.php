<?php

namespace App\Http\Controllers;

use App\Models\company;
use App\Models\customerinfo;
use App\Models\customerledgerdetails;
use App\Models\invoice;
use App\Models\item;
use App\Models\SmsLog;
use App\Services\SmsService;
use App\Helpers\InvoiceSmsHelper;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\salesitem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItemsalesController extends Controller
{
    
    public function index()
    {
        if(Auth::check()){
        $breadcrumb = [
            'subtitle' => 'View',
            'title' => 'View Invoice Sales Details',
            'link' => 'View Invoice Sales Details'
        ];
        $cus = salesitem::orderBy('id', 'DESC')->paginate(20); 
        return view('itemssales.list', compact('cus', 'breadcrumb'));
    }
    return redirect('/login');
}  



    public function create()
    {
        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'',
            'title'=>'Invoice',
            'link'=>''
        ];

     
        $cus = customerinfo::all();
        $statement  = DB::select("SHOW TABLE STATUS LIKE 'invoices'");
        $nextUserId = $statement[0]->Auto_increment;
        $breadcrumb['title'] = 'Invoice No: ' . $nextUserId;

        $itemsdata = item::all();
        return view('itemssales.create', ['page' => 'isc', 'all' => $cus, 'data' => $itemsdata,'nextgenid' => $nextUserId,'breadcrumb'=>$breadcrumb]);
    }

    return redirect('/login');
 }
    public function store(Request $req)
    {
        if(Auth::check()){
           
        $sales_arr = json_decode($req->sales_arr); //rowdetails
        $final_arr = json_decode($req->final_arr); //finaltotalinvoice
        // invoice insert
        $invoice_data = new invoice();
        $invoice_data->customerid = $final_arr[0]->customer;
        // $invoice_data->paidamount = null;
        // $invoice_data->dueamount = $final_arr[0]->total;
        $invoice_data->subtotal = $final_arr[0]->subtotal;
        $invoice_data->discount = $final_arr[0]->discount == "" ? 0.00 : $final_arr[0]->discount;
        $invoice_data->total = $final_arr[0]->total;
        $invoice_data->notes = $final_arr[0]->note;
        $invoice_data->inv_type = $req->invoice_type;
        $invoice_data->inv_date = $req->date;


        // $invoice_data->added_by = session('user_email');
        $invoice_data->added_by =   Auth::user()->name ;
     



        //dd( $invoice_data->notes);
        $invoice_data->save();

        // sales insert
        foreach ($sales_arr as $value) {
            $data = new salesitem();
            $data->invoiceid = $invoice_data->id;
            $data->itemid = $value->product == "" ? null : $value->product;

            $data->unstockedname = $value->unstocked;
            $data->date = $req->date;

            $data->quantity = $value->quantity;
            $data->unit = $value->unit;

            if ($data->itemid) {
              

                DB::table('items')->where('id', $data->itemid)->decrement('quantity', $value->quantity);


            }

            $data->price = $value->price;
            // $data->discount = $value->discount == "" ? 0.00 : $value->discount;
            $data->subtotal = $value->subtotal;
            $data->added_by = session('user_email');

            $data->save();
        }

        $cus_data = new customerledgerdetails();
        $cus_data->customerid = $final_arr[0]->customer;
        $cus_data->invoiceid = $invoice_data->id;
        $cus_data->date = $req->date;
        $cus_data->particulars  = "Goods Sales";
        $cus_data->voucher_type = "sales";
        $cus_data->invoicetype = $req->invoice_type;
        $cus_data->debit =  $final_arr[0]->total;
        $cus_data->credit_limit_days = $req->credit_days;
        $cus_data->added_by = session('user_email');

        $cus_data->save();
        

        $customer = customerinfo::find($invoice_data->customerid);
        $phone = preg_replace('/\D+/', '', $customer->phoneno ?? '');

        if (strlen($phone) === 10) {
            $phone = '977' . $phone;
        }

        $redirect = redirect()->route('onlyviewbillafterbill', ['invoiceid' => $invoice_data->id])
            ->with('success', 'Invoice Created Successfully !!');

        if ($invoice_data->inv_type === 'credit') {
            // Calculate customer's total due amount from ledger
            $totalDueAmount = customerledgerdetails::where('customerid', $invoice_data->customerid)
                ->where('created_at', '<=', now())
                ->sum(DB::raw('COALESCE(debit, 0) - COALESCE(credit, 0)'));

            // Create SMS message with invoice details and total due amount
            $invoiceMessage = 'Namaste ' . ($customer->name ?? 'Customer')
                . ', your invoice no ' . $invoice_data->id
                . ' has been created. Invoice Amount: Rs ' . number_format((float) $invoice_data->total, 2)
                . '. Your total due till today: Rs ' . number_format($totalDueAmount, 2)
                . '. Thank you!';

            // Truncate message to SMS character limit
            $invoiceMessage = InvoiceSmsHelper::truncateMessage($invoiceMessage);

            // Auto-send SMS if customer has phone number
            if ($phone && $customer) {
                try {
                    $smsService = new SmsService();
                    $smsResponse = $smsService->send($phone, $invoiceMessage);

                    // Log SMS sent
                    $smsLog = SmsLog::create([
                        'invoice_id' => $invoice_data->id,
                        'customer_id' => $invoice_data->customerid,
                        'phone_number' => $phone,
                        'message' => $invoiceMessage,
                        'sms_type' => 'invoice_created',
                        'status' => $smsResponse['success'] ? 'sent' : 'failed',
                        'api_response' => json_encode($smsResponse),
                    ]);

                    if ($smsResponse['success']) {
                        $smsLog->markAsSent(json_encode($smsResponse['data']));
                        Log::info('Invoice SMS auto-sent', [
                            'invoice_id' => $invoice_data->id,
                            'customer' => $customer->name,
                            'phone' => $phone
                        ]);
                    } else {
                        Log::error('Invoice SMS auto-send failed', [
                            'invoice_id' => $invoice_data->id,
                            'customer' => $customer->name,
                            'phone' => $phone,
                            'status' => $smsResponse['status'] ?? null,
                            'response' => $smsResponse['body'] ?? $smsResponse['error'] ?? $smsResponse['data'] ?? null,
                        ]);
                    }

                } catch (\Exception $e) {
                    Log::error('Failed to auto-send invoice SMS', [
                        'invoice_id' => $invoice_data->id,
                        'customer' => $customer->name,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $redirect->with('invoice_whatsapp_message', $invoiceMessage);

            if ($phone) {
                $redirect->with('invoice_whatsapp_url', 'https://wa.me/' . $phone . '?text=' . rawurlencode($invoiceMessage));
            }
        }

        return $redirect;
                                
       

    }

    return redirect('/login');
 }

 public function oldPriceSearch(Request $req)
 {
     if (!Auth::check()) {
         return response()->json([], 401);
     }

     $customerId = $req->query('customerid');
     $customerName = trim($req->query('customer_name', ''));
     $search = trim($req->query('search', ''));

     if (empty($customerId) && $customerName !== '') {
         $customerId = customerinfo::where('name', 'like', '%' . $customerName . '%')->value('id');
     }

     if (empty($customerId) || $search === '') {
         return response()->json([]);
     }

     $terms = collect(preg_split('/\s+/', $search))
         ->filter()
         ->values();

     $results = salesitem::query()
         ->from('salesitems as s')
         ->leftJoin('items as it', 'it.id', '=', 's.itemid')
         ->leftJoin('invoices as inv', 'inv.id', '=', 's.invoiceid')
         ->where('inv.customerid', $customerId)
         ->where(function ($query) use ($terms) {
             foreach ($terms as $term) {
                 $like = '%' . $term . '%';
                 $query->where(function ($subQuery) use ($like) {
                     $subQuery->where('s.unstockedname', 'like', $like)
                         ->orWhere('it.itemsname', 'like', $like);
                 });
             }
         })
         ->orderByDesc('s.date')
         ->orderByDesc('s.id')
         ->limit(8)
         ->get([
             's.invoiceid',
             's.date',
             's.unstockedname',
             's.quantity',
             's.unit',
             's.price',
             's.subtotal',
             'it.itemsname',
         ])
         ->map(function ($row) {
             return [
                 'invoiceid' => $row->invoiceid,
                 'date' => $row->date,
                 'item_name' => $row->itemsname ?: $row->unstockedname,
                 'quantity' => $row->quantity,
                 'unit' => $row->unit,
                 'price' => $row->price,
                 'subtotal' => $row->subtotal,
             ];
         });

     return response()->json($results);
 }



 public function edit($id)
 {
     if (Auth::check()) {
         $breadcrumb = [
             'subtitle' => 'Edit',
             'title' => 'Edit SalesItem Details',
             'link' => 'Edit SalesItem Details'
         ];
 
         $all = salesitem::findOrFail($id);
 
         return view('itemssales.edit', ['all' => $all, 'breadcrumb' => $breadcrumb]);
 
         return redirect('/login');
     }
 }
 
 public function update($id, Request $req)
 {
     if (Auth::check()) {
         $validator = Validator::make($req->all(), [
             'itemid' => 'required',
             'unstockedname' => 'required',
             'quantity' => 'required',
             'price' => 'required',
             'discount' => 'required',
             'subtotal' => 'required',
             // Add more validation rules as needed
         ]);
 
         if ($validator->passes()) {
             $item = salesitem::find($id);
             $item->itemid = $req->itemid;
             $item->unstockedname = $req->unstockedname;
             $item->quantity = $req->quantity;
             $item->price = $req->price;
             $data->unit = $value->unit;

            //  $item->discount = $req->discount;
             $item->subtotal = $req->subtotal;
             // Update other fields as needed
             $item->added_by = session('user_email');

 
             $item->save();
 
             return redirect()->route('itemsales.index')->with('success', 'ItemSales Updated Successfully!');
         } else {
             return redirect()->route('itemsales.edit', $id)->withErrors($validator)->withInput();
         }
     }
 
     return redirect('/login');
 }
 







    
}
