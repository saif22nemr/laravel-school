<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Http\Controllers\ApiController;
use App\Schedule;
use App\ScheduleDate;
use App\Semester;
use Illuminate\Http\Request;

class AdminSemesterScheduleController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Admin $admin)
    {
        $schedule = $admin->schedules()->with('coursesDate')->get();
        return $this->showAll($schedule);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Admin $admin , Semester $semester)
    {
        $request->validate([
            'title' => 'required|min:2|max:150',
            'course' => 'required|array',
            'datetime' => 'required|array',
            'type' => 'required|in:semester,other'
        ]);
        //for duplicated
        if(count($request->course) > count(array_unique($request->course)))
            return $this->errorResponse('There duplicated course',422);
        if($request->type == 'semester'){
            $check = Schedule::where('semester_id',$semester->id)->where('type','semester')->first();
            if(isset($check->id))
                return $this->errorResponse('The semester really have schedule');
            //check that every course in semester in the request
            $courses = $semester->courses->unique('id');
            if(count($request->course) != count($request->datetime))
                return $this->errorResponse('The schedule must be all course that recorded to this semester'.count($courses),422);
        }
        if(count($request->datetime) != count($request->course))
            return $this->errorResponse('The schedule must be all course that recorded to this semester',422);
        $course = $request->course;
        $datetime = $request->datetime;

        for ($i=0;$i < count($request->course); $i++) {
            //check course
            if(!is_numeric($course[$i])) // check the course must be integer
                return $this->errorResponse('The course must be integer',422);
            if(!$this->validateDate($datetime[$i])) //check the datetime must be datetime
                return $this->errorResponse('The datetime is invalid',422);
            if(!$semester->courses->contains($course[$i])) //check if the recorded for any teacher.
                return $this->errorResponse('The semester not contain this course '.$course[$i],422);
        }
        $data = $request->only(['title','type']);
        $data['created_by'] = $admin->id;
        $data['semester_id'] = $semester->id;
        $schedule = Schedule::create($data);
        for ($i=0;$i < count($request->course); $i++) {
            ScheduleDate::create([
                'course_id' => $course[$i],
                'datetime' => $datetime[$i],
                'schedule_id' => $schedule->id,
            ]);
        }
        $schedule->coursesDate;
        return $this->showOne($schedule);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function show(Admin $admin,Semester $semester,Schedule $schedule)
    {
        if($admin->scheduleSemesters->contains($semester))
            if($semester->schedules->contains($schedule)){
                $schedule->coursesDate;
                return $this->showOne($schedule);
            }
        return $this->errorResponse('The schedule not created by this admin',422);
    }

    public function update(Request $request, Admin $admin,Semester $semester,Schedule $schedule)
    {
        if(!$admin->schedules->contains($schedule) or $semester->schedules->contins($schedule))
            return $this->errorResponse('The schedule not created by this admin',422);
        $request->validate([
            'title' => 'min:2|max:150',
            'course' => 'array',
            'datetime' => 'array',
        ]);
        //it can't change the type of the schedule.
        if(isset($request->type))
            return $this->errorResponse('You can not change the type of this schedule',422);
        //for duplicated
        if(count($request->course) > count(array_unique($request->course)))
            return $this->errorResponse('There duplicated course',422);
        //update title
        if(isset($request->title)){
            $schedule->title = $request->title;
            $schedule->save();
        }
        //check count
        $courses = $request->course;
        $datetimes = $request->datetime;
        //check course and datetime
        for ($i=0; $i < count($courses) ; $i++) { 
            if(!is_numeric($courses[$i]))
                return $this->errorResponse('Invalid Input => Course',422);
            if(!$this->validateDate($datetimes[$i]))
                return $this->errorResponse('Invalid Input => Datetime',422);
            if(!$semester->courses->contains($courses[$i]))
                return $this->errorResponse('There no course have this id:'.$course[$i],422);
        }
        // In the schedule of semester it must be the same count of course that recorded for this semester
        if($schedule->type == 'semester'){
            // $schedulesDate = $schedule->schedulesDate;
            $allCourses = $semester->courses->unique()->values();
            if(count($allCourses) != count($courses) or count($allCourses) != count($datetimes))
                return $this->errorResponse('Invalid Input, it must be '.count($allCourses).' courses',422);
        }
        $schedulesDate = $schedule->coursesDate;
        for ($i=0; $i < count($courses); $i++) { 
            $temp = $schedulesDate->where('course_id',$courses[$i])->first();
            if(isset($temp->id)){ //find and update datatime
                $temp->datetime = $datetimes[$i];
                $temp->save();
            }else{ //this case must be not heppen. if not exit in date , it will create a new one.
                ScheduleDate::create([
                    'schedule_id' => $schedule->id,
                    'course_id' => $courses[$i],
                    'datetime' => $datetimes[$i],
                ]);
            }
        }
        if($schedule->type == 'other'){//for remove the courses not exist in request
            foreach ($schedulesDate as $index => $courseDate) {
                if(!in_array($courseDate->course_id,$courses))
                    $courseDate->delete();
            }
        }
        $scheduleDetails = $schedule->where('id',$schedule->id)->whereHas('coursesDate')->with('coursesDate')->first();
        return $this->showOne($scheduleDetails);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admin $admin,Semester $semester, Schedule $schedule)
    {
        if($admin->scheduleSemesters->contains($semester))
            if($semester->schedules->contains($schedule)){
                $schedule->delete();
                return $this->showOne($schedule);
            }
        return $this->errorResponse('The admin and semester not contain this schedule or was delete',422);
    }
}
