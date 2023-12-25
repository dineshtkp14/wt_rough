<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\invoice;
use Livewire\WithPagination;
use App\Models\customerinfo;

class Invoicelivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

    public function render()
    {
        $alldata = invoice::select('invoices.*', 'customerinfos.name as customer_name')
            ->leftJoin('customerinfos', 'invoices.customerid', '=', 'customerinfos.id')
            ->where('customerinfos.name', 'like', "%" . $this->searchTerm . "%")
            
            ->orWhere('invoices.subtotal', 'like', "%" . $this->searchTerm . "%")
            ->orWhere('invoices.discount', 'like', "%" . $this->searchTerm . "%")
            ->orWhere('invoices.total', 'like', "%" . $this->searchTerm . "%")
            ->orderBy('invoices.id', 'DESC')
            ->paginate(10);

        // $alldata = $query->paginate(10);

        foreach ($alldata as $data) {
            if ($data->customerid) {
                $item = customerinfo::where('id', $data->customerid)->select('name')->first();
                if ($item) {
                    $data->name = $item->name;
                    
                }
            }
        }

        return view('livewire.invoicelivewire', [
            'all' => $alldata,
        ]);
    }
}
