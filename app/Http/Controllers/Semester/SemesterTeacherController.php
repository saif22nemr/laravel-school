<?php

namespace App\Http\Controllers\Semester;

use App\Http\Controllers\ApiController;
use App\Semester;
use App\Teacher;
use Illuminate\Http\Request;

class SemesterTeacherController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Semester $semester)
    {
        $teachers = $semester->teachers()->with('info')
        ->with('courses')->get()->unique('id')->values();
        return $this->showAll($teachers);
    }

    public function show(Semester $semester,Teacher $teacher)
    {
        if($semester->teachers->contains($teacher))
            return $this->showOne($teachers = $semester->teachers()->with('info')
        ->with('courses')->first());
        return $this->errorResponse('The semester not contain this teacher',422);
    }

}
