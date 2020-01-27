<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $table = 'levels';
    protected $fillable = [
      'title','level_number' ,'stage'
    ];
    protected $hidden = [
      'pivot'
    ];
    public function students(){
      return $this->belongsToMany('App\Student','student_level_semester','level_id','student_id');
    }
    
    public function courses(){
      return $this->hasMany('App\Course','level_id');
    }
    public function semesters(){
      return $this->belongsToMany('App\Semester','student_level_semester','level_id','semester_id');
    }
}
