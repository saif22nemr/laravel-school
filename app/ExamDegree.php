<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExamDegree extends Model
{
    protected $table = 'exam_student_degree';
    protected $fillable = [
    	'exam_id', 'degree', 'student_id'
    ];

    public function exams(){
    	return $this->belongsTo('App\ExamCourse','exam_id');
    }
    public function students(){
    	return $this->belongsTo('App\Student','student_id');
    }
}
