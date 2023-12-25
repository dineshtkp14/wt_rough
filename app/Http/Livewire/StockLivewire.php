<?php

namespace App\Http\Livewire;

use App\Models\item;
use App\Models\saleitem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class StockLivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
   
    public $searchTerm = "";


    public function render()
    {
        $all = item::orderBy('id', 'DESC')->select('*');
    
        if (!empty($this->searchTerm)) {
            // Check if the search term is "out" and add a condition to search for items with quantity = 0
            if (strtolower($this->searchTerm) === 'out') {
                $all->where('quantity', 0);
            }
             // Check if the search term is "WAR" and add a condition to search for items with $i->quantity <= $i->showwarning and $i->quantity >= 1
        elseif (strtolower($this->searchTerm) === 'war') {
            $all->where('quantity', '>=', 1)
                ->whereColumn('quantity', '<=', 'showwarning');
        }
             else {
                // For other search terms, perform the existing search logic
                $all->orWhere('id', 'like', "%" . $this->searchTerm . "%");
                $all->orWhere('distributorname', 'like', "%" . $this->searchTerm . "%");
                $all->orWhere('itemsname', 'like', "%" . $this->searchTerm . "%");
                $all->orWhere('mrp', 'like', "%" . $this->searchTerm . "%");
            }
        }
    
        $all = $all->paginate(7);

        $warning = item::where('showwarning', '>', 0)
    ->where('quantity', '>=', 1)
    ->where('quantity', '<=', DB::raw('showwarning'))
    ->count();
        $count = item::where('quantity', '>', 0)->count();
        $couout = item::where('quantity', '=', 0)->count();
    
        return view('livewire.stock-livewire', [
            'all' => $all,
            'cou' => $count,
            'x' => $couout,
            'war' => $warning
        ]);
    }
    
}
