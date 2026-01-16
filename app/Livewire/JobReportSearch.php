<?php

// namespace App\Livewire;

// use Livewire\Component;
// use App\Models\JobReport;

// class JobReportSearch extends Component
// {
//     public $date_from;

//     public $date_to;

//     public function render()
//     {
//         // Render job report search by date between
//         $reportQuery = JobReport::query();

//         if ($this->date_from && $this->date_to) {
//             $reportQuery->whereBetween('created_at', [$this->date_from, $this->date_to]);
//         }

//         $reports = $reportQuery->get();

//         return view('livewire.job-report-search', compact('reports'));
//     }
// }
