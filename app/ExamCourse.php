<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExamCourse extends Model
{
    protected $table = 'exam_course';
    protected $fillable = [
    	'course_id', 'exam_id','examDate' , 'timer', 'teacher_id', 'maxDegree'
    ];

    public function degrees(){
    	return $this->belongsTo('App\Degree','exam_id');
    }
    public function courses(){
    	return $this->belongsTo('App\Course','course_id');
    }
    public function exams(){
    	return $this->belongsTo('App\Exam','exam_id');
    }
    public function teachers(){
        return $this->belongsTo('App\Teacher','teacher_id');
    }
}
