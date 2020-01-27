<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Schedule;

class Schedule extends Model
{
  protected $table = 'schedules';
  protected $fillable = [
    'title', 'semester_id', 'created_by','type'
  ];
  protected $hidden = [
    'pivot'
  ];
  public function coursesDate(){
    return $this->hasMany('App\ScheduleDate','schedule_id');
  }
  public function courses(){
    return $this->belongsToMany('App\Course','schedule_course','schedule_id','course_id');
  }
  public function teachers(){
    return $this->belongsTo('App\Teacher','created_by');
  }
  public function semesters(){
    return $this->belongsTo('App\Semester','semester_id');
  }

}
