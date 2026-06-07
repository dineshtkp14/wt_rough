<?php

namespace App\Http\Controllers;

use App\Models\customerinfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerAPI extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $req)
    {

        $cus = customerinfo::where('name', 'LIKE', '%'.$req->name.'%')
            ->orWhere('email', 'LIKE', '%'.$req->name.'%')
            ->orWhere('phoneno', 'LIKE', '%'.$req->name.'%')
            ->orWhere('address', 'LIKE', '%'.$req->name.'%')
            ->limit(20)
            ->get();

        $customerIds = $cus->pluck('id');

        $ledgerDue = collect();
        $creditNoteCredit = collect();

        if ($customerIds->isNotEmpty()) {
            $ledgerDue = DB::table('customerledgerdetails')
                ->select('customerid', DB::raw('SUM(COALESCE(debit, 0) - COALESCE(credit, 0)) as due'))
                ->whereIn('customerid', $customerIds)
                ->whereIn('invoicetype', ['credit', 'payment', 'settlement'])
                ->groupBy('customerid')
                ->pluck('due', 'customerid');

            $creditNoteCredit = DB::table('creditnotes_customerledgerdetails as cn')
                ->select('cn.customerid', DB::raw('SUM(COALESCE(cn.debit, cn.credit, 0)) as credit'))
                ->whereIn('cn.customerid', $customerIds)
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('customerledgerdetails as oldcl')
                        ->whereColumn('oldcl.customerid', 'cn.customerid')
                        ->where(function ($match) {
                            $match->whereColumn('oldcl.cninvoiceid', 'cn.invoiceid')
                                ->orWhereColumn('oldcl.returnidforcreditnotes', 'cn.invoiceid')
                                ->orWhere(function ($sameReturn) {
                                    $sameReturn->whereColumn('oldcl.date', 'cn.date')
                                        ->where(function ($returnType) {
                                            $returnType->whereIn('oldcl.particulars', ['salesreturn', 'Goods_Return'])
                                                ->orWhereIn('oldcl.voucher_type', ['return', 'Return']);
                                        })
                                        ->whereRaw('oldcl.credit BETWEEN COALESCE(cn.debit, cn.credit, 0) - 0.01 AND COALESCE(cn.debit, cn.credit, 0) + 0.01');
                                });
                        });
                })
                ->groupBy('cn.customerid')
                ->pluck('credit', 'customerid');
        }

        $cus->transform(function ($customer) use ($ledgerDue, $creditNoteCredit) {
            $due = (float) ($ledgerDue[$customer->id] ?? 0) - (float) ($creditNoteCredit[$customer->id] ?? 0);
            $customer->total_due = max(0, $due);
            $customer->total_due_formatted = number_format($customer->total_due, 2);

            return $customer;
        });

        return response()->json($cus);
      



    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
