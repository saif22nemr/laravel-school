<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Degree extends Model
{
    protected $table = 'exam_student_degree';
    protected $fillable = [
      'degree', 'exam_id', 'student_id'
    ];
    protected $hidden = [
      'pivot'
    ];
    public function exams(){
      return $this->belongsTo('App\ExamCourse','exam_id');
    }
    public function students(){
      return $this->belongsTo('App\Student','student_id');
    }
}
