<?php

namespace App\Http\Controllers\Schedule;

use App\Http\Controllers\ApiController;
use App\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $scheduleWithDate = Schedule::whereHas('coursesDate')->with('coursesDate')->get();
        return $this->showAll($scheduleWithDate);
    }

    public function show(Schedule $schedule)
    {
        $schedule->coursesDate;
        return $this->showOne($schedule);
    }

    
}
