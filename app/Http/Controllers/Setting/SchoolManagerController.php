<?php

namespace App\Http\Controllers\Setting;

use App\Setting;
use App\Semester;
use App\AcademicYear;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class SchoolManagerController extends ApiController
{
    //method availableAcademicYear for get all academic year that date can be in this interval
    public function getAvailableAcademicYear($id = -1){
        if($id <= 0){
            $academicYears = AcademicYear::orderBy('created_at','desc')->get();
        }
        else
            $academicYears = AcademicYear::where('id',$id)->orderBy('created_at','desc')->get();
        $availableAcademicYears = [];
        $academicYears->map(function($academic){
            $currentDate = date('Y-m-d');
            $semesters = $academic->semesters()->orderBy('type','asc')->get();
            //check : the current date should be less_than the end_date of second semester
            if($this->compareDate($currentDate,$semesters[1]->end_date) == -1){
                $academic['semesters'] = $semesters;
                return $academic;
            }
        });
        // foreach($academicYears as $key => $academic){
        //     $semesters = $academic->semesters()->orderBy('type','asc')->get();
        //     $currentDate = date('Y-m-d');
        //     //check : the current date should be less_than the end_date of second semester
        //     if($this->compareDate($currentDate,$semesters[1]->end_date) == -1){
        //         $availableAcademicYears[] = $academic->semesters;
        //     }
        // }
        if($id < 0)
            return $this->successResponse($academicYears);
        else
            return $academicYears;
    }


    // for get current academic year that has been aready stored in database;
    public function getCurrentAcademicYear(){
        //first check if there exist in setting or not
        $academic = Setting::where('title','current_academic_year')->first();
        $semester = Setting::where('title','current_semester')->first();
        if(isset($academic->id) and isset($semester->id))
            return $this->successResponse([$academic->value,$semester->value]);
        return $this->successResponse([-1,-1]);
        //return $this->errorResponse('There not exist of current the academic year',404);
    }
    //this method of store and update current of academic year
    public function storeCurrentAcademicYear(AcademicYear $academicYear, Semester $semester){
        if(!$academicYear->semesters->contains($semester))
            return $this->errorResponse('Invalid Input',422);
        //validate
        $check = $this->getAvailableAcademicYear($academicYear->id);
        if(count($check) != 1)
            return $this->errorResponse('Invalid Input',422);
        $academic = Setting::where('title', 'current_academic_year')->first();
        if(isset($academic->id))://update
            $academic->value = $academicYear->id;
            $academic->save();
        else://store
            $academic = Setting::create([
                'title'  => 'current_academic_year',
                'value'  => $academicYear->id,
                'description' => 'The current academic year that for used to register student and other stuff',
            ]);
        endif;
        $academic = Setting::where('title', 'current_semester')->first();
        if(isset($academic->id))://update
            $academic->value = $semester->id;
            $academic->save();
        else://store
            $academic = Setting::create([
                'title'  => 'current_semester',
                'value'  => $semester->id,
                'description' => 'The current semester',
            ]);
        endif;
        $academic->semesters;
        return $this->showOne($academic);
    }
    public function getAvailableSemester(AcademicYear $academicYear){
        //check if this academic year can be exist in this current date
        if(count($this->getAvailableAcademicYear($academicYear->id) == 0)):
            return $this->errorResponse('Invalid input, the academic year should be in this interval',422);
        endif;
        $semesters = $academicYear->semesters;
        if(count($semesters) != 2) return $this->errorResponse('Invalid Input',422);
        $currentDate = date('Y-m-d');
        if($this->compareDate($currentDate, $semesters[0]->end_date) == 1){
            //that mean the firest semester in past and the current semester is semester 2
            return $this->showOne($semesters[0]);
        }
        else return $this->showAll($semesters);
    }
}
