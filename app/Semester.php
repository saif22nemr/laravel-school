<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $table = 'semesters';
    protected $fillable = [
      'type', 'start_date', 'end_date', 'title', 'academic_year_id','status'
    ];
    protected $hidden = [
      'pivot'
    ];
    public function schedules(){
      return $this->hasMany('App\Schedule','semester_id');
    }
    public function academicYears(){
      return $this->belongsTo('App\AcademicYear','academic_year_id');
    }
    public function teachers(){
      return $this->belongsToMany('App\Teacher','teacher_course_semester','semester_id','teacher_id')->withTimestamps();
    }
    public function courses(){
      return $this->belongsToMany('App\Course','teacher_course_semester','semester_id','course_id')->withTimestamps();
    }
    public function students(){
      return $this->belongsToMany('App\Student','student_level_semester', 'semester_id', 'student_id')->withTimestamps();
    }
    public function levels(){
      return $this->belongsToMany('App\Level','student_level_semester','semester_id','level_id')->withTimestamps();
    }
    public function exams(){
      return $this->hasMany('App\Exam','semester_id');
    }


}
