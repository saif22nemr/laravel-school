<?php

namespace App\Http\Controllers\Level;

use App\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;

class LevelController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'sortby'  => 'string|in:id,title,stage,level_number,created_at',
            'searchby'=> 'string|in:id,title,stage,level_number',
            'orderby'   => 'string|in:desc,asc',
        ]);
        //Searching
        if(isset($request->search) and isset($request->searchby)){
            if(in_array($request->searchby,['id','stage','level_number'])){
                if(!is_numeric($request->search))
                    return $this->errorResponse('The search field should be integer',422);
                $levels = Level::where('id',$request->search);
            }else{
                $levels = Level::where('title', 'like', '%'.$request->search.'%');
            }
        }
        //Sorting
        if(isset($request->sortby) and isset($request->orderby)){
            if(isset($levels))
                $levels = $levels->orderBy($request->sortby, $request->orderby);
            else
                $levels = Level::orderBy($request->sortby, $request->orderby);
        }
        //get final result
        if(isset($levels)){
            $levels = $levels->get();
        }else{
            $levels = Level::orderBy('created_at','desc')->get();
        }
        return $this->showAll($levels);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:2|max:150|unique:levels',
            'level_number' => 'required|integer',
            'stage' => 'required|in:1,2,3'
        ]);
        $check = Level::where('level_number',$request->level_number)->where('stage',$request->stage)->first();

        if(isset($check->id))
            return $this->errorResponse('The Leve really exist',422);
        $data = $request->only([
            'title', 'level_number', 'stage'
        ]);
        $level = Level::create($data);
        return $this->showOne($level);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Level  $level
     * @return \Illuminate\Http\Response
     */
    public function show(Level $level)
    {
        return $this->showOne($level);
    }


    public function update(Request $request, Level $level)
    {
        $request->validate([
            'title' => 'string|min:2|max:150|unique:levels',
            'level_number' => 'integer',
            'stage' => 'in:1,2,3'
        ]);
        $data = $request->only([
            'title','level_number','stage'
        ]);
        if(count($data) == 0)
            return $this->errorResponse('It can\'t update the empty input',422);
        $level->fill($data);
        $check = Level::where('id','!=',$level->id)->where('stage',$level->stage)->where('level_number',$level->level_number)->first();
        if(isset($check->id))
            return $this->errorResponse('You can\'t update data that have been exist',422);
        $level->save();
        return $this->showOne($level);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Level  $level
     * @return \Illuminate\Http\Response
     */
    public function destroy(Level $level)
    {
        $level->delete();
        return $this->showOne($level);
    }
    public function getLevelsCourses(){
        //$all = Level::with('courses')->leftJoin('courses','courses.level_id','levels.id')->select('levels.*')->get();
        $all = Level::with('courses')->get();
        //$all = Level::all();
        return $this->showAll($all);
    }
}
