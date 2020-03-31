<?php

namespace App\Http\Controllers\Log;

use App\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class LogController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //return $request->all();
        $request->validate([
            'searchby' => 'string|in:id,fullname,year,created_at',
            'sortby'   => 'string|in:id,fullname,created_at',
            'orderby'  => 'string|in:desc,asc'
        ]);
        //return $this->successResponse($request->all(),403);
        $logs = Log::join('users','users.id','logs.user_id')->select(['logs.id','logs.user_id','users.fullname','users.userGroup','logs.created_at']);
        if(isset($request->search) and isset($request->searchby)){
            if($request->searchby == 'id'){
                if(!is_numeric($request->search))
                    return $this->errorResponse('The search field should be integer',422);
                $logs = $logs->where('logs.id',$request->search);
            }
            elseif($request->searchby == 'fullname'){
                $logs = $logs->where('users.fullname','%'.$request->search.'%');
            }
            elseif($request->searchby == 'year'){
                if(strlen($request->search) != 4 or !is_numeric($request->search))
                    return $this->errorResponse('The search field should be integer',422);
                $logs = $logs->where('logs.created_at', '%'.$request->search.'%');
            }
            elseif($request->searchby == 'created_at'){
                $date = checkDate($request->search);
                if($date == false)
                    return $this->errorResponse('The search field should be date formatt',422);
                $logs = $logs->where('logs.created_at', '%'.$date.'%');
            }
        }
        //Sorting
        $checkSort = false;
        if(isset($request->sortby) and isset($request->orderby)){
            $checkSort = true;
            $sort = $request->sortby == 'fullname'? 'users.fullname' : 'logs.'.$request->sortby;
            $logs = $logs->orderBy($sort, $request->orderby);
        }
        //return $this->errorResponse($logs->get(),403);
        if(!$checkSort)
            $logs = $logs->orderBy('created_at','desc');
        $logs = $logs->get();
        return $this->showAll($logs);
        //return "{'test'=>'Message test'}";
    }

    public function destroy(Log $log)
    {
        $log->delete();
        return $this->showOne($log);
    }
    public function destroyByTime(String $type = '',int $value=0){
        //all steps for delete by time
        // 1- type  : [hours, week, year, executly time, interval by date]
        if($value < 0 or !in_array($type, ['hours','days','weeks','month','years',''])){
            return $this->errorResponse('Invalid entries',422);
        }
        elseif($value == 0 and $type == ''){ // on this stutiation will delete check and delete
            //check and delete
            if(1){
                $check = Setting::where('title', 'last_date_destroy_logs')->first();
                if($check == null) {
                    //check if there last date of delete all logs
                    //if not found, create anow one as default
                    Setting::create([
                        'title'      => 'last_date_destroy_logs',
                        'value'      => Carbon::now()->toDateTimeString(),
                        'description'=> 'The last datetime of delete logs',
                    ]);
                }
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

                return $this->successReponse('Successfull Delete All Logs',200);
            }
        }
        elseif($value == 0 or $type == '') // else
        {
            //for return error message
            return $this->errorResponse('Invalid Input',422);
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
            return $this->successResponse('Successfull update datetime');
        }
        //return $lastDestroy;
    }
}
