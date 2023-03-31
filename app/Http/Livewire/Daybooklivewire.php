<?php

namespace App\Http\Livewire;

use App\Models\daybook;
use Livewire\Component;
use Livewire\WithPagination;

class Daybooklivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

    public function render()
    {
         $dataval=now()->format('Y-m-d');
         $count=daybook::all()->where('date',  $dataval)->where('modeofpay',  "jamma")->sum('amount');
        
        $list = daybook::orderby('id','DESC')->select('*');
        if(!empty($this->searchTerm)){

            $list->orWhere('name','like',"%".$this->searchTerm."%");
            $list->orWhere('contact','like',"%".$this->searchTerm."%");
  
        }
        $list =$list->paginate(10);
        

        return view('livewire.daybooklivewire', [
            'custo'=>$list,
            'totalsum'=>$count
        ]);
      
    }

    


}
