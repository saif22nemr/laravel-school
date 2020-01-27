<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Course;
use App\Exam;
use App\ExamCourse;
use App\Http\Controllers\ApiController;
use App\Semester;
use App\Teacher;
use Illuminate\Http\Request;

class AdminSemesterExamController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Admin $admin , Semester $semester)
    {
        $exams = $admin->exams()->where('semester_id',$semester->id)->with('details')->get();
        if($exams == []) return $this->showAll([]);
        foreach($exams as $index => $exam){
            foreach($exams[$index]['details'] as $ind => $detail){
                $exams[$index]['details'][$ind]['course'] = Course::find($detail->course_id);
                $exams[$index]['details'][$ind]['teacher'] = Teacher::find($detail->teacher_id);
            }
        }
        return $this->showAll($exams);
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Admin $admin, Semester $semester)
    {
        $request->validate([
            'title' => 'required|min:2|max:150',
            'examDate' => 'required|array',
            'course_id' => 'required|array',
            'maxDegree' => 'required|array',
            'timer' => 'required|array',
            'teacher_id' => 'required|array',
            'type' => 'required|in:final,quiz'
        ]);
        //if the type == final : it must only one exam in this semester has type final.
        if($request->type == 'final')
        $check = $semester->exams->where('type','final')->first();
        if(isset($check->id))
            return $this->errorResponse('The exam must be one final exam',422);
        //if the type final, it must contain all course from this semester
        $availableCourses = $semester->courses->unique('id')->values();
        if($request->type == 'final' and count($availableCourses) != count($request->course_id))
            return $this->errorResponse('The exam must be contain all course recorded from this semester '.count($availableCourses),422);
        //more check for request.
        if(count($request->examDate) == count($request->course_id) and count($request->course_id) == count($request->maxDegree) and count($request->timer) == count($request->maxDegree) and count($request->teacher_id) == count($request->timer)){
            //the first loop validate the input
            for ($i=0; $i <count($request->timer) ; $i++) { 
                if(!isset($request->examDate[$i]) or !$this->validateDate($request->examDate[$i])):
                    return $this->errorResponse('There something worng in exam date format',422);
                elseif(!isset($request->timer[$i]) or !$this->validateDate($request->timer[$i],'H:i:s')):
                    return $this->errorResponse('The timer invalid format',422);
                elseif(!isset($request->maxDegree[$i]) or ! is_numeric($request->maxDegree[$i]) and $maxDegree[$i] > 0):
                    return $this->errorResponse('The max degree it must be integer and positive value',422);
                elseif(!is_numeric($request->course_id[$i]) or !$semester->courses->contains($request->course_id[$i])):
                    return $this->errorResponse('The course id must be integer and existing in this semester',422);
                endif;
            }
            $exam = Exam::create([
                'title' => $request->title,
                'created_by' => $admin->id,
                'semester_id' => $semester->id,
                'type' => $request->type,
            ]);
            for ($i=0; $i < count($request->examDate); $i++) { 
                ExamCourse::create([
                    'course_id' => $request->course_id[$i],
                    'examDate' => $request->examDate[$i],
                    'maxDegree' => $request->maxDegree[$i],
                    'timer' => $request->timer[$i],
                    'exam_id' => $exam->id,
                    'teacher_id' => $request->teacher_id[$i],
                ]);
            }
            $exam->details;
            return $this->showOne($exam);
        }
        return $this->errorResponse('The invalid input',422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function show(Admin $admin,Semester $semester, Exam $exam)
    {
        if($admin->exams->contains($exam))
            if($semester->exams->contains($exam)){
                $exams = $exam->with('details')->first();
                    foreach($exams['details'] as $ind => $detail){
                        $exams['details'][$ind]['course'] = Course::find($detail->course_id);
                        $exams['details'][$ind]['teacher'] = Teacher::find($detail->teacher_id);
                    }
                return $this->showOne($exams);
            }
        return $this->errorResponse('The semester or the admin not contain this exam',422);
    }

    
    public function update(Request $request, Admin $admin, Semester $semester, Exam $exam)
    {
        if(!$admin->exams->contains($exam) or !$semester->exams->contains($exam))
            return $this->errorReponse('The exam maybe not available to this admin or this semester',422);
        $request->validate([
            'title' => 'required|min:2|max:150',
            'examDate' => 'required|array',
            'course_id' => 'required|array',
            'maxDegree' => 'required|array',
            'timer' => 'required|array',
            'teacher_id' => 'required|array',
        ]);
        if(count($request->course_id) > count(array_unique($request->course_id)))
            return $this->errorReponse('There duplicated course id',422);
        //check request.
        if($exam->type == 'final'){
            $allAvailableCourse = $semester->courses->unique('id')->values();
            if(count($allAvailableCourse) != count($request->course_id))
                return $this->errorResponse('This exam must be contain all courses from this semester',422);
        }
        $examDetails = $exam->details;
        if(count($request->examDate) == count($request->course_id) and count($request->course_id) == count($request->maxDegree) and count($request->timer) == count($request->maxDegree) and count($request->teacher_id) == count($request->timer)){
            //the first loop validate the input
            for ($i=0; $i <count($request->timer) ; $i++) { 
                if(!isset($request->examDate[$i]) or !$this->validateDate($request->examDate[$i])):
                    return $this->errorResponse('There something worng in exam date format',422);
                elseif(!isset($request->timer[$i]) or !$this->validateDate($request->timer[$i],'H:i:s')):
                    return $this->errorResponse('The timer invalid format',422);
                elseif(!isset($request->maxDegree[$i]) or ! is_numeric($request->maxDegree[$i]) and $maxDegree[$i] > 0):
                    return $this->errorResponse('The max degree it must be integer and positive value',422);
                elseif(!is_numeric($request->course_id[$i]) or !$semester->courses->contains($request->course_id[$i]) or !$examDetails->where('course_id')):
                    return $this->errorResponse('The course id must be integer and existing in this semester',422);
                endif;
            }
            $exam->fill([
                'title' => $request->title,
            ]);
            $exam->save();
            $courseId = $examDetails->pluck('course_id');
            //first loop for delete that not exist in the request
            foreach ($courseId as $index => $id) {
                if(!in_array($id, $request->course_id)){
                    $exam->details->where('course_id',$id)->first()->delete();
                }
            }

            //the second step to add new course or update that existed before.
            for ($i=0; $i < count($request->course_id); $i++) { 
                $examCourse = $examDetails->where('course_id',$request->course_id[$i])->first();
                if(isset($examCourse->id)){ //for update
                    $examCourse->fill([
                        'maxDegree' => $request->maxDegree[$i],
                        'examDate' => $request->examDate[$i],
                        'timer' => $request->timer[$i],
                        'teacher_id' => $request->teacher_id[$i]
                    ]);
                    $examCourse->save();
                }elseif($exam->type == 'quiz'){//for create
                    ExamCourse::create([
                        'course_id' => $request->course_id[$i],
                        'examDate' => $request->examDate[$i],
                        'maxDegree' => $request->maxDegree[$i],
                        'timer' => $request->timer[$i],
                        'exam_id' => $exam->id,
                        'teacher_id' => $request->teacher_id[$i],
                    ]);
                }
            }
            $return = $exam->where('id',$exam->id)->with('details')->first();
            return $this->showOne($return);
        }
        return $this->errorResponse('The invalid input',422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admin $admin,Semester $semester, Exam $exam)
    {
        if($admin->exams->contains($exam))
            if($semester->exams->contains($exam)){
                $exam->examDetails;
                $exam->delete();
                return $this->showOne($exam);
            }
        return $this->errorResponse('The exam maybe not exist in this semester or admin',422);
    }
}
