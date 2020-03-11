<?php

namespace App\Http\Controllers;

use App\Exam;
use App\User;
use App\Admin;
use App\Level;
use App\Course;
use App\Student;
use App\Teacher;
use App\Fullyear;
use App\Schedule;
use App\Semester;
use App\AcademicYear;
use App\ScheduleDate;
use Illuminate\Http\Request;
use App\SemesterLevelStudent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestController extends Controller
{
    public function test(){
        $date = '2020-03-31';
        //$val = DB::select("select a.id as academic_id, a.title as academic_title,s.title,s.start_date,s.end_date from academic_year a left join semesters s on( academic_year_id = a.id) order by start_date");
        $val = 'home';
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
