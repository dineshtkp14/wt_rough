<?php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\CreditnotesInvoice; // Fix typo here
use Livewire\WithPagination;
use App\Models\CustomerInfo; // Fix typo here

class CreditnotesInvoiceLivewire extends Component
{
   
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

    public function render()
    {
        $alldata = CreditnotesInvoice::select('creditnotes_invoices.*', 'customerinfos.name as customer_name')
            ->leftJoin('customerinfos', 'creditnotes_invoices.customerid', '=', 'customerinfos.id')
            ->where('customerinfos.name', 'like', "%" . $this->searchTerm . "%")
            ->orWhere('creditnotes_invoices.subtotal', 'like', "%" . $this->searchTerm . "%")
            ->orWhere('creditnotes_invoices.discount', 'like', "%" . $this->searchTerm . "%")
            ->orWhere('creditnotes_invoices.total', 'like', "%" . $this->searchTerm . "%")
            ->orderBy('creditnotes_invoices.id', 'DESC')
            ->paginate(10);

        foreach ($alldata as $data) {
            if ($data->customerid) {
                $item = CustomerInfo::where('id', $data->customerid)->select('name')->first();
                if ($item) {
                    $data->name = $item->name;
                }
            }
        }

        return view('livewire.creditnotes-invoice-livewire', [
            'all' => $alldata,
        ]);
    }
}
