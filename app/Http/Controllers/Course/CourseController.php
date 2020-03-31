<?php

namespace App\Http\Controllers\Course;

use App\Level;
use App\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Database\Eloquent\Builder;

class CourseController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'searchby'  => 'string|in:id,title,level,stage,level_title',
            'sortby'    => 'string|in:id,level,stage,level_title,title,description,created_at',
            'orderby'   => 'string|in:desc,asc',
        ]);
        $checkPass = true;

        //$result = Course::with('levels')->join('levels', 'levels.id','courses.level_id')->select('courses.*')->where('levels.title','like','%1/1%')->orderBy('levels.title')->get();
        $courses = Course::with('levels')->join('levels', 'levels.id','courses.level_id')->select('courses.*');
        if(isset($request->search)){
            $checkPass = true;
            if($request->searchby == 'level_title') $searchby = 'title';
            elseif($request->searchby == 'level') $searchby = 'level_number';
            else $searchby = $request->searchby;
            if(in_array($request->searchby, ['id','title'])){ //search from course table
                if($request->searchby == 'id'){ // id search
                    if(!is_numeric($request->search))
                        return $this->errorResponse('The search field should be integer',422);
                    $courses = $courses->where('courses.id', $request->search);
                }else{ // title search
                    $courses = $courses->where('courses.title', 'like', '%'.$request->search.'%');
                }
            }else{
                //$seachby = 'levels.'.$searchby;
                if(in_array($request->searchby, ['stage', 'level'])){
                    if(!is_numeric($request->search))
                        return $this->errorResponse('The search field should be integer',422);
                    $courses = $courses->where('levels.'.$searchby, $request->search);
                }else{
                    //return $this->errorResponse($courses->where($searchby, 'like','%'.$request->search.'%')->get(), 422);
                    $courses = $courses->where('levels.title', 'like','%'.$request->search.'%');
                }
            }
        }
        //return $this->errorResponse($seachby, 422);
        if(isset($request->sortby)){
            $checkPass = true;
            if($request->sortby == 'level_title') $sort = 'title';
            elseif($request->sortby == 'level') $sort = 'level_number';
            else $sort = $request->sortby;

            if(in_array($request->sortby, ['id','title','created_at','description'])) $sort = 'courses.'.$sort;
            else $sort = 'levels.'.$sort;

            $courses = $courses->orderBy($sort, $request->orderby);
        }


        if($checkPass)
            $courses = isset($request->sortby)? $courses->get() : $courses->orderBy('created_at','desc')->get();
        else
            $courses = Course::whereHas('levels')->with('levels')->orderBy('created_at','desc')->get();
        return $this->showAll($courses);
    }

    public function show(Course $course)
    {
        return $this->showOne($course);
    }

    public function destroy(Course $course){
        $course->delete();
        return $this->showOne($course);
    }

}
