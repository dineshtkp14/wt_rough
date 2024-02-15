<?php

namespace App\Http\Livewire;

use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class StockLivewire extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";
    public $firm_name = ''; // Define a public property to capture selected firm name

    public function render()
    {
        $query = Item::orderBy('id', 'DESC')
             ->where('check_remove_ofs', 0)
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
    
        if (!empty($this->firm_name)) {
            $query->where('firm_name', $this->firm_name);
        }
        $all = $query->paginate(7);
    
        $warning = Item::where('showwarning', '>', 0)
                       ->where('quantity', '>=', 1)
                       ->where('check_remove_ofs', 0)
                       ->where('quantity', '<=', DB::raw('showwarning'))
                       ->count();
    
        $count = Item::where('quantity', '>', 0)->where('check_remove_ofs', 0)->count();
        $couout = Item::where('quantity', '=', 0)->where('check_remove_ofs', 0)->count();

        // Check if a firm name is selected, if yes, return only the filtered items, else return all items
    if (!empty($this->firm_name)) {
        dd("ok");
        return view('livewire.stock-livewire', [
            'all' => $all,
            'cou' => $count,
            'x' => $couout,
            'war' => $warning
        ]);
    } else {
        // dd("nonono");
        return view('livewire.stock-livewire', [
            'all' => Item::orderBy('id', 'DESC')->where('check_remove_ofs', 0)->paginate(7),
            'cou' => $count,
            'x' => $couout,
            'war' => $warning
        ]);
    }
}
}
