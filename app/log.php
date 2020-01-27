<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class log extends Model
{
    protected $table = 'log';
    protected $fillable = [
      'loginDate', 'admin_side', 'user_id'
    ];
    
    public function users(){
      return $this->belongsTo('App\User','user_id');
    }
}
