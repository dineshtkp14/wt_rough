<?php

namespace App\Http\Livewire;

use App\Models\customerledgerdetails;
use App\Models\customerinfo;

use Livewire\Component;
use Livewire\WithPagination;

class Allcustomercreditlistlivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

    public function render()
    {
        // Calculate the total_debit and total_credit separately
        $totals = customerledgerdetails::select(
            \DB::raw('SUM(IFNULL(debit, 0)) AS total_debit'),
            \DB::raw('SUM(IFNULL(credit, 0)) AS total_credit')
        )->where('invoicetype', 'credit')->first();

        // Main query to get individual customer data
        $query = customerledgerdetails::select(
            'customerid',
            \DB::raw('SUM(debit) AS total_debit'),
            \DB::raw('COALESCE(SUM(credit), 0) AS total_credit'),
            \DB::raw('COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) AS debit_credit_difference'
            ))
            ->where('invoicetype', 'credit')
            ->groupBy('customerid');
    
        // Apply search conditions
        if (!empty($this->searchTerm)) {
            $query->where(function ($query) {
                $query->where('customerid', 'like', "%" . $this->searchTerm . "%")
                    ->orWhereHas('customerinfo', function ($subQuery) {
                        $subQuery->where('name', 'like', "%" . $this->searchTerm . "%");
                    });
            });
        }
    
        // Paginate the results
        $allResults = $query->paginate(10);
    
        // Fetch additional data
        foreach ($allResults as $data) {
            if ($data->customerid) {
                $item = customerinfo::where('id', $data->customerid)->select('name', 'phoneno')->first();
                if ($item) {
                    $data->cname = $item->name;
                    $data->cphoneno = $item->phoneno;
                }
            }
        }

        return view('livewire.allcustomercreditlistlivewire', [
            'all' => $allResults,
            'total_debit' => $totals->total_debit,
            'total_credit' => $totals->total_credit,
        ]);
    }
    
    
    

}