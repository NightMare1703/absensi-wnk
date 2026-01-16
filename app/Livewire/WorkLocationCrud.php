<?php

namespace App\Livewire;

use App\Models\WorkLocation;
use Livewire\Component;

class WorkLocationCrud extends Component
{
    public $location, $location_id;

    protected $rules = [
        'location' => 'required',
    ];

    public function save()
    {
        $this->validate();

        WorkLocation::Create([
            'location' => $this->location
        ]);

        $this->resetForm();
    }

    public function delete($id)
    {
        WorkLocation::find($id)->delete();
    }

    public function resetForm()
    {
        $this->location = '';
        $this->location_id = null;
    }

    public function render()
    {
        return view('livewire.work-location-crud',
    [
        'locations' => WorkLocation::orderBy('location', 'asc')->get()
    ]);
    }

}