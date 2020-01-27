<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Teacher;

class Employee extends Model
{
    protected $table = 'employees';
    protected $fillable= [
      'titleJob', 'startDate', 'salary', 'user_id', 'admin'
    ];
    protected $hidden = [
      'admin' , 'pivot'
    ];
    public function info(){
      return $this->belongsTo('App\User' ,'user_id');
    }
    public function phones(){
      return $this->hasMany('App\Phone','user_id','user_id');
    }
    
    public function isTeacher(Teacher $teacher){
      return $teacher->admin == 0? true : false;
    }

}
