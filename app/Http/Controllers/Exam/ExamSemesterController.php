<?php

namespace App\Http\Controllers\Exam;

use App\Exam;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class ExamSemesterController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Exam $exam)
    {
        $semester = $exam->semesters->first();
        return $this->showOne($semester);
    }

}
