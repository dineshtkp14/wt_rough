<?php

namespace App\Http\Livewire;

use App\Models\pricelist;
use Livewire\Component;
use Livewire\WithPagination;

class Pricelistliveware extends Component
{
    
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $orderColumn = "itemname";
    public $sortOrder = "asc";
    public $sortLink = '<i class="sorticon fa-solid fa-caret-up"></i>';
    public $searchTerm = "";

    
  


    public function sortOrder($columnName=""){
        $caretOrder = "up";
        if($this->sortOrder == 'asc'){
             $this->sortOrder = 'desc';
             $caretOrder = "down";
        }else{
             $this->sortOrder = 'asc';
             $caretOrder = "up";
        } 
        $this->sortLink = '<i class="sorticon fa-solid fa-caret-'.$caretOrder.'"></i>';

        $this->orderColumn = $columnName;

   }
 
   public function render(){ 


       $list = pricelist::orderby($this->orderColumn,$this->sortOrder)->select('*');
        if(!empty($this->searchTerm)){

            $list->orWhere('id','like',"%".$this->searchTerm."%");
            $list->orWhere('itemname','like',"%".$this->searchTerm."%");

            
        }
        $list =$list->paginate(10);
        

        return view('livewire.pricelistliveware', [
            'pricelist'=>$list
        ]);
}
}
