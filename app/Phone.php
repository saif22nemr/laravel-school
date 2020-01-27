<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    protected $table = 'phones';
    protected $fillable = [
      'user_id','phoneNumber'
    ];
    public $timestamps = false;
    public function users(){
      $this->belongsTo('App\User' ,'user_id');
    }
}
