<?php

namespace App\Livewire;

use App\Models\WorkShift;
use Livewire\Component;

class WorkShiftCrud extends Component
{

    public $shift,$late;

    protected $rules = [
        'shift' => 'required',
        'late' => 'nullable',
    ];

    public function save(){
        $this->validate($this->rules);

        WorkShift::Create([
            'shift' => $this->shift,
            'late' => $this->late,
        ]);

        $this->resetForm();
    }

    public function delete($id){
        WorkShift::find($id)->delete();
    }

    public function resetForm(){
        $this->shift = '';
        $this->late = '';
    }
    
    public function render()
    {
        return view('livewire.work-shift-crud', [
            'shifts' => WorkShift::orderBy('shift', 'asc')->get()
        ]);
    }
}
