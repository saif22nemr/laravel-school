<?php

namespace App\Http\Controllers\AcademicYear;

use App\Semester;
use App\AcademicYear;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class AcademicYearController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'academic_title' => 'string|min:1',
            'semester_title' => 'string|min:1',
            'interval'       => 'string|min:1',
            'year'           => 'integer|min:1900',
            'academic_id'    => 'integer|min:0',
        ]);
        //filter
        $filter = $request->only([
            'academic_title', 'semester_title', 'year', 'academic_id'
        ]);
        if(count($filter) == 0){
            $allAcademicYears = AcademicYear::with('semesters');
        }
        else{
            foreach ($filter as $filterBy => $value) { //take first one of filter
                if($filterBy == 'academic_title'){
                    $allAcademicYears = AcademicYear::where('title','like', '%'.$value.'%')->with('semesters');
                }
                elseif($filterBy == 'academic_id'){
                    $allAcademicYears = AcademicYear::where('id',$value)->with('semesters');
                }
                elseif($filterBy == 'semester_title'){
                    $allAcademicYears = AcademicYear::whereHas('semesters', function(Builder $query) use ($value){
                        return $query->where('title', 'like' , '%' . $value . '%');
                    })->with('semesters');
                }
                elseif($filterBy == 'year'){
                    $allAcademicYears = AcademicYear::whereHas('semesters', function(Builder $query) use ($value){
                        return $query->where('start_date', 'like' , '%' . $value . '%')->orWhere('end_date', 'like' , '%' . $value . '%');
                    })->with('semesters');
                }
                break;
            }
        }
        //sorting
        if(isset($request->sortby)){
            $avaliableSort = ['id' , 'created_at', 'title'];
            if(in_array($request->sortby, $avaliableSort)){
                if(isset($request->orderby) and $request->orderby == 'desc' or $request->orderby == 'asc'){
                    $order = $request->orderby;
                }
                else{
                    $order = 'asc';
                }
                $sort = $request->sortby;
            }
        }else{
            $order = 'desc';
            $sort = 'created_at';
        }
        $result = $allAcademicYears->orderBy($sort, $order)->get();
        $addition = [
            'sort' => $sort,
            'order'=> $order
        ];
        return $this->showAll($result, 200, 20, $addition);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //this method will store academic year with has semesters
        $request->validate([
            //academic year
            'title'                => 'required|min:2|max:150|unique:academic_year',
            //first semester
            'semester1_title'      => 'required|min:2|max:150',
            'semester1_start_date' => 'required|date',
            'semester1_end_date'   => 'required|date',
            //second semester
            'semester2_start_date' => 'required|date',
            'semester2_end_date'   => 'required|date',
            'semester1_title'      => 'required|min:2|max:150',
        ]);
        //return $request->all();
        $firstSemester = [
            $request->semester1_title,
            $request->semester1_start_date,
            $request->semester1_end_date,
        ];
        $secondSemester = [
            $request->semester2_title,
            $request->semester2_start_date,
            $request->semester2_end_date,
        ];
        //validate semester data
        $check = $this->validateSemester($firstSemester);
        if($check != 'true') return $this->errorResponse($check,422);
        $check = $this->validateSemester($secondSemester);
        if($check != 'true') return $this->errorResponse($check,422);
        //insert academic year
        $academicYear = AcademicYear::create(['title' => $request['title']]);

        if(isset($academicYear->id)){
            //insert semester
            $semester1 = $this->storeSemester($academicYear, $firstSemester);
            if(!isset($semester1->id)) return $this->errorResponse($semester1,422);
            $semester2 = $this->storeSemester($academicYear ,$secondSemester);
            if(!isset($semester2->id)) return $this->errorResponse($semester2,422);
            $academicYear->semesters;
            return $this->showOne($academicYear);
        }
        else // for make sure it created ..
            return $this->errorResponse('There something wrong, try again!',422);

    }
    private function storeSemester(AcademicYear $academicYear,Array $semester){
        //Note:: you don't use this method before use validateSemester

        //must $semester have three data [title, start_date, end_date]
        if(count($semester)  != 3)
            return 'There some data lost of this semester';
        //check type , if this semester is the first one or the second and get type data dyanmicaly
        $semesterOfAcademicYear = $academicYear->semesters()->orderBy('type','desc')->first();
        if(isset($semesterOfAcademicYear->id)){
            if($semesterOfAcademicYear->type == 1)
                $type = 2;
            else if($semesterOfAcademicYear->type == 2)
                return 'The semester has been recorded before';
        }else{
            $type = 1;
        }
        //Now, you can store semester
        $semester = Semester::create([
            'title'      => $semester[0],
            'start_date' => $semester[1],
            'end_date'   => $semester[2],
            'type'       => $type,
            'academic_year_id' => $academicYear->id,
        ]);
        return $semester;
    }
    private function validateSemester(Array $semester){
        //must $semester have three data [title, start_date, end_date]
        if(count($semester)  != 3)
            return 'There some data lost of this semester';
        //check if start date is last than end date
        if($this->compareDate($semester[1] , $semester[2]) == 1){//that's mean the start date is bigger than the end date
            return 'The start date is bigger than the end date of this semester';
        }
        //check if the start date not from the past
        $lastSemester = Semester::orderBy('created_at','desc')->first();
        if(isset($lastSemester->id) and $this->compareDate($lastSemester->start_date, $semester[1]) == 1){
            return 'The date you enter from the past';
        }
        return 'true';
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function show(AcademicYear $academicYear)
    {
        $academicYear->semesters;
        return $this->showOne($academicYear);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        //update academic year with has own semesters
        $request->validate([
            //academic year
            'title'                => 'min:2|max:150',
            //first semester
            'semester1_title'      => 'min:2|max:150',
            'semester1_start_date' => 'date',
            'semester1_end_date'   => 'date',
            //second semester
            'semester2_start_date' => 'date',
            'semester2_end_date'   => 'date',
            'semester1_title'      => 'min:2|max:150',
        ]);
        $semesters = $academicYear->semesters;
        //update academic year
        if(isset($request->title) and $request->title != $academicYear->title){
            //check if academic year title is unique
            $check = AcademicYear::where('id', '!=' , $academicYear->id)->where('title',$request->title)->first();
            if(isset($check->id))
                return $this->errorResponse('The academic year title must be unique',422);
            $academicYear->title = $request->title;
            $academicYear->save();
        }
        if(count($semesters) != 2)
            return $this->errorResponse('There semester not inserted, it should removed and reinsert',422);
        $firstSemester = $request->only([
            'semester1_title', 'semester1_start_date', 'semester1_end_date'
        ]);
        $secondSemester = $request->only([
            'semester2_title', 'semester2_start_date', 'semester2_end_date'
        ]);
        //return $semesters[0];
        if(count($firstSemester) != 0){ // start update second semester of this academic year
            foreach($firstSemester as $key => $value){
                $isValid = false;
                if($key == 'semester1_title'){
                    $semesters[0]->title = $value;
                    $isValid = true;
                }
                elseif($key == 'semester1_start_date' and $value != $semesters[0]->start_date){
                    $isValid = true;
                    //validate this date
                    $lastDate = Semester::where('start_date' , '>' , $semesters[0]->start_date)->orderBy('start_date','asc')->pluck('start_date')->first();
                    //check if this date not less than the last semester recorded before this semester
                    //return $value . '=>' .$lastDate;
                    if($this->compareDate($value, $lastDate) == 1){
                        return $this->errorResponse('Invalid start date of first semester',422);
                    }
                    if($this->compareDate($value, $semesters[0]->end_date) == 1 or $this->compareDate($value, $semesters[1]->end_date) == 1)
                        return $this->errorResponse('Invalid start date of first semester', 422);
                    $semesters[0]->start_date = $value;
                }
                elseif($key == 'semester1_end_date' and $value != $semesters[0]->end_date){
                    $isValid = true;
                    //validate end date of first semester, it must be between the start date of first semester and the start date of the second semester
                    if($this->compareDate($semesters[0]->start_date , $value) == 1 or $this->compareDate($value, $semesters[1]->start_date) == 1){
                        return $this->errorResponse('Invalid end date of first semester',422);
                    }

                    $semesters[0]->end_date = $value;
                }
                if($isValid)
                    $semesters[0]->save();
            }
        }
        if(count($secondSemester) != 0){ // start update second semester of this academic year
            foreach($secondSemester as $key => $value){
                $isValid = false;
                if($key == 'semester2_title'){
                    $semesters[1]->title = $value;
                    $isValid = true;
                }
                elseif($key == 'semester2_start_date' and $value != $semesters[1]->start_date){
                    $isValid = true;
                    //start date of the second semester, must be between the end date of first semester and the end date of the second semester
                    if($this->compareDate($semesters[0]->end_date, $value) == 1 or $this->compareDate($value, $semesters[1]->end_date) == 1)
                        return $this->errorResponse('Invalid start date of second semester', 422);
                    $semesters[1]->start_date = $value;
                }
                elseif($key == 'semester2_end_date' and $value != $semesters[1]->end_date){
                    $isValid = true;
                    //validate this date
                    //the end date of the second semester must be between the start date of this semester and the start date of less than semester.
                    $lastDate = Semester::where('start_date' , '>', $semesters[1]->end_date)->orderBy('start_date','asc')->pluck('start_date')->first();
                    if($lastDate and $this->compareDate($value, $lastDate) == 1 or $this->compareDate($semesters[1]->start_date, $value) == 1){
                        return $this->errorResponse('Invalid end date of second semester',422);
                    }
                    $semesters[1]->end_date = $value;
                }
                if($isValid)
                    $semesters[1]->save();
            }
        }

        return $this->showOne($academicYear);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();
        return $this->showOne($academicYear);
    }
}
