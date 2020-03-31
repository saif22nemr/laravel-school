<?php

namespace App\Http\Controllers\Exam;

use App\Exam;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class ExamCourseController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Exam $exam)
    {
        $courses = $exam->courses;
        return $this->showAll($courses);
    }

}
