<?php

namespace App\Http\Controllers\AcademicYear;

use App\AcademicYear;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class AcademicYearController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $all = AcademicYear::all();
        return $this->showAll($all);
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //store academicyear table
        $request->validate([
            'title' => 'required|min:2|max:150|unique:academic_year',
        ]);
        $check = AcademicYear::where('status',1)->first();
        if(isset($check->id))
            return $this->errorResponse('There academic year opened, you can\'t create new one while there another opened',422);
        $academicYear = AcademicYear::create($request->only(['title']));
        return $this->showOne($academicYear);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function show(AcademicYear $academicYear)
    {
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
        $request->validate([
            'title' => 'min:2|max:150|unique:academic_year',
            'status' => 'boolean', // 1: opened , 0: closed
        ]);
        $data = $request->only(['title', 'status']);
        //check if the request is empty
        if(count($data) == 0) return $this->errorResponse('There no input for update',422);
        //if academic year is closed, you can't reopen it
        if($academicYear->status == 0 and isset($request->status) and $request->status == 0)
            return $this->errorResponse('If the academic year is closed, you can not reopen',422);
        //check if there academic year opened
        $check = AcademicYear::where('id','!=',$academicYear->id)->where('status',1)->first();
        if(isset($check->id))
            return $this->errorResponse('There academic year opened, you can\'t create new one while there another academic year opened',422);
        $academicYear->fill($data);
        $academicYear->save();
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
