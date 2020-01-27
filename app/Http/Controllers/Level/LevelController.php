<?php

namespace App\Http\Controllers\Level;

use App\Http\Controllers\ApiController;
use App\Level;
use Illuminate\Http\Request;

class LevelController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $levels = Level::all();
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
}
