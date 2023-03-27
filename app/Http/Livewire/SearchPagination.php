<?php

namespace App\Http\Livewire;

use App\Models\customerinfo;
use Livewire\Component;
use Livewire\WithPagination;

class SearchPagination extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $orderColumn = "name";
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

     //$cus=customerinfo::orderBy('id','DESC')->get();

       $cus = customerinfo::orderby($this->orderColumn,$this->sortOrder)->select('*');
        if(!empty($this->searchTerm)){

            $cus->orWhere('id','like',"%".$this->searchTerm."%");
            $cus->orWhere('name','like',"%".$this->searchTerm."%");
            $cus->orWhere('phoneno','like',"%".$this->searchTerm."%");

            
        }

       $cus =$cus->paginate(10);

        return view('livewire.search-pagination', [
             'all' =>$cus,
        ]);
}

}