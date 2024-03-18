<?php


namespace App\Http\Livewire;

use App\Models\Trackcreditnotes;
use Livewire\Component;
use Livewire\WithPagination;

class TrackcreditnotesLivewire extends Component
{
   
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

 
   public function render(){ 

       $trackcn = Trackcreditnotes::orderby('id','DESC')->select('*');
        if(!empty($this->searchTerm)){

            $trackcn->orWhere('id','like',"%".$this->searchTerm."%");
            $trackcn->orWhere('Cn_bill_no','like',"%".$this->searchTerm."%");
            $trackcn->orWhere('notes','like',"%".$this->searchTerm."%");

            
        }

       $trackcn =$trackcn->paginate(50);

        return view('livewire.trackcreditnotes-livewire', [
             'all' =>$trackcn,
        ]);
}
}
