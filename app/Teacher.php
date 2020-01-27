<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Employee
{
    protected $table = 'employees';

    protected static function boot()
  {
      parent::boot();
      static::addGlobalScope(function ($query) {
          $query->where('admin',0);
      });
  }
  	public function courses(){
      return $this->belongsToMany('App\Course' ,'teacher_course_semester','teacher_id','course_id')->withTimestamps();
    }

    public function semesters(){
      return $this->belongsToMany('App\Semester' ,'teacher_course_semester','teacher_id','semester_id');
    }
}
