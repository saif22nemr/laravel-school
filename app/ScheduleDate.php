<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduleDate extends Model
{
    protected $table = 'schedule_course';
    protected $fillable = [
      'datetime', 'schedule_id', 'course_id'
    ];
    public function schedules(){
      return $this->belongsTo('App\Schedule','schedule_id');
    }
    public function courses(){
      return $this->belongsTo('App\Course','course_id');
    }
}
