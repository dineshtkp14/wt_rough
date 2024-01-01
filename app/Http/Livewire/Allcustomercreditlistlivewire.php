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
        $cus = customerledgerdetails::select(
            'customerid',
            \DB::raw('SUM(debit) AS total_debit'),
            \DB::raw('COALESCE(SUM(credit), 0) AS total_credit'),
            \DB::raw('COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) AS debit_credit_difference')
        )
            ->where('invoicetype', 'credit')
            ->groupBy('customerid');

            if (!empty($this->searchTerm)) {
                $cus->where(function ($query) {
                    $query->where('customerid', 'like', "%" . $this->searchTerm . "%")
                        ->orWhereHas('customerinfo', function ($subQuery) {
                            $subQuery->where('name', 'like', "%" . $this->searchTerm . "%");
                            $subQuery->where('phoneno', 'like', "%" . $this->searchTerm . "%");

                        });
                });
            }

        // Get the results after applying the conditions
        $results = $cus->paginate(20);

        // Iterate over the results and fetch related data
        foreach ($results as $data) {
            
            if ($data->customerid) {
                $item = customerinfo::where('id', $data->customerid)->select('name','phoneno')->first();
                if ($item) {
                    $data->cname = $item->name;
                    $data->cphoneno = $item->phoneno;
                }
            }
        }

        return view('livewire.allcustomercreditlistlivewire', ['all' => $results]);
    }
}
