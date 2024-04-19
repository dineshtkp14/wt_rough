<?php

namespace App\Http\Livewire;

use App\Models\item;
use App\Models\company;

use Livewire\Component;
use Livewire\WithPagination;

class Itemslivewire extends Component
{
        
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
   
    public $searchTerm = "";

    
    public function render()
{
    $all = Item::orderBy('id', 'DESC')->select('*');

    if (!empty($this->searchTerm)) {
        $all->orWhere('id', 'like', "%" . $this->searchTerm . "%")
            ->orWhere('companyid', 'like', "%" . $this->searchTerm . "%")
            ->orWhere('itemsname', 'like', "%" . $this->searchTerm . "%")
            ->orWhere('mrp', 'like', "%" . $this->searchTerm . "%")
            ->orWhere('notes', 'like', "%" . $this->searchTerm . "%")
            ->orWhere('billno', 'like', "%" . $this->searchTerm . "%");
    }

    $all = $all->paginate(50);

    // Loop through each item to fetch the associated company name
    foreach ($all as $item) {
        $company = Company::where('id', $item->companyid)->select('name')->first();
        if ($company) {
            // Assign the company name to the item
            $item->companyname = $company->name;
           
        }
    }

    return view('livewire.itemslivewire', [
        'all' => $all,
    ]);
}

}


