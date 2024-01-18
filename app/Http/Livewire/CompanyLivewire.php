<?php

namespace App\Http\Livewire;

use App\Models\company;
use Livewire\Component;
use Livewire\WithPagination;


class CompanyLivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

 
   public function render(){ 

    $cus=company::orderBy('id','DESC')->select('*');
    if(!empty($this->searchTerm)){

            $cus->orWhere('id','like',"%".$this->searchTerm."%");
            $cus->orWhere('name','like',"%".$this->searchTerm."%");
            $cus->orWhere('phoneno','like',"%".$this->searchTerm."%");
            $cus->orWhere('email','like',"%".$this->searchTerm."%");

            
        }

       $cus =$cus->paginate(10);

        return view('livewire.company-livewire', [ 'all' =>$cus, ]);
}
    
}
