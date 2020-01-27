<?php

namespace App\Http\Controllers\Semester;

use App\Course;
use App\Http\Controllers\ApiController;
use App\Semester;
use Illuminate\Http\Request;

class SemesterCourseTeacherController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Semester $semester , Course $course)
    {
        $teacher = $semester->courses($course)->first()->teachers()->with('info')->first();
        return $this->showOne($teacher);
    }

    public function show(Semester $semester)
    {
        //
    }

   
}
