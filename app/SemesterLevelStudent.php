<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SemesterLevelStudent extends Model
{
    protected $table = 'student_level_semester';
    protected $fillable = [
    	'student_id', 'semester_id', 'level_id'
    ];

    public function students(){
    	return $this->belongsTo('App\Student','student_id');
    }
    public function semesters(){
    	return $this->belongsTo('App\Semester','semester_id');
    }
    public function levels(){
    	return $this->belongsTo('App\Leve','level_id');
    }
}
