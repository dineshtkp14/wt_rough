<?php

namespace App\Http\Controllers;

use App\Models\company;
use App\Models\TrackCustomerLedger;

use App\Models\customerledgerdetails;

use Illuminate\Support\Facades\DB; //

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\customerinfo;
use App\Models\invoice;
use Illuminate\Support\Facades\Auth;
use App\Models\CreditnotesInvoice;
use App\Services\CustomerSmsNotifier;
use App\Support\NepaliDate;




class CustomerLedgerDetailsController extends Controller

{
    public function index(Request $req)
    {

        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Payments',
            'title'=>'All Customers Payment Histrory',
            'link'=>'All Customers Payment Histrory'
        ];
       
 // $cus = customerledgerdetails::where('invoicetype', 'payment')
        // ->orderBy('id', 'DESC');
        // $cus=customerledgerdetails::orderBy('id','DESC')->get(); 
        // foreach($cus as $data){
        //     if($data->customerid){
        //         $cus_name = customerinfo::where('id', $data->customerid)->select('name')->first();
        //         if ($cus_name) {
        //             $data->customerid = $cus_name->name;
        //         } else {
        //             $data->customerid = 'Unknown'; // or any default value
        //         }
        //     }
        }
         return view('customerdetails.list',['breadcrumb'=>$breadcrumb]);   
    
}

    public function create()
    
    {
        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Payments',
            'title'=>' Customers Ledger Payment',
            'link'=>'Customers Ledger Payment'
        ];
        $cus=customerinfo::orderBy('id','DESC')->get();

        $statement  = DB::select("SHOW TABLE STATUS LIKE 'customerledgerdetails'");
        $nextUserId = $statement[0]->Auto_increment;       
        return view('customerdetails.create',['all'=>$cus,'breadcrumb'=>$breadcrumb,'nextUserId'=>$nextUserId]);   ;
    }
    return redirect('/login');
}

public function store(Request $req)
{
    // Validate the input data
    $validator = Validator::make($req->all(), [
        'customerid' => 'required',
        'date' => 'required',
        'amount' => 'required',
        'is_cheque' => 'nullable|boolean',
        'cheque_bank' => 'required_if:is_cheque,1|nullable|string|max:150',
        'cheque_no' => 'required_if:is_cheque,1|nullable|string|max:50',
        'cheque_exchange_date_bs' => 'required_if:is_cheque,1|nullable|regex:/^\\d{4}-\\d{1,2}-\\d{1,2}$/',
        'notes' => 'nullable|string|max:60',
        'invoiceid' => 'nullable|integer',
        'invoiceids' => 'nullable|string',
        'particulars' => 'required_without_all:disableFields,is_cheque',
        'vt' => 'required_without_all:disableFields,is_cheque',
        // 'cninvoiceid' => 'required_without:disableFields', // Only required if disableFields is not present
    ]);

    if ($validator->fails()) {
        return redirect()->route('cpayments.create')->withErrors($validator)->withInput();
    }

    if ($req->filled('invoiceid')) {
        $invoice = invoice::where('id', $req->invoiceid)
            ->where('customerid', $req->customerid)
            ->first();

        if (!$invoice) {
            return redirect()->route('cpayments.create')
                ->with('error', 'The selected invoice was not found for this customer.')
                ->withInput();
        }

        $invoiceAmount = (float) customerledgerdetails::where('customerid', $req->customerid)
            ->where('invoiceid', $req->invoiceid)
            ->where('invoicetype', 'credit')
            ->sum('debit');
        $paidAmount = (float) customerledgerdetails::where('customerid', $req->customerid)
            ->where('invoiceid', $req->invoiceid)
            ->where('invoicetype', 'payment')
            ->sum('credit');
        $remainingAmount = max(0, round($invoiceAmount - $paidAmount, 2));

        if ((float) $req->amount > $remainingAmount + 0.01) {
            return redirect()->route('cpayments.create')
                ->with('error', 'Payment cannot be greater than the remaining invoice balance of Rs. ' . number_format($remainingAmount, 2) . '.')
                ->withInput();
        }
    }

    $invoiceIds = collect(explode(',', (string) $req->input('invoiceids')))
        ->map(fn ($id) => trim($id))
        ->filter(fn ($id) => ctype_digit($id))
        ->unique()
        ->values();

    if ($invoiceIds->isNotEmpty()) {
        $validInvoiceCount = invoice::where('customerid', $req->customerid)
            ->whereIn('id', $invoiceIds->all())
            ->count();

        if ($validInvoiceCount !== $invoiceIds->count()) {
            return redirect()->route('cpayments.create')
                ->with('error', 'One or more selected invoices were not found for this customer.')
                ->withInput();
        }
    }

    if ($req->has('disableFields')) { // Replace 'your_checkbox_name' with the name of your checkbox input
        // Check if cninvoiceid and customerid exist in your database
        if (empty($req->cninvoiceid)) {
            return redirect()->route('cpayments.create')->with('error', 'Please enter a value for Credit Notes Invoice ID.');
        }
       
        $cnInvoice = CreditNotesInvoice::where([
            ['id', $req->cninvoiceid],
            ['customerid', $req->customerid]
        ])->first();


        if (!$cnInvoice) {
            return redirect()->route('cpayments.create')->with('error', 'Credit Notes Invoice ID not found.');
        }

        // Validate the amount
        if ($req->amount != $cnInvoice->total) {
            return redirect()->route('cpayments.create')->with('error', 'Amount does not match the amount in the Credit Notes Invoice.');
        }

        // Check if cnvoiceid already exists in customerledgerdetails table
        $existingCnvoiceId = customerledgerdetails::where('cninvoiceid', $req->cninvoiceid)->exists();
        if ($existingCnvoiceId) {
            return redirect()->route('cpayments.create')->with('error', 'This Credit Notes Invoice ID has already been inserted.');
        }
    }

    $nextUserId = DB::select("SHOW TABLE STATUS LIKE 'customerledgerdetails'")[0]->Auto_increment;

    $chequeExchangeDate = $this->chequeExchangeDate($req);
    $payment = null;

    DB::transaction(function () use ($req, $chequeExchangeDate, &$payment) {
        $cl = new customerledgerdetails();
        $cl->customerid = $req->customerid;
        $cl->date = $req->date;
        $cl->invoiceid = $req->invoiceid ?? null;
        $cl->invoiceids = $req->invoiceids ?? null;
        $cl->particulars = $req->has('disableFields') ? "salesreturn" : ($req->particulars ?: ($req->boolean('is_cheque') ? 'CHEQUE DEPOSIT - '.$req->input('cheque_bank') : ''));
        $cl->voucher_type = $req->has('disableFields') ? "return" : ($req->vt ?: ($req->boolean('is_cheque') ? 'CHEQUE DEPOSIT' : ''));
        $cl->cninvoiceid = $req->cninvoiceid ?? null;
        $cl->invoicetype = "payment";
        $cl->credit = $req->amount;
        $cl->notes = $req->notes;
        $cl->is_cheque = $req->boolean('is_cheque');
        $cl->cheque_bank = $req->input('cheque_bank');
        $cl->cheque_no = $req->input('cheque_no');
        $cl->cheque_exchange_date = $chequeExchangeDate;
        $cl->added_by = session('user_email');
        $cl->save();
        $payment = $cl;

        if ($req->has('nilaccount') && !$req->has('disableFields')) {
            $settlement = new customerledgerdetails();
            $settlement->customerid = $req->customerid;
            $settlement->date = $req->date;
            $settlement->particulars = 'NIL ACCOUNT / ACCOUNT SETTLED';
            $settlement->voucher_type = 'SETTLEMENT';
            $settlement->invoicetype = 'settlement';
            $settlement->debit = 0;
            $settlement->credit = 0;
            $settlement->notes = trim(($req->notes ?? '') . ' Settlement marker after payment receipt CR-(' . $cl->id . ') for amount ' . $req->amount . '.');
            $settlement->added_by = session('user_email');
            $settlement->save();
        }

        $notes = 'Customer ID ' . $req->customerid . ' inserted with particulars: ' . $cl->particulars . ', voucher type: ' . $cl->voucher_type . ', credit: ' . $cl->credit . ', date: ' . $cl->date . ', by ' . session('user_email');

        TrackCustomerLedger::create([
            'title' => $req->has('nilaccount') && !$req->has('disableFields') ? 'Inserted_Payment_With_Nil_Account' : 'Inserted_Payment',
            'updated_by' => session('user_email'),
            'notes' => $notes
        ]);
    });

    $smsStatus = null;
    $smsMessage = null;
    $smsSentText = null;

    if ($payment && !$req->has('disableFields')) {
        $customer = customerinfo::find($payment->customerid);
        $smsResponse = (new CustomerSmsNotifier())->paymentCreated($payment, $customer);

        if ($smsResponse === null) {
            $smsStatus = 'warning';
            $smsMessage = 'Payment saved, but SMS was not sent because customer phone number is missing.';
        } elseif ($smsResponse['success'] ?? false) {
            $smsStatus = 'success';
            $smsMessage = 'Payment saved and SMS sent successfully to ' . ($customer->phoneno ?? 'customer') . '.';
        } else {
            $smsError = $smsResponse['error'] ?? $smsResponse['body'] ?? 'Unknown error';
            $smsMessage = str_contains(strtolower($smsError), 'empty queue')
                ? 'Payment saved, but SMS failed because the mobile number is not correct. Please check customer mobile number.'
                : 'Payment saved, but SMS failed: ' . $smsError;
            $smsStatus = 'danger';
        }

        if (is_array($smsResponse)) {
            $smsSentText = $smsResponse['message'] ?? null;
        }
    }

    $redirect = redirect()
        ->route('cashreceipt.search', ['receiptno' => $nextUserId])
        ->with('success', 'Invoice Created Successfully !!');

    if ($smsStatus && $smsMessage) {
        $redirect
            ->with('payment_sms_status', $smsStatus)
            ->with('payment_sms_message', $smsMessage);

        if ($smsSentText) {
            $redirect->with('payment_sms_sent_text', $smsSentText);
        }
    }

    return $redirect;
}

public function edit($id)

{
    if(Auth::check()){
    $breadcrumb= [
        'subtitle'=>'Edit',
        'title'=>'Edit Customer Payement',
        'link'=>'Edit Customers Payment'
    ];

    $distrinutors=customerledgerdetails::findOrfail($id);

    return view('customerdetails.edit',['payment'=>$distrinutors,'breadcrumb'=>$breadcrumb]);   
    
}
}
public function update(Request $req, $id)
{
    // Validate the input data
    $validator = Validator::make($req->all(), [
        'customerid' => 'required',
        'date' => 'required',
        'amount' => 'required',
        'is_cheque' => 'nullable|boolean',
        'cheque_bank' => 'required_if:is_cheque,1|nullable|string|max:150',
        'cheque_no' => 'required_if:is_cheque,1|nullable|string|max:50',
        'cheque_exchange_date_bs' => 'required_if:is_cheque,1|nullable|regex:/^\\d{4}-\\d{1,2}-\\d{1,2}$/',
        'notes' => 'nullable|string|max:60',
        'particulars' => 'required_without_all:disableFields,is_cheque',
        'vt' => 'required_without_all:disableFields,is_cheque',
        // 'cninvoiceid' => 'required_without:disableFields', // Only required if disableFields is not present
    ]);

    if ($validator->fails()) {
        return redirect()->route('cpayments.edit', $id)->withErrors($validator)->withInput();
    }

    if ($req->has('disableFields')) { // Replace 'your_checkbox_name' with the name of your checkbox input
        // Check if cninvoiceid and customerid exist in your database
        if (empty($req->cninvoiceid)) {
            return redirect()->route('cpayments.edit', $id)->with('error', 'Please enter a value for Credit Notes Invoice ID.');
        }
       
        $cnInvoice = CreditNotesInvoice::where([
            ['id', $req->cninvoiceid],
            ['customerid', $req->customerid]
        ])->first();


        if (!$cnInvoice) {
            return redirect()->route('cpayments.edit', $id)->with('error', 'Credit Notes Invoice ID not found.');
        }

        // Validate the amount
        if ($req->amount != $cnInvoice->total) {
            return redirect()->route('cpayments.edit', $id)->with('error', 'Amount does not match the amount in the Credit Notes Invoice.');
        }

        // Check if cnvoiceid already exists in customerledgerdetails table
        $existingCnvoiceId = customerledgerdetails::where('cninvoiceid', $req->cninvoiceid)->where('id', '!=', $id)->exists();
        if ($existingCnvoiceId) {
            return redirect()->route('cpayments.edit', $id)->with('error', 'This Credit Notes Invoice ID has already been inserted.');
        }
    }

    $chequeExchangeDate = $this->chequeExchangeDate($req);

    // Retrieve the record to be updated
    $cl = customerledgerdetails::findOrFail($id);
    
    // Update record fields
    $cl->customerid = $req->customerid;
    $cl->date = $req->date;
    $cl->particulars = $req->has('disableFields') ? "salesreturn" : ($req->particulars ?: ($req->boolean('is_cheque') ? 'CHEQUE DEPOSIT - '.$req->input('cheque_bank') : ''));
    $cl->voucher_type = $req->has('disableFields') ? "return" : ($req->vt ?: ($req->boolean('is_cheque') ? 'CHEQUE DEPOSIT' : ''));
    $cl->cninvoiceid = $req->cninvoiceid ?? null;
    $cl->credit = $req->amount;
    $cl->notes = $req->notes;
    $cl->is_cheque = $req->boolean('is_cheque');
    $cl->cheque_bank = $req->input('cheque_bank');
    $cl->cheque_no = $req->input('cheque_no');
    $cl->cheque_exchange_date = $chequeExchangeDate;
    $cl->added_by = session('user_email');
    $cl->save();
    dd("success");

    return redirect()->route('cashreceipt.search', ['receiptno' => $id])->with('success', 'Invoice Updated Successfully !!');
}

    public function destroy($id, Request $req)
    {
    $redirectRoute = $req->input('redirect_to') === 'modern.dashboard'
        ? 'modern.dashboard'
        : 'cpayments.index';

    // Find the record to be deleted
    $cusiddelete = customerledgerdetails::findOrFail($id);

    // Store values for insertion into TrackCustomerLedger
    $title = "Deleted_CustomerPayment";
    $updated_by = session('user_email');

    $notes = 'Record ID: ' . $cusiddelete->id .
    ', Customer ID: ' . $cusiddelete->customerid .
    ', Date: ' . $cusiddelete->date .
    ', Particulars: ' . $cusiddelete->particulars .
    ', Voucher Type: ' . $cusiddelete->voucher_type .
    ', Credit: ' . $cusiddelete->credit .
    ', Deleted by: ' . $updated_by;
    // Check if the invoice type is "payment"
    if ($cusiddelete->invoicetype === 'payment') {
        DB::transaction(function () use ($cusiddelete, $title, $updated_by, $notes) {
            customerledgerdetails::where('customerid', $cusiddelete->customerid)
                ->where('date', $cusiddelete->date)
                ->where('invoicetype', 'settlement')
                ->where('notes', 'like', '%CR-(' . $cusiddelete->id . ')%')
                ->delete();

            $cusiddelete->delete();

            TrackCustomerLedger::create([
                'title' => $title,
                'updated_by' => $updated_by,
                'notes' => $notes
            ]);
        });

        return redirect()->route($redirectRoute)->with('success', 'Customer Payment Receipt Deleted successfully.');
    } else {
        // If invoice type is not "payment", return with an error message
        return redirect()->route($redirectRoute)->with('error', 'Cannot delete this record as invoice type is not "payment".');
    }
    }

    public function markChequeExchanged($id)
    {
        $payment = customerledgerdetails::where('invoicetype', 'payment')
            ->where('is_cheque', true)
            ->findOrFail($id);

        $payment->cheque_exchanged = true;
        $payment->save();

        return back()->with('success', 'Cheque marked as exchanged.');
    }

private function chequeExchangeDate(Request $request): ?string
{
    if (!$request->boolean('is_cheque')) return null;
    $value = trim((string) $request->input('cheque_exchange_date_bs'));
    if (!preg_match('/^(\\d{4})-(\\d{1,2})-(\\d{1,2})$/', $value, $parts)) {
        throw \Illuminate\Validation\ValidationException::withMessages(['cheque_exchange_date_bs' => 'Enter the cheque date in B.S. format YYYY-MM-DD.']);
    }
    try {
        return NepaliDate::bsToAdString((int)$parts[1], (int)$parts[2], (int)$parts[3]);
    } catch (\Throwable $exception) {
        throw \Illuminate\Validation\ValidationException::withMessages(['cheque_exchange_date_bs' => 'The cheque B.S. date is not valid.']);
    }
}





//foralldetailsdisplay
public function showdetails()
{
    if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'View ',
            'title'=>'Today sales',
            'link'=>'View All Details'
        ];

      
         $cus=customerledgerdetails::orderBy('id','DESC')->get();

         
         return view('allsalesdetails.index',['all'=>$cus,'breadcrumb'=>$breadcrumb]);
}
}


public function showtodaysalesdetailsforpdf()
{
    if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'View ',
            'title'=>'All Details',
            'link'=>'View All Details'
        ];

      
         $cus=customerledgerdetails::orderBy('id','DESC')->get();

         
         return view('allsalesdetails.index',['all'=>$cus,'breadcrumb'=>$breadcrumb]);
}

}
//foralldetailsdisplay
public function showallcuscreditdetails()
{
    if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'View ',
            'title'=>'All Customer Credit Details List',
            'link'=>'View Customer Credit Details List'
        ];

      
       
       // $query=customerledgerdetails::orderBy('id','DESC')->get();
       
        return view('allsalesdetails.allcustomercreditlist', [ 'breadcrumb' => $breadcrumb]);
        

}
}
}
