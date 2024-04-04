<?php

namespace App\Http\Livewire;

use App\Models\customerledgerdetails;
use App\Models\customerinfo;

use Livewire\Component;
use Livewire\WithPagination;

class CustomerPaymenthistrylivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

    public function render()
{
    $query = customerledgerdetails::select('customerledgerdetails.*', 'customerinfos.name as customername')
    ->join('customerinfos', 'customerledgerdetails.customerid', '=', 'customerinfos.id')
    ->where('customerledgerdetails.invoicetype', 'payment')
    ->orderBy('customerledgerdetails.id', 'DESC');


        // $query = customerledgerdetails::orderby('id','DESC')->select('*');

    if (!empty($this->searchTerm)) {
        $query->where(function ($q) {
            $q->where('customerledgerdetails.id', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('customerledgerdetails.date', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('customerledgerdetails.particulars', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('customerledgerdetails.voucher_type', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('customerledgerdetails.credit', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('customerinfos.name', 'like', '%' . $this->searchTerm . '%');
        });
    }

   


    
    $cus = $query->paginate(50);

     // Convert customerid to customer name
     foreach ($cus as $data) {
        if ($data->customerid) {
            $cus_name = customerinfo::where('id', $data->customerid)->select('name')->first();
            $data->customername = $cus_name ? $cus_name->name : 'Unknown';
        } else {
            $data->customerid = 'Unknown';
        }
    }

        return view('livewire.customer-paymenthistrylivewire', [
            'all' =>$cus,
           
       ]);
    }
}
