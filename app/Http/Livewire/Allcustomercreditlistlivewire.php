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
    public $sortBy = ''; // Add this property

    public function render()
    {
       

        // Main query to get individual customer data
        $query = customerledgerdetails::select(
            'customerid',
            \DB::raw('SUM(debit) AS total_debit'),
            \DB::raw('COALESCE(SUM(credit), 0) AS total_credit'),
            \DB::raw('COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) AS debit_credit_difference'
            ))
            ->where('invoicetype', 'credit')
            ->orWhere('invoicetype', 'payment')
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


        // Apply sorting if sortBy is set
        if ($this->sortBy !== '') {
            $query->orderBy('debit_credit_difference', $this->sortBy);
        }

  

        // Paginate the results
        $allResults = $query->paginate(20);

        $this->totalDebitCreditDifference = $allResults->filter(function ($value, $key) {
            return $value->debit_credit_difference > 0;
        })->sum('debit_credit_difference');

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
          
        ]);
    }
}
