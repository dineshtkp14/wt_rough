<?php

namespace App\Http\Livewire;
use App\Models\PurchaseOrder;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseOrderlivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

    public function render()

    {
        $cus=PurchaseOrder::orderBy('id','DESC')->select('*');
        if(!empty($this->searchTerm)){
    
                $cus->orWhere('id','like',"%".$this->searchTerm."%");
                $cus->orWhere('date','like',"%".$this->searchTerm."%");
                $cus->orWhere('orderlist','like',"%".$this->searchTerm."%");
                $cus->orWhere('notes','like',"%".$this->searchTerm."%");
    
                
            }
    
           $cus =$cus->paginate(50);
        return view('livewire.purchase-orderlivewire', [ 'all' =>$cus, ]);
    
    }
}
