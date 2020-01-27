<?php

namespace App\Http\Controllers\Course;

use App\Course;
use App\Http\Controllers\ApiController;
use App\Semester;
use App\Teacher;
use Illuminate\Http\Request;

class CourseSemesterTeacherController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Course $course, Semester $semester)
    {
        $teacher = $semester->courses($course)->first()->teachers()->with('info')->first();
        return $this->showOne($teacher);
    }

}
