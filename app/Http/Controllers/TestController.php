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
use App\SemesterLevelStudent;
use App\Student;
use App\Teacher;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestController extends Controller
{
    public function test(){
        
        //$val =  SemesterLevelStudent::where('level_id',6)->where('semester_id',6)->students;
        $val = SemesterLevelStudent::where('semester_id',6)->where('level_id',4)->with('students')->get()->pluck('students');

        //this way for get student by query directly.
        // $val = DB::select('select username,id,email,fullname,address,image birthday from 
        //     student_level_semester sls inner join users st on (st.id = sls.student_id and st.userGroup = 3)
        //     where level_id = 4 and semester_id = 6 
        //     ');

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
