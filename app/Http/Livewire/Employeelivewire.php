<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;

class Employeelivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

    public function render()
    {
        $emp = User::orderBy('id', 'DESC')->select('*');
        if (!empty($this->searchTerm)) {
            $emp->orWhere('id', 'like', "%" . $this->searchTerm . "%");
            $emp->orWhere('name', 'like', "%" . $this->searchTerm . "%");
            $emp->orWhere('phoneno', 'like', "%" . $this->searchTerm . "%");
            $emp->orWhere('email', 'like', "%" . $this->searchTerm . "%");
        }

        $emp = $emp->paginate(5);

        return view('livewire.employeelivewire', [
            'all' => $emp,
        ]);
    }
}
