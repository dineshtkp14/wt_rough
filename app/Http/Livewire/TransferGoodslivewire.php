<?php


namespace App\Http\Livewire;

use App\Models\TransferGoods;
use App\Models\Item;
use App\Models\salesitem;



use Livewire\Component;
use Livewire\WithPagination;

class TransferGoodslivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";
    public $searchItemId = "";

    public function render()
    {
        $transferGoods = TransferGoods::with('item')
            ->orderBy('id', 'DESC');

        $sumQuantity = null; // Initialize sumQuantity variable
        $sellout=null;
        // $sellout = salesitem::where('itemid', $item->id)->sum('quantity');


        if (!empty($this->searchItemId)) {
            $transferGoods->whereHas('item', function ($query) use (&$sumQuantity) {
                $query->where('id', 'like', '%' . $this->searchItemId . '%');

                // Calculate sum of quantity when search is performed by item ID
                $sumQuantity = TransferGoods::whereHas('item', function ($query) {
                    $query->where('id', 'like', '%' . $this->searchItemId . '%');
                })->sum('quantity');
            });


                    $sellout = salesitem::where('itemid', $this->searchItemId)->sum('quantity');

        }

        if (!empty($this->searchTerm)) {
            $transferGoods->where(function ($query) {
                $query->where('id', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('quantity', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('date', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('shiftArea', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('shiftBy', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('notes', 'like', '%' . $this->searchTerm . '%')
                    ->orWhereHas('item', function ($itemsQuery) {
                        $itemsQuery->where('itemsname', 'like', '%' . $this->searchTerm . '%');
                    });
            });
        }

        $transferGoods = $transferGoods->paginate(50);

        return view('livewire.transfer-goodslivewire', [
            'all' => $transferGoods,
            'sumQuantity' => $sumQuantity, // Pass sumQuantity to the view
            'sellout' => $sellout, // Pass sumQuantity to the view

          
        ]);
    }
}
