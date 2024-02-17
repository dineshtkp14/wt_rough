<?php

namespace App\Http\Livewire;

use App\Models\item;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class StockLivewire extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";
    public $firm_name = '';

    public function render()
    {
        $query = item::orderBy('id', 'DESC')
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
                        ->orWhere('mrp', 'like', "%$searchTerm%")
                        ->orWhere('firm_name', 'like', "%$searchTerm%");
                });
            }
        }

        if (!empty($this->firm_name)) {
            $query->where('firm_name', $this->firm_name);
        }

        $all = $query->paginate(50);

        $warning = item::where('showwarning', '>', 0)
            ->where('quantity', '>=', 1)
            ->where('check_remove_ofs', 0)
            ->where('quantity', '<=', DB::raw('showwarning'))
            ->count();

        $count = item::where('quantity', '>', 0)
            ->where('check_remove_ofs', 0)
            ->count();

        $couout = item::where('quantity', '=', 0)
            ->where('check_remove_ofs', 0)
            ->count();

        return view('livewire.stock-livewire', [
            'all' => $all,
            'cou' => $count,
            'x' => $couout,
            'war' => $warning
        ]);
    }
}
