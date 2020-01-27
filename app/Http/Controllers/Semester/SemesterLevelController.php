<?php

namespace App\Http\Controllers\Semester;

use App\Http\Controllers\ApiController;
use App\Level;
use App\Semester;
use Illuminate\Http\Request;

class SemesterLevelController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Semester $semester)
    {
        $levels = $semester->levels()->with('students')->get()->unique('id')->values();
        return $this->showAll($levels);
    }
    public function show(Semester $semester, Level $level)
    {
        if($semester->levels->contains($level)){
            $levels = $semester->levels()->where('id',$level->id)->with('students')->first();
            return $this->showOne($levels);
        }
        return $this->errorResponse('The semester not contain this level',422);
    }

}
