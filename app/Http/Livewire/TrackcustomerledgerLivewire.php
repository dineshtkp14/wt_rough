<?php

namespace App\Http\Livewire;

use App\Models\TrackCustomerLedger;
use Livewire\Component;
use Livewire\WithPagination;

class TrackcustomerledgerLivewire extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

    public function render()
    {
        $all = TrackCustomerLedger::orderBy('id', 'DESC');
    
        if (!empty($this->searchTerm)) {
            $all->where('id', 'like', "%" . $this->searchTerm . "%")
                ->orWhere('title', 'like', "%" . $this->searchTerm . "%")
                ->orWhere('notes', 'like', "%" . $this->searchTerm . "%")
                ->orWhere('updated_by', 'like', "%" . $this->searchTerm . "%")
                ->orWhere('created_at', 'like', "%" . $this->searchTerm . "%");
        }
    
        $all = $all->paginate(50);
    
        return view('livewire.trackcustomerledger-livewire', [
            'all' => $all,
        ]);
    }
    
}
