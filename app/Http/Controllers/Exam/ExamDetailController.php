<?php

namespace App\Http\Controllers\Exam;

use App\Exam;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class ExamDetailController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Exam $exam)
    {
        $details = $exam->details;
        return $this->showAll($details);
    }

}
