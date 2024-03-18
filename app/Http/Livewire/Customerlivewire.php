<?php

namespace App\Http\Livewire;

use App\Models\customerinfo;
use Livewire\Component;
use Livewire\WithPagination;

class Customerlivewire extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

 
   public function render(){ 

       $cus = customerinfo::orderby('id','DESC')->select('*');
        if(!empty($this->searchTerm)){

            $cus->orWhere('id','like',"%".$this->searchTerm."%");
            $cus->orWhere('name','like',"%".$this->searchTerm."%");
            $cus->orWhere('phoneno','like',"%".$this->searchTerm."%");
            $cus->orWhere('email','like',"%".$this->searchTerm."%");

            
        }

       $cus =$cus->paginate(50);

        return view('livewire.customerlivewire', [
             'all' =>$cus,
        ]);
}


}
