<?php

namespace App\Http\Livewire;

use App\Models\Trackinvoice;
use Livewire\Component;
use Livewire\WithPagination;

class TrackinvoiceLivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

 
   public function render(){ 

       $trackinvoice = Trackinvoice::orderby('id','DESC')->select('*');
        if(!empty($this->searchTerm)){

            $trackinvoice->orWhere('id','like',"%".$this->searchTerm."%");
            $trackinvoice->orWhere('bill_no','like',"%".$this->searchTerm."%");
            $trackinvoice->orWhere('notes','like',"%".$this->searchTerm."%");

            
        }

       $trackinvoice =$trackinvoice->paginate(50);

        return view('livewire.trackinvoice-livewire', [
             'all' =>$trackinvoice,
        ]);
}

   
}
