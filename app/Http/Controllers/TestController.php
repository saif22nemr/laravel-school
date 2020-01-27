<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Course;
use App\Exam;
use App\Fullyear;
use App\Level;
use App\Schedule;
use App\ScheduleDate;
use App\Semester;
use App\Student;
use App\Teacher;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TestController extends Controller
{
    public function test(){
      
      return $val;
    }
    private function defineGroupUser(){
    	$users = User::all();
    	foreach ($users as $index => $user) {
    		$teacher = $user->teachers()->first();
    		if(isset($teacher->titleJob)){
    			if($teacher->admin == 1 )
	    			$user->userGroup = 1; //that meadn : admin
	    		else
	    			$user->userGroup = 2; //and that mean : teacher
    		}else
    			$user->userGroup = 3; // that mean : student
    		if($user->password == 'password')
    			$user->password = Hash::make('password');
    		$user->save();
    	}
    }
}
