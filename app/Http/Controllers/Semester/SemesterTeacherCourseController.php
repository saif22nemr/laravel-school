<?php

namespace App\Http\Controllers\Semester;

use App\Course;
use App\Http\Controllers\ApiController;
use App\Semester;
use App\SemesterCourseTeacher;
use App\Teacher;
use Illuminate\Http\Request;

class SemesterTeacherCourseController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Semester $semester, Teacher $teacher)
    {
        if(!$semester->teachers->contains($teacher))
            return $this->errorResponse('The semester not contain this teacher',422);
        //$courses = $teacher->courses()->whereHas('levels')->with('levels')->get();
        $courses = SemesterCourseTeacher::where('semester_id',$semester->id)->where('teacher_id',$teacher->id)->with('courses')->get()->pluck('courses');
        return $this->showAll($courses);
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Semester $semester, Teacher $teacher)
    {
        //this method for store and update too.
        $request->validate([
            'course' => 'required|array'
        ]);
        //check all course id is exist
        foreach ($request->course as $index => $value) {
            $check = Course::where('id',$value)->first();
            if(!isset($check->id))
                return $this->errorResponse('There no course contain this id',422);
        }
        //delete all course from this teacher
        $semester->teachers()->detach($teacher->id);
        foreach ($request->course as $key => $value) {
            $semester->teachers()->attach($teacher->id,['course_id'=>$value]);
        }
        $courses = $teacher->courses()->with('levels')->get();
        return $this->showAll($courses);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function show(Semester $semester,Teacher $teacher, Course $course)
    {
        if($semester->teachers->contains($teacher))
            if($teacher->courses->contains($course))
                return $this->showOne($teacher->courses($course)->with('levels')->first());
        return $this->errorResponse('The semester and teacher not record this course',422);
    }

   
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function destroy(Semester $semester,Teacher $teacher, Course $course)
    {
        if($semester->teachers->contains($teacher))
            if($teacher->courses->contains($course)){
                $semester->courses()->wherePivot('teacher_id',$course->id)->detach($course->id);
                return $this->showOne($course);
            }
        return $this->errorResponse('The semester and teacher not contain this course',422);
    }
}
