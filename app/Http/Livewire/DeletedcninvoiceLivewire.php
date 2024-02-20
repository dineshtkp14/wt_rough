<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;
use App\Models\CustomerInfo;

class DeletedcninvoiceLivewire extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

    public function render()
    {
        $alldata = Invoice::select('invoices.*', 'customerinfos.name as customer_name')
            ->leftJoin('customerinfos', 'invoices.customerid', '=', 'customerinfos.id')
            ->where('customerinfos.name', 'like', "%" . $this->searchTerm . "%")
            ->orWhere('invoices.subtotal', 'like', "%" . $this->searchTerm . "%")
            ->orWhere('invoices.discount', 'like', "%" . $this->searchTerm . "%")
            ->orWhere('invoices.total', 'like', "%" . $this->searchTerm . "%")
            ->orderBy('invoices.id', 'DESC')
            ->paginate(10);

        foreach ($alldata as $data) {
            if ($data->customerid) {
                $item = CustomerInfo::where('id', $data->customerid)->select('name')->first();
                if ($item) {
                    $data->name = $item->name;
                }
            }
        }

        return view('livewire.deletedcninvoice-livewire', [
            'all' => $alldata,
        ]);
    }
}
