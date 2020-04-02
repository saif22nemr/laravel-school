<?php

namespace App\Http\Controllers;

use App\Log;
use App\Exam;
use App\User;
use App\Admin;
use App\Level;
use App\Course;
use App\Setting;
use App\Student;
use App\Teacher;
use App\Fullyear;
use App\Schedule;
use App\Semester;
use Carbon\Carbon;
use App\AcademicYear;
use App\ScheduleDate;
use Illuminate\Http\Request;
use App\SemesterLevelStudent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class TestController extends Controller
{
    public function destroyByTime(String $type = '',int $value=0){
        //all steps for delete by time
        // 1- type  : [hours, week, year, executly time, interval by date]
        if($value < 0 or !in_array($type, ['hours','days','weeks','month','years',''])){
            return "Invalid Value";
        }
        elseif($value == 0 and $type == ''){ // on this stutiation will delete check and delete
            //check and delete
            $check = Setting::where('title', 'last_date_destroy_logs')->first();
            if($check == null) return false;
            else{
                $currentDate = Carbon::now();
                //$currentDate->addHours(2);
                $lastDestroyDate = Carbon::createFromFormat('Y-m-d H:i:s',$check->value,'Africa/Cairo');
                //echo $lastDestroyDate;
                //$lastDestroyDate->addSeconds(10);
                $rtype = Setting::where('title','type_destroy_logs')->first();
                $rvalue = Setting::where('title','value_destroy_logs')->first();
                //echo $rvalue->value;
                if($rtype->value == 'hours'):
                    $lastDestroyDate = $lastDestroyDate->addHours($rvalue->value);
                    if($currentDate->greaterThan($lastDestroyDate) == 1)://that will delete all
                        $check->value = $currentDate->toDateTimeString();
                        Log::all()->delete();
                    endif;
                elseif($rtype->value == 'days'):
                    $lastDestroyDate->addDays($rvalue->value);
                    if($currentDate->greaterThan($lastDestroyDate) <= 0)://that will delete all
                        $check->value = $currentDate->toDateTimeString();
                        Log::all()->delete();
                    endif;
                elseif($rtype->value == 'weeks'):
                    $lastDestroyDate->addWeeks($rvalue->value);
                    if($currentDate->greaterThan($lastDestroyDate) <= 0)://that will delete all
                        $check->value = $currentDate->toDateTimeString();
                        Log::all()->delete();
                    endif;
                elseif($rtype->value == 'month'):
                    $lastDestroyDate->addMonth($rvalue->value);
                    if($currentDate->greaterThan($lastDestroyDate) <= 0)://that will delete all
                        $check->value = $currentDate->toDateTimeString();
                        Log::all()->delete();
                    endif;
                elseif($rtype->value == 'years'):
                    $lastDestroyDate->addYears($rvalue->value);
                    if($currentDate->greaterThan($lastDestroyDate) <= 0)://that will delete all
                        $check->value = $currentDate->toDateTimeString();
                        Log::all()->delete();
                    endif;
                endif;
                //don't forget update the last delete logs
                $check->save();

                return true;
            }
        }
        elseif($value == 0 or $type == '') // else
        {
            //for return error message
            echo 'message error';
        }else{// store and update
            $check = Setting::where('title', 'last_date_destroy_logs')->first();
            if(!isset($check->id)){
                Setting::create([
                    'title'      => 'last_date_destroy_logs',
                    'value'      => Carbon::now('Africa/Cairo')->toDateTimeString(),
                    'description'=> 'The last datetime of delete logs'
                ]);
            }
            //check if exist or not
            $check = Setting::where('title','type_destroy_logs')->first();
            if(isset($check->id)){ //updated
                $check->value = $type;
                $check->save();
            }else{ //stored
                Setting::create([
                    'title'  => 'type_destroy_logs',
                    'value'  => $type,
                    'description'=> 'This type of interval that will delete all logs by it'
                ]);
            }
            //save value
            $check = Setting::where('title','value_destroy_logs')->first();
            if(isset($check->id)){ //updated
                $check->value = $value;
                $check->save();
            }else{ //stored
                Setting::create([
                    'title'  => 'value_destroy_logs',
                    'value'  => $value,
                    'description'=> 'This value time of interval will delete by it'
                ]);
            }
            return 'success update data';
        }
        //return $lastDestroy;
    }
    public function test(){
        $semesterid = Setting::where('title','current_semester')->first()->value;
        // $semesterid = 5;
        if(!isset($semesterid) or !is_numeric($semesterid))
            return $this->errorResponse('You should choose semester and academic year', 422);
        $all = Level::with(['courses' => function( $query) use($semesterid){
            $query->with(['teachers' => function($teacher) use($semesterid){
                $teacher->whereHas('semesters', function($check) use($semesterid){
                    $check->where('id',$semesterid);
                });
            }]);
        }])->get();
        return $all;
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
