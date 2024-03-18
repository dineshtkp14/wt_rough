<?php


namespace App\Http\Livewire;
use App\Models\trackcompanybillentry;
use Livewire\Component;
use Livewire\WithPagination;

class TrackcompanyledgerLivewire extends Component
{
   
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

 
   public function render(){ 

       $trackcompanyledger = trackcompanybillentry::orderby('id','DESC')->select('*');
        if(!empty($this->searchTerm)){

            $trackcompanyledger->orWhere('id','like',"%".$this->searchTerm."%");
            $trackcompanyledger->orWhere('updated_by','like',"%".$this->searchTerm."%");
            $trackcompanyledger->orWhere('notes','like',"%".$this->searchTerm."%");
            $trackcompanyledger->orWhere('title','like',"%".$this->searchTerm."%");

            
        }

       $trackcompanyledger =$trackcompanyledger->paginate(50);

        return view('livewire.trackcompanyledger-livewire', [
             'all' =>$trackcompanyledger,
        ]);
}
}

