<?php

namespace App\Http\Controllers\Semester;

use App\Course;
use App\Http\Controllers\ApiController;
use App\Semester;
use Illuminate\Http\Request;

class SemesterCourseController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Semester $semester)
    {
        $courses = $semester->courses()->with('levels')->with('teachers')->get()->unique('id')->values();
        return $this->showAll($courses);
    }

    
    public function show(Semester $semester,Course $course)
    {
        if($semester->courses->contains($course)){
            $course = $course->where('id',$course->id)->with('levels')->with('teachers')->first();
            return $this->showOne($course);
        }
        return $this->errorResponse('The semester not contain this course');
    }

}
