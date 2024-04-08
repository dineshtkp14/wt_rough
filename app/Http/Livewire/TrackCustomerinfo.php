<?php

namespace App\Http\Livewire;

use App\Models\trackcustomerinfos; // Corrected model name
use Livewire\Component;
use Livewire\WithPagination;

class TrackCustomerinfo extends Component
{
    use WithPagination; // Moved the 'use WithPagination' statement outside of the render() method
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

    public function render()
    {
        // Corrected model name
        $all = trackcustomerinfos::orderBy('id', 'DESC');

        if (!empty($this->searchTerm)) {
            // Modified the query to use 'orWhere' correctly
            $all->where('id', 'like', "%" . $this->searchTerm . "%")
                ->orWhere('title', 'like', "%" . $this->searchTerm . "%")
                ->orWhere('notes', 'like', "%" . $this->searchTerm . "%")
                ->orWhere('updated_by', 'like', "%" . $this->searchTerm . "%")
                ->orWhere('created_at', 'like', "%" . $this->searchTerm . "%");
        }

        // Corrected variable name and pagination
        $all = $all->paginate(50);
        
        // Corrected variable name in the view return
        return view('livewire.track-customerinfo', [
            'all' => $all, // Changed 'trackcn' to 'all'
        ]);
    }
}
