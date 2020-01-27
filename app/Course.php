<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'courses';
    protected $fillable = [
      'title','description', 'level_id'
    ];
    protected $hidden = [
      'pivot'
    ];
    public function exams(){
      return $this->hasMany('App\ExamCourse','exam_id');
    }
    public function levels(){
      return $this->belongsTo('App\Level','level_id');
    }
    public function teachers(){
      return $this->belongsToMany('App\Teacher','teacher_course_semester','course_id','teacher_id')->withTimestamps();
    }
    public function semesters(){
      return $this->belongsToMany('App\Semester','teacher_course_semester','course_id','semester_id')->withTimestamps();
    }
    public function scheduleDetails(){
      return $this->hasMany('App\ScheduleDate','course_id');
    }
    public function schedules(){
      return $this->belongsToMany('App\Schedule','schedule_course','course_id','schedule_id');
    }
}
