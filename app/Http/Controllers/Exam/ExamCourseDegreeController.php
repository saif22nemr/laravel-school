<?php

namespace App\Http\Controllers\Exam;

use App\Course;
use App\Exam;
use App\ExamDegree;
use App\Http\Controllers\ApiController;
use App\Semester;
use App\SemesterLevelStudent;
use App\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ExamCourseDegreeController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Exam $exam,Course $course)
    {
        $examDetail = $exam->details->where('course_id',$course->id)->first();
        if(!$exam->courses->contains($course) or !isset($examDetail->id))
            return $this->errorResponse('The exam does not contain this course',422);
        $degree = Student::whereHas('degrees')->with(['degrees'=>function($d) use($examDetail)
        {
            $d->where('exam_id',$examDetail->id);
        }])->get();
        return $degree;
        return $this->showAll($degree);
    }
    //this store for insert and update
    public function store(Request $request, Exam $exam,Course $course)
    {
        //check if the exam from this semester.
        if(!$exam->courses->contains($course))
            return $this->errorResponse('The exam does not contain this course',422);

        $request->validate([
            'student' => 'required|array',
            'degree'  => 'required|array'
        ]);
        //check every student and degree
        $students = $request->student;
        $degrees  = $request->degree;
        if(count($students) > count(array_unique($students)))
            return $this->errorResponse('There student id duplicated !',422);
        $level = $course->levels->first();
        $examDetail = $exam->details->where('course_id',$course->id)->first();
        $existDegrees = $examDetail->degrees;
        // //for check if store this course for this exam before or not
        // if(isset($check->id))
        //     return $this->errorResponse('The exam areadly have degrees',422);
        $avaliableStudent = SemesterLevelStudent::where('semester_id',$exam->semester_id)->where('level_id',$level->id)->with('students')->get()->pluck('students.id')->toArray();
        
        if(count($avaliableStudent) == count($students) and count($students) == count($degrees))
        {
            //first loop for check
            for($i=0; $i < count($students); $i++){
                if(!in_array($students[$i],$avaliableStudent))
                    return $this->errorResponse('There student have not this exam',422);
                if($degrees[$i] < 0 or $degrees[$i] > $examDetail->maxDegree)
                    return $this->errorResponse('There invalid degree',422);
            }

            //the second loop for add
            for ($i=0; $i < count($students); $i++) {
                $temp = $existDegrees->where('student_id',$students[$i])->first();
                if(isset($temp->id)){
                    $temp->degree = $degrees[$i];
                    $temp->save();
                }else{
                    ExamDegree::create([
                        'student_id' => $students[$i],
                        'exam_id'    => $examDetail->id,
                        'degree'     => $degrees[$i],
                    ]);
                }   
            }
            $degree = Student::whereHas('degrees')->with(['degrees'=>function($d) use($examDetail)
            {
                $d->where('exam_id',$examDetail->id);
            }])->get();
            return $this->showAll($degree);
        }
        return $this->errorResponse('Invalid Input, it must be the student input equal all student register for this course',422);
    }

   
}
