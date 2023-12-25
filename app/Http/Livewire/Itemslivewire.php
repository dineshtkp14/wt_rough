<?php

namespace App\Http\Livewire;

use App\Models\item;
use Livewire\Component;
use Livewire\WithPagination;

class Itemslivewire extends Component
{
        
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
   
    public $searchTerm = "";

    
    public function render()
    {
          $all = item::orderby('id','DESC')->select('*');
        if(!empty($this->searchTerm)){

            $all->orWhere('id','like',"%".$this->searchTerm."%");
            $all->orWhere('distributorname','like',"%".$this->searchTerm."%");
            $all->orWhere('itemsname','like',"%".$this->searchTerm."%");
            $all->orWhere('mrp','like',"%".$this->searchTerm."%");

            
        }

       $all =$all->paginate(7);
        return view('livewire.itemslivewire',[
            'all' =>$all,
        ]);
    }
}
