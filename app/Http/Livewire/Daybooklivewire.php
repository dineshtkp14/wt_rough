<?php

namespace App\Http\Livewire;

use App\Models\daybook;
use Livewire\Component;
use Livewire\WithPagination;

class Daybooklivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $orderColumn = "name";
    public $sortOrder = "asc";
    public $searchTerm = "";

    public function render()
    {
         //$cus=daybook::orderBy('id','DESC')->get();
         //$count=daybook::all()->where('date', '2023-02-01')->sum('amount');
         $dataval=now()->format('Y-m-d');
        
         $count=daybook::all()->where('date',  $dataval)->where('modeofpay',  "jamma")->sum('amount');
        
        $list = daybook::orderby($this->orderColumn,$this->sortOrder)->select('*');
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
