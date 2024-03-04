<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Myfirm;
use Livewire\WithPagination;



class MyfirmLivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

    public function render()
    {
        $cus = Myfirm::orderby('id','DESC')->select('*');
        if(!empty($this->searchTerm)){

            $cus->orWhere('id','like',"%".$this->searchTerm."%");
            $cus->orWhere('firm_name','like',"%".$this->searchTerm."%");
            $cus->orWhere('nick_name','like',"%".$this->searchTerm."%");
            $cus->orWhere('notes','like',"%".$this->searchTerm."%");

            
        }

       $cus =$cus->paginate(5);
       return view('livewire.myfirm-livewire', [
        'all' =>$cus,
   ]);
}
}