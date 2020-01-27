<?php

namespace App\Http\Controllers\Course;

use App\Course;
use App\Http\Controllers\ApiController;
use App\Semester;
use Illuminate\Http\Request;

class CourseSemesterStudentController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Course $course, Semester $semester)
    {
        if($course->levels()->first()->semesters->contains($semester)){
            $students = $course->levels()->first()->semesters($semester)->first()->students;
            return $this->showAll($students);
        }
        return $this->errorResponse('The semester not contains this course',422);
    }

    
}
