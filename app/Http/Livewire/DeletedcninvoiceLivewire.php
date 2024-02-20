<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BackupCreditnotesInvoice;
use App\Models\customerinfo;

class DeletedcninvoiceLivewire extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

    public function render()
    {
        $alldata = BackupCreditnotesInvoice::select('backupcreditnotes_invoices.*', 'customerinfos.name as customer_name')
            ->leftJoin('customerinfos', 'backupcreditnotes_invoices.customerid', '=', 'customerinfos.id')
            ->where('customerinfos.name', 'like', "%" . $this->searchTerm . "%")
            ->orWhere('backupcreditnotes_invoices.subtotal', 'like', "%" . $this->searchTerm . "%")
            ->orWhere('backupcreditnotes_invoices.discount', 'like', "%" . $this->searchTerm . "%")
            ->orWhere('backupcreditnotes_invoices.total', 'like', "%" . $this->searchTerm . "%")
            ->orderBy('backupcreditnotes_invoices.id', 'DESC')
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
