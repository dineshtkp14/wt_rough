<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BackupInvoice;
use App\Models\customerinfo;

class DeletedinvoiceLivewire extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

    public function render()
    {
        $alldata = BackupInvoice::select('backup_invoices.*', 'customerinfos.name as customer_name')
            ->leftJoin('customerinfos', 'backup_invoices.customerid', '=', 'customerinfos.id')
            ->where('customerinfos.name', 'like', "%" . $this->searchTerm . "%")
            ->orWhere('backup_invoices.subtotal', 'like', "%" . $this->searchTerm . "%")
            ->orWhere('backup_invoices.discount', 'like', "%" . $this->searchTerm . "%")
            ->orWhere('backup_invoices.total', 'like', "%" . $this->searchTerm . "%")
            ->orderBy('backup_invoices.id', 'DESC')
            ->paginate(10);

        foreach ($alldata as $data) {
            if ($data->customerid) {
                $item = customerinfo::where('id', $data->customerid)->select('name')->first();
                if ($item) {
                    $data->name = $item->name;
                }
            }
        }

        return view('livewire.deletedinvoice-livewire', [
            'all' => $alldata,
        ]);
    }
}







