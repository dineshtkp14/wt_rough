<?php




namespace App\Http\Livewire;

use App\Models\CompanyLedger;
use Livewire\Component;
use Livewire\WithPagination;



class BillEntrylivewire extends Component
{
   
use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

 
   public function render(){ 

    $cus=CompanyLedger::orderBy('id','DESC')->select('*');
    if(!empty($this->searchTerm)){

            $cus->orWhere('id','like',"%".$this->searchTerm."%");
          

            
        }

       $cus =$cus->paginate(50);

        return view('livewire.bill-entrylivewire', [
             'all' =>$cus,
        ]);
}
}
