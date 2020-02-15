<?php

namespace App\Http\Controllers\Semester;

use App\Http\Controllers\ApiController;
use App\Level;
use App\Semester;
use App\SemesterLevelStudent;
use App\Student;
use Illuminate\Http\Request;

class SemesterLevelStudentController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Semester $semester, Level $level)
    {
        $students = SemesterLevelStudent::where('semester_id',$semester->id)->where('level_id',$level->id)->with('students')->get()->pluck('students');
        return $this->showAll($students);
    }

    public function store(Request $request, Semester $semester, Level $level)
    {
        //it will add and delete that not exit in the request
        $request->validate([
            'student' => 'required|array'
        ]);
        $students = Student::all();
        //check all id has student
        foreach ($request->student as $index => $student) {
            //check if exist in database
            if(!$students->contains($student))
                return $this->errorResponse('There no student have this id',422);
            //check if exist to another level
            if($semester->levels()->where('id','!=',$level->id)->first()->students->contains($student))
                return $this->errorResponse('There student recorded for another level',422);
        }
        //delete all student from the level
        $semester->levels()->detach($level->id);
        foreach ($request->student as $index => $studentId) {
            $semester->students()->attach($studentId,['level_id'=>$level->id]);
        }
        $students = $semester->levels->where('id',$level->id)->first()->students;
        return $this->showAll($students);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function show(Semester $semester,Level $leve, Student $student)
    {
        if($semester->levels->contains($level))
            if($level->students->contains($student))
                return $this->showOne($student);
    }

    public function destroy(Semester $semester,Level $level, Student $student)
    {
        if($semester->levels->contains($level))
            if($level->students->contains($student)){
                $semester->students()->wherePivot('level_id',$level->id)->detach($student->id);
                return $this->showOne($student);
            }
        return $this->errorResponse('The student not record for this semester and level too',422);
    }
}
