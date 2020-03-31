<?php

namespace App\Http\Controllers\Web;

use App\AcademicYear;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        //filter
        //return (!isset($request->search) or $request->search == '')? 'true' : 'false';
        if(!isset($request->search) or $request->search == ''){
            $allAcademicYears = AcademicYear::with('semesters');
            $search = isset($request->searchby)? $request->searchby : 'academic_id';
        }
        else{
            if($request->searchby == 'academic_title'){
                $allAcademicYears = AcademicYear::where('title','like', '%'.$request->search.'%')->with('semesters');
            }
            elseif($request->searchby == 'academic_id' and is_numeric($request->search)){
                $allAcademicYears = AcademicYear::where('id',$request->search)->with('semesters');
            }
            elseif($request->searchby == 'semester_title'){
                $allAcademicYears = AcademicYear::whereHas('semesters', function(Builder $query) use ($request){
                    return $query->where('title', 'like' , '%' . $request->search . '%');
                })->with('semesters');
            }
            elseif($request->searchby == 'year' and is_numeric($request->search)){
                $allAcademicYears = AcademicYear::whereHas('semesters', function(Builder $query) use ($request){
                    return $query->where('start_date', 'like' , '%' . $request->search . '%')->orWhere('end_date', 'like' , '%' . $request->search . '%');
                })->with('semesters');
            }else{
                $allAcademicYears = AcademicYear::with('semesters');
            }
            $search = $request->searchby;
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
            else{
                $sort = $request->sortby;
            }
        }else{
            $order = isset($request->order)? $request->order: 'desc';
            $sort = isset($request->sort)? $request->sort : 'created_at';
        }
        $result = $allAcademicYears->orderBy($sort, $order)->paginate(20);
        $addition = [
            'sort' => $sort,
            'order'=> $order
        ];
        $data['academicYear'] = $result;

        return view('admin.academic_year.academic_year_list',compact(['data','sort','order','search']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.academic_year.academicYearForm');
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function show(AcademicYear $academic)
    {
        //
        return $academic;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function edit(AcademicYear $academic)
    {
        $academic->semesters;
        //return $academic;
        return view('admin.academic_year.academicYearForm',compact('academic'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicYear $academicYear,Request $request)
    {

        //will return json data
        $academicYear->delete();
        $id = $request->id;
        AcademicYear::where('id',$id)->delete();

        return response()->json(['status'=>'success'],200);
    }


}
