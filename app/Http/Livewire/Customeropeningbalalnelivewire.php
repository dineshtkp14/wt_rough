<?php

namespace App\Http\Livewire;

use App\Models\customerinfo;
use App\Models\customerledgerdetails;

use Livewire\Component;
use Livewire\WithPagination;

class Customeropeningbalalnelivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

    public function render()
    {
        $cus = customerledgerdetails::orderby('id','DESC')->select('*');
    
        if (!empty($this->searchTerm)) {
            $cus->where(function ($query) {
                $query->orWhere('id','like',"%".$this->searchTerm."%")
                      ->orWhere('date','like',"%".$this->searchTerm."%")
                      ->orWhere('particulars','like',"%".$this->searchTerm."%")
                      ->orWhere('invoicetype','like',"%".$this->searchTerm."%")
                      ->orWhere('debit','like',"%".$this->searchTerm."%")
                      ->orWhere('notes','like',"%".$this->searchTerm."%")
                      ->orWhere('added_by','like',"%".$this->searchTerm."%");
                      
                // Search by customer name
                $query->orWhereHas('customer', function ($query) {
                    $query->where('name', 'like', "%".$this->searchTerm."%");
                });
            });
        }
    
        $cus = $cus->paginate(50);
    
        // Convert customerid to customer name
        foreach ($cus as $data) {
            if ($data->customerid) {
                $cus_name = customerinfo::where('id', $data->customerid)->select('name')->first();
                $data->customername = $cus_name ? $cus_name->name : 'Unknown';
            } else {
                $data->customername = 'Unknown';
            }
        }
    
        return view('livewire.customeropeningbalalnelivewire', [
            'all' =>$cus,
        ]);
    }
}