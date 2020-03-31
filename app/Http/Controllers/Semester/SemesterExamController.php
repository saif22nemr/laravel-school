<?php

namespace App\Http\Controllers\Semester;

use App\Exam;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class SemesterExamController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Exam $exam)
    {
        $exams = $semester->exams;
        return $this->showAll($exams);
    }

}
