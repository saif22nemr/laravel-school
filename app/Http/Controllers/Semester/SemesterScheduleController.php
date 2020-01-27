<?php

namespace App\Http\Controllers\Semester;

use App\Http\Controllers\ApiController;
use App\Schedule;
use App\Semester;
use Illuminate\Http\Request;

class SemesterScheduleController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Semester $semester)
    {
        $schedules = $semester->schedules()->with('coursesDate')->get();
        return $this->showAll($schedules);
    }

    
    public function show(Semester$semester, Schedule $schedule)
    {
        if($semester->schedules->contains($schedule)){
            $schedule->coursesDate;
            return $this->showOne($schedule);
        }
    }

}
