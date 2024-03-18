<?php

namespace App\Http\Livewire;
use Livewire\Component;
use App\Models\item;
use App\Models\Expense;
use Livewire\WithPagination;

class ExpenseLivewire extends Component
{
        use WithPagination;
        protected $paginationTheme = 'bootstrap';
        public $searchTerm = ""; 
   
        public function render()
    {
        $cus = Expense::orderby('id','DESC')->select('*');
        if(!empty($this->searchTerm)){

            $cus->orWhere('id','like',"%".$this->searchTerm."%");
            $cus->orWhere('billno','like',"%".$this->searchTerm."%");
            $cus->orWhere('amount','like',"%".$this->searchTerm."%");
            $cus->orWhere('date','like',"%".$this->searchTerm."%");
            $cus->orWhere('particulars','like',"%".$this->searchTerm."%");
            $cus->orWhere('notes','like',"%".$this->searchTerm."%");


            
        }

       $cus =$cus->paginate(50);


        return view('livewire.expense-livewire', [
            'expense' =>$cus,
       ]);
    }
}
