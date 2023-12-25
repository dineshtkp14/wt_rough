<?php

namespace App\Http\Livewire;

use App\Models\item;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\salesitem;

class ItemSaleslivewire extends Component
{
   
    protected $paginationTheme = 'bootstrap';
    use WithPagination;
    public $searchTerm = '';

    public function render()
    {
        if (Auth::check()) {
            $breadcrumb = [
                'subtitle' => 'View',
                'title' => 'View Invoice Sales Details',
                'link' => 'View Invoice Sales Details'
            ];

            $cus = Salesitem::select('salesitems.*', 'items.itemsname', 'items.mrp')
            ->leftJoin('items', 'salesitems.itemid', '=', 'items.id')
            ->where('items.itemsname', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('salesitems.unstockedname', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('salesitems.quantity', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('salesitems.invoiceid', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('salesitems.price', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('salesitems.discount', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('salesitems.subtotal', 'like', '%' . $this->searchTerm . '%')
            ->orderBy('salesitems.id', 'DESC')
            ->paginate(20);

            foreach ($cus as $data) {
                if ($data->itemid) {
                    $item = item::where('id', $data->itemid)->select('itemsname', 'mrp', 'dlp')->first();

                    if ($item) {
                        $data->itemname = $item->itemsname;
                        $data->itemprice = $item->mrp;
                        $data->itemdlp = $item->dlp;
                    }
                }
            }

            return view('livewire.item-saleslivewire', compact('cus', 'breadcrumb'));
        }

        return redirect('/login');
    }
}
