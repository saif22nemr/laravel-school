<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\addGlobalScope;
use Illuminate\Database\Eloquent\Model;

class Admin extends Employee
{
  // userGroup : 1
  protected $table = 'employees';
  protected static function boot()
  {
      parent::boot();
      static::addGlobalScope(function ($query) {
          $query->where('admin',1);
      });

  }
  public function schedules(){
      return $this->hasMany('App\Schedule' ,'created_by');
  }
  public function scheduleSemesters(){
      return $this->belongsToMany('App\Semester','schedules','created_by','semester_id');
  }
  public function exams(){
      return $this->hasMany('App\Exam' ,'created_by');
  }

}
