<?php

namespace App\Http\Controllers\Semester;

use App\Course;
use App\Http\Controllers\ApiController;
use App\Schedule;
use App\Semester;
use Illuminate\Http\Request;

class SemesterCourseScheduleController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Semester $semester, Course $course)
    {
        $courseDate = $semester->schedules()->with('coursesDate')->get()->pluck('coursesDate')->collapse()->where('course_id',$course->id)->values();
        foreach ($courseDate as $index => $schedule) {
            $schedule->schedules;
        }
        return $this->showAll($courseDate);
    }

    public function show(Semester $semester,Course $course, Schedule $schedule)
    {
        if($semester->schedules->contains($schedule))
            if($schedule->courses->contains($course)){
                $val = $schedule->coursesDate->where('course_id',$course->id)->first();
                return $this->showOne($val);
            }
        return $this->errorResponse('The semester not contain this schedule or not contain this course',422);
    }

    
}
