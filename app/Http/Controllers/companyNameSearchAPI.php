<?php

namespace App\Http\Controllers;

use App\Models\company;
use App\Models\CompanyLedger;
use Illuminate\Http\Request;

class companyNameSearchAPI extends Controller
{
    public function index(Request $req)
    {

        $search = $req->name;

        $companies = company::where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('email', 'LIKE', '%' . $search . '%')
                    ->orWhere('phoneno', 'LIKE', '%' . $search . '%');
            })
            ->get();

        $ledgerTotals = CompanyLedger::whereIn('companyid', $companies->pluck('id'))
            ->selectRaw('companyid, COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
            ->groupBy('companyid')
            ->get()
            ->keyBy('companyid');

        $companies->transform(function ($company) use ($ledgerTotals) {
            $totals = $ledgerTotals->get($company->id);
            $totalDue = round((float) ($totals->total_debit ?? 0) - (float) ($totals->total_credit ?? 0), 2);

            $company->total_due = $totalDue;
            $company->total_due_formatted = number_format($totalDue, 2);

            return $company;
        });

        return response()->json($companies);
      



    }
}
