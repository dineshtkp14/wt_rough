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
        // $query = Item::orderBy('id', 'DESC')->select('*');

        $query = Item::orderBy('id', 'DESC')
             ->where('id', 2)
             ->select('*');
    
        if (!empty($this->searchTerm)) {
            $searchTerm = strtolower(trim($this->searchTerm));
    
            if ($searchTerm === 'out') {
                $query->where('quantity', 0);
            } elseif ($searchTerm === 'war') {
                $query->where('quantity', '>=', 1)
                      ->where('quantity', '<=', DB::raw('showwarning'));
            } else {
                $query->where(function ($subquery) use ($searchTerm) {
                    $subquery->where('id', 'like', "%$searchTerm%")
                             ->orWhere('distributorname', 'like', "%$searchTerm%")
                             ->orWhere('itemsname', 'like', "%$searchTerm%")
                             ->orWhere('mrp', 'like', "%$searchTerm%");
                });
            }
        }
    
        $all = $query->paginate(7);
    
        $warning = Item::where('showwarning', '>', 0)
                       ->where('quantity', '>=', 1)
                       ->where('quantity', '<=', DB::raw('showwarning'))
                       ->count();
    
        $count = Item::where('quantity', '>', 0)->count();
        $couout = Item::where('quantity', '=', 0)->count();
    
        return view('livewire.stock-livewire', [
            'all' => $all,
            'cou' => $count,
            'x' => $couout,
            'war' => $warning
        ]);
    }


}
