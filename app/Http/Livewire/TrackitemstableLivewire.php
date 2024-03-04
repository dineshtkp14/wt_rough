<?php

namespace App\Http\Livewire;
use App\Models\trackitemstable;

use Livewire\Component;
use Livewire\WithPagination;
class TrackitemstableLivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

    public function render()
    {
       
   
       $trackitemstable = trackitemstable::orderby('id','DESC')->select('*');
        if(!empty($this->searchTerm)){

            $trackitemstable->orWhere('id','like',"%".$this->searchTerm."%");
            $trackitemstable->orWhere('title','like',"%".$this->searchTerm."%");

            $trackitemstable->orWhere('notes','like',"%".$this->searchTerm."%");

            
        }

       $trackitemstable =$trackitemstable->paginate(5);

        return view('livewire.trackitemstable-livewire', [
             'all' =>$trackitemstable,
        ]);
}

   


}
