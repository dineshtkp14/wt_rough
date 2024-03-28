<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ChequeDeposit;
use Livewire\WithPagination;
use Carbon\Carbon;

class ChequeDepositLivewire extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";
    public $selectedDateFilter = "";

    public function render()
    {
        $query = ChequeDeposit::orderBy('id', 'DESC');

        // Apply search filter
        if (!empty($this->searchTerm)) {
            $query->where(function ($query) {
                $query->where('id', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('date', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('cheque_date', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('amount', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('customerid', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('bank_name', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('added_by', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('notes', 'like', '%' . $this->searchTerm . '%');
            });
        }

        // Apply date filter
        if ($this->selectedDateFilter == 'today') {
            $query->whereDate('cheque_date', Carbon::today());
        }

        $chequeDeposits = $query->paginate(50);

        return view('livewire.chequedeposit-livewire', [ 'all' =>$chequeDeposits ]);
    }
}
