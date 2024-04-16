<?php

namespace App\Http\Livewire;
use App\Models\Trackitemstable;

use Livewire\Component;
use Livewire\WithPagination;
class TrackitemstableLivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

    public function render()
    {
       
   
       $trackitemstable = Trackitemstable::orderby('id','DESC')->select('*');
        if(!empty($this->searchTerm)){

            $trackitemstable->orWhere('id','like',"%".$this->searchTerm."%");
            $trackitemstable->orWhere('title','like',"%".$this->searchTerm."%");

            $trackitemstable->orWhere('notes','like',"%".$this->searchTerm."%");
            $trackitemstable->orWhere('created_at','like',"%".$this->searchTerm."%");


            
        }

       $trackitemstable =$trackitemstable->paginate(50);

        return view('livewire.trackitemstable-livewire', [
             'all' =>$trackitemstable,
        ]);
}

   


}
