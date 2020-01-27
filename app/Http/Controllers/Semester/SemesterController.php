<?php

namespace App\Http\Controllers\Semester;

use App\Http\Controllers\ApiController;
use App\Semester;
use Illuminate\Http\Request;

class SemesterController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $semesters = Semester::all();
        return $this->showAll($semesters);
    }

    
    public function show(Semester $semester)
    {
        return $this->showOne($semester);
    }

}
