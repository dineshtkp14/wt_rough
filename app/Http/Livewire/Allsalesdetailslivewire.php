<?php

namespace App\Http\Livewire;

use App\Models\customerledgerdetails;
use App\Models\customerinfo;

use Livewire\Component;
use Livewire\WithPagination;


class Allsalesdetailslivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

 
    public function render()
    {
        $cus = customerledgerdetails::orderBy('id', 'DESC')->select('*');
    
        if (!empty($this->searchTerm)) {
            $cus->orWhere('invoiceid', 'like', "%" . $this->searchTerm . "%");
            $cus->orWhere('particulars', 'like', "%" . $this->searchTerm . "%");
            $cus->orWhere('voucher_type', 'like', "%" . $this->searchTerm . "%");
            $cus->orWhere('date', 'like', "%" . $this->searchTerm . "%");
            $cus->orWhere('debit', 'like', "%" . $this->searchTerm . "%");
            $cus->orWhere('credit', 'like', "%" . $this->searchTerm . "%");
            $cus->orWhere('invoicetype', 'like', "%" . $this->searchTerm . "%");
            $cus->orWhere('notes', 'like', "%" . $this->searchTerm . "%");
    
            // Use whereHas to search by name in the customerinfo table
            $cus->orWhereHas('customerinfo', function ($query) {
                $query->where('name', 'like', "%" . $this->searchTerm . "%");
            });
        }
    
        // Get the results after applying the conditions
        $results = $cus->paginate(50);
    
        // Iterate over the results and fetch related data
        foreach ($results as $data) {
            if ($data->customerid) {
                $item = customerinfo::where('id', $data->customerid)->select('name')->first();
                if ($item) {
                    $data->cname = $item->name;
                }
            }
        }
    
        return view('livewire.allsalesdetailslivewire', ['all' => $results]);
    }
    
    
    }
