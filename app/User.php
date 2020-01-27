<?php

namespace App;


use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *  userGroup: [
            1 : admin,
            2 : teacher,
            3 : student, 
            4 : parent,
        ]
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'active', 'address', 'fullname', 'birthday', 'userGroup','image'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
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
    public function phones(){
      return $this->hasMany('App\Phone','user_id');
    }
    public function degrees(){
      return $this->hasMany('App\Degree','student_id');
    }
    
    

}
