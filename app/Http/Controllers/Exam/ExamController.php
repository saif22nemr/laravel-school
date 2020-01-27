<?php

namespace App\Http\Controllers\Exam;

use App\Course;
use App\Exam;
use App\Http\Controllers\ApiController;
use App\Teacher;
use Illuminate\Http\Request;

class ExamController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $exams = Exam::whereHas('details')->with('details')->get();
        if(count($exams) != 0){
            foreach ($exams as $index => $exam) {
                foreach ($exam['details'] as $key => $value) {
                    $exams[$index]['details'][$key]['course'] = Course::find($value->course_id);
                    $exams[$index]['details'][$key]['teacher'] = Teacher::find($value->teacher_id)->with('info')->first();
                }
            }
        }
        return $this->showAll($exams);
    }

   
    public function show(Exam $exam)
    {
        $exam->details;
        foreach ($exam['details'] as $key => $value) {
            $exam['details'][$key]['course'] = Course::find($value->course_id);
            $exam['details'][$key]['teacher'] = teacher::find($value->teacher_id)->with('info')->first();
        }
        return $this->showOne($exam);
    }

}
