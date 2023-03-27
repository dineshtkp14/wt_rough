<?php

namespace App\Http\Livewire;

use App\Models\Bank;
use Livewire\Component;

class Bankslivewire extends Component
{
    public function render()


    {
    $from=date($req->date1);
    $to=date($req->date2);

    $custo=null;
    $count=null;
    if($from == "" || $to==""){

        $custo=bank::orderBy('id','DESC')->get();
    }else{
        
       
        $count=Bank::whereBetween('date',[$from,$to])->sum('amount');
        $custo=Bank::whereBetween('date',  [$from,$to])->get();
}
        
        $list = bank::orderby($this->orderColumn,$this->sortOrder)->select('*');
        if(!empty($this->searchTerm)){

            $list->orWhere('id','like',"%".$this->searchTerm."%");
            $list->orWhere('name','like',"%".$this->searchTerm."%");

            
        }
        $list =$list->paginate(10);
        


        return view('livewire.bankslivewire');
    }


}
    
