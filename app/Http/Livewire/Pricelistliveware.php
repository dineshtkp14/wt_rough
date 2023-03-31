<?php

namespace App\Http\Livewire;

use App\Models\pricelist;
use Livewire\Component;
use Livewire\WithPagination;

class Pricelistliveware extends Component
{
    
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
   
    public $searchTerm = "";

    
  


   public function render(){ 


       $list = pricelist::orderby('id','DESC')->select('*');
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
