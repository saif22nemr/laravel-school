<?php

namespace App\Http\Controllers\Course;

use App\Course;
use App\Http\Controllers\ApiController;
use App\Semester;
use App\SemesterCourseTeacher;
use App\Teacher;
use Illuminate\Http\Request;

class CourseSemesterTeacherController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Course $course, Semester $semester)
    {
        if(!$semester->courses->contains($course))
            return $this->errorResponse('The semester does not contain this course',422);
        $teacher = SemesterCourseTeacher::where('course_id',$course->id)->where('semester_id',$semester->id)->with('teachers.info')->get()->pluck('teachers');
        return $this->showAll($teacher);
    }

}
