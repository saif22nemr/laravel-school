<?php

namespace App\Http\Controllers\AcademicYear;


use App\AcademicYear;
use App\Http\Controllers\ApiController;
use App\Semester;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AcademicYearSemesterController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AcademicYear $academicYear)
    {
        $semesters = $academicYear->semesters;
        return $this->showAll($semesters);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function store(Request $request, AcademicYear $academicYear)
    {
        $request->validate([
            'title' => 'required|min:2|max:150',
            'start_date' => 'required|date|unique:semesters',
            'end_date' => 'required|date|unique:semesters',
        ]);
        //get semester type automatically
        $semesters = Semester::where('academic_year_id',$academicYear->id)->get();
        $type = '1';
        if(count($semesters) == 1 and $semesters[0]->type == '1') $type = '2';
        else if(count($semesters) >= 2) return $this->errorResponse('The academic year have two semester',422);
        //check if the end date is begger than start date
        if($this->compareDate($request->start_date,$request->end_date) == 1)
            return $this->errorResponse('The start or end date is not logical',422);
        //check date from the first semester
        if($type == '2'){
            $semester = $academicYear->semesters->where('academic_year_id',$academicYear->id)->first();
            if(!isset($semester->id)) return $this->errorResponse('There not semester from type 1',422);
            
            if($this->compareDate($semester->end_date,$request->start_date) == 1)
                return $this->errorResponse('The both of date not logical',422);
        }
        
        //check if there semester opened
        $check = Semester::where('status',1)->first();
        if(isset($check->id))
            return $this->errorResponse('You can\'t add new semester while there another semester opening',422);
        $data = $request->only([
            'title' , 'start_date', 'end_date'
        ]);
        $data['academic_year_id'] = $academicYear->id;
        $data['type'] = $type;
        $data['status'] = 1;
        //return $data;
        $semester = Semester::create($data);
        return $this->showOne($semester);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function show(AcademicYear $academicYear,Semester $semester)
    {
        if($academicYear->semesters->contains($semester))
            return $this->showOne($semester);
        return $this->errorResponse('The academic year not contain this semester',422);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AcademicYear $academicYear,Semester $semester)
    {
        $request->validate([
            'title' => 'min:2|max:150',
            'start_date' => 'date|unique:semesters',
            'end_date' => 'date|unique:semesters',
            'status' => 'boolean'
        ]);
        //check if this academic year is contain this semeseter
        if(!$academicYear->semesters->contains($semester))
            return $this->errorResponse('The academic year not contain this semester',422);

        //check if status open, it can't be open because it will open while  you created.
        if(isset($request->status) and $request->status == 1)
            return $this->errorResponse('The semester can not be opened',422);

        //where close the semester you must sure there another semester opened.
        $check = Semester::where('id','!=',$semester->id)->where('status',1)->first();
        if(isset($check->id))
            return $this->errorResponse('There another semester opened',422);

        //the second check for check date if logical
        $semesters = $academicYear->semesters->where('type','1')->first();
        if(isset($semesters->id) == 1 and $semester->type == '2'){
            if(isset($request->start_date)):
                if($this->compareDate($semesters->end_date,$request->start_date) == 1)
                    return $this->errorResponse('The start date not logical',422);
            endif;
        }
        if(isset($request->end_date) and $this->compareDate($semester->start_date, $request->end_date) == 1)
            return $this->errorResponse('The end date not logical',422);
        $data = $request->only(['title','start_date','end_date','status']);
        //check if there input
        if(count($data) == 0)
            return $this->errorResponse('There no input for update',422);
        $semester->fill($data);
        $semester->save();
        return $this->showOne($semester);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicYear $academicYear,Semester $semester)
    {
        if(!$academicYear->semesters->contains($semester))
            return $this->errorResponse('The academic year no includeing this semester',422);
        $semester->delete();
        return $this->showOne($semester);
    }
}
