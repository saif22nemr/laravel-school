<?php

namespace App\Http\Controllers\Course;

use App\Course;
use App\Http\Controllers\ApiController;
use App\Semester;
use App\SemesterLevelStudent;
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
        if($semester->courses->contains($course)){
            $level = $course->levels->first();
            if(!$semester->levels->contains($level)) 
                return $this->errorResponse('There no student for this semester',422);
            $students = SemesterLevelStudent::where('semester_id',$semester->id)->where('level_id',$level->id)->with('students')->get()->pluck('students');
            return $this->showAll($students);
        }
        return $this->errorResponse('The semester not contains this course',422);
    }

    
}
