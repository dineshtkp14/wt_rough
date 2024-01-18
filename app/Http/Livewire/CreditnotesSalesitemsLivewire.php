<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\CreditnotesSalesitem; // Fix typo here
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\item; // Fix typo here

class CreditnotesSalesitemsLivewire extends Component
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

            $cus = CreditnotesSalesitem::select('creditnotes_salesitems.*', 'items.itemsname', 'items.mrp')
            ->leftJoin('items', 'creditnotes_salesitems.itemid', '=', 'items.id')
            ->where('items.itemsname', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('creditnotes_salesitems.unstockedname', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('creditnotes_salesitems.quantity', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('creditnotes_salesitems.invoiceid', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('creditnotes_salesitems.price', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('creditnotes_salesitems.discount', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('creditnotes_salesitems.subtotal', 'like', '%' . $this->searchTerm . '%')
            ->orderBy('creditnotes_salesitems.id', 'DESC')
            ->paginate(20);

            foreach ($cus as $data) {
                if ($data->itemid) {
                    $item = item::where('id', $data->itemid)->select('itemsname', 'mrp', 'costprice')->first();

                    if ($item) {
                        $data->itemname = $item->itemsname;
                        $data->itemprice = $item->mrp;
                        $data->itemdlp = $item->costprice;
                    }
                }
            }

            return view('livewire.creditnotes-salesitems-livewire', compact('cus', 'breadcrumb'));
        }

        return redirect('/login');
    }
}
