<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $table = 'academic_year';
    protected $fillable = [
      'title', 'status'
    ];
    public function semesters(){
      return $this->hasMany('App\Semester', 'academic_year_id');
    }

}
