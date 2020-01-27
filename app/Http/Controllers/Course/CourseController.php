<?php

namespace App\Http\Controllers\Course;

use App\Course;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class CourseController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses = Course::all();
        return $this->showAll($courses);
    }

    public function show(Course $course)
    {
        return $this->showOne($course);
    }

}
