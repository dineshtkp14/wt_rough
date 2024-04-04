<?php

namespace App\Http\Livewire;

use App\Models\CompanyLedger;
use App\Models\Company;

use Livewire\Component;
use Livewire\WithPagination;

class BillEntrylivewire extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

    public function render()
    {
        $query = CompanyLedger::select('company_ledgers.*')
            ->leftJoin('companies', 'company_ledgers.companyid', '=', 'companies.id')
            ->orderBy('company_ledgers.id', 'DESC');

        if (!empty($this->searchTerm)) {
            $query->where(function($q) {
                $q->where('company_ledgers.id', 'like', "%{$this->searchTerm}%")
                  ->orWhere('company_ledgers.particulars', 'like', "%{$this->searchTerm}%")
                  ->orWhere('company_ledgers.voucher_type', 'like', "%{$this->searchTerm}%")
                  ->orWhere('company_ledgers.voucher_no', 'like', "%{$this->searchTerm}%")
                  ->orWhere('company_ledgers.debit', 'like', "%{$this->searchTerm}%")
                  ->orWhere('company_ledgers.credit', 'like', "%{$this->searchTerm}%")
                  ->orWhere('company_ledgers.notes', 'like', "%{$this->searchTerm}%")
                  ->orWhere('companies.name', 'like', "%{$this->searchTerm}%");
            });
        }

        $records = $query->paginate(50);
        
          // Convert companyid to customer name
     foreach ($records as $data) {
        if ($data->companyid) {
            $cus_name = company::where('id', $data->companyid)->select('name')->first();
            $data->companyname = $cus_name ? $cus_name->name : 'Unknown';
        } else {
            $data->companyid = 'Unknown';
        }
    }


        return view('livewire.bill-entrylivewire', ['all' => $records]);
    }
}
