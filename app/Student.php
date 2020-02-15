<?php

namespace App;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\addGlobalScope;
use Illuminate\Database\Eloquent\Model;
use App\User;

class Student extends User
{
  protected $table = 'users';
  protected static function boot()
  {
      parent::boot();
      static::addGlobalScope(function ($query) {
          $query->where('userGroup',3);
      });

  }
   protected $hidden = [
        'password', 'remember_token', 'pivot'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
  
  public function logs(){
    return $this->hasMany('App\Log','user_id');
  }
  
    public function levels(){
        return $this->belongsToMany('App\Level','student_level_semester','student_id','level_id')->withTimestamps();
    }
    public function semesters(){
        return $this->belongsToMany('App\Semester','student_level_semester','student_id','semester_id')->withTimestamps();
    }
    public function degrees(){
        return $this->hasMany('App\ExamDegree','student_id');
    }
}
