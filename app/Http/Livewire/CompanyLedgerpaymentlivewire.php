<?php

namespace App\Http\Livewire;

use App\Models\CompanyLedger;
use App\Models\company;

use Livewire\Component;
use Livewire\WithPagination;

class CompanyLedgerpaymentlivewire extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

    public function render()
    {
        // Start with the base query
        $query = CompanyLedger::select('company_ledgers.*')
            ->leftJoin('companies', 'company_ledgers.companyid', '=', 'companies.id')
            ->orderBy('company_ledgers.id', 'DESC');

        // Apply search filter if search term is provided
        if (!empty($this->searchTerm)) {
            $query->where(function ($query) {
                $query->where('company_ledgers.id', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('company_ledgers.particulars', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('company_ledgers.voucher_type', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('company_ledgers.date', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('company_ledgers.debit', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('company_ledgers.notes', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('companies.name', 'like', '%' . $this->searchTerm . '%'); // Search by companyname
            });
        }

        // Paginate the results
        $records = $query->paginate(50);

        // Convert companyid to company name
        foreach ($records as $data) {
            if ($data->companyid) {
                $company = company::find($data->companyid);
                $data->companyname = $company ? $company->name : 'Unknown';
            } else {
                $data->companyname = 'Unknown';
            }
        }

        return view('livewire.company-ledgerpaymentlivewire', ['all' => $records]);
    }
}
