<?php

namespace App\Http\Controllers\Level;

use App\Course;
use App\Http\Controllers\ApiController;
use App\Level;
use Illuminate\Http\Request;

class LevelCourseController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Level $level)
    {
        $courses = $level->courses;
        return $this->showAll($courses);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Level $level)
    {
        $request->validate([
            'title' => 'required|min:2|max:100',
            'description' => 'required|min:10|max:500',
        ]);
        $data = $request->only([
            'title', 'description'
        ]);
        $data['level_id'] = $level->id;
        $check = Course::where('level_id',$level->id)->where('title',$request->title)->first();
        if(isset($check->id))
            return $this->errorResponse('The level is contain samilar course',422);
        $course = Course::create($data);
        return $this->showOne($course);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Level  $level
     * @return \Illuminate\Http\Response
     */
    public function show(Level $level,Course $course)
    {
        if($level->courses->contains($course))
            return $this->showOne($course);
        return $this->errorResponse('The level not contain this course',422);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Level  $level
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Level $level,Course $course)
    {
        $request->validate([
            'title' => 'min:2|max:150|string',
            'description' => 'min:2|string'
        ]);
        if(!$level->courses->contains($course))
            return $this->errorResponse('The level not contain this course',422);
        $check = Course::where('id','!=',$course->id)->where('level_id',$level->id)->where('title',$request->title)->first();
        if(isset($check->id))
            return $this->errorResponse('The level is contain samilar course',422);
        $data = $request->only([
            'title' , 'description'
        ]);
        $course->fill($data);
        $course->save();
        return $this->showOne($course);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Level  $level
     * @return \Illuminate\Http\Response
     */
    public function destroy(Level $level,Course $course)
    {
        if(!$level->courses->contains($course))
            return $this->errorResponse('The level not contain this course',422);
        $course->delete();
        return $this->showOne($course);
    }
}
