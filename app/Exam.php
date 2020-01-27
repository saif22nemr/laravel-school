<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $table = 'exams';
    protected $fillable = [
      'title', 'semester_id', 'created_by','type'
    ];
    public function courses(){
      return $this->belongsToMany('App\Course','exam_course','exam_id','course_id');
    }
    public function details(){
      return $this->hasMany('App\ExamCourse','exam_id');
    }
    public function employees(){
      return $this->belongsTo('App\Employee','created_by');
    }
    public function semesters(){
      return $this->belongsTo('App\Semester','semester_id');
    }
}
