<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SemesterCourseTeacher extends Model
{
    protected $table = 'teacher_course_semester';
    protected $fillable = [
    	'teacher_id','course_id','semester_id'
    ];

    public function teachers(){
    	return $this->belongsTo('App\Teacher','teacher_id');
    }
    public function semesters(){
    	return $this->belongsTo('App\Semester','semester_id');
    }
    public function courses(){
    	return $this->belongsTo('App\Course','course_id');
    }
}
