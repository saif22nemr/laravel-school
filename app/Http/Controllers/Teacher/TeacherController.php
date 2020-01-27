<?php

namespace App\Http\Controllers\Teacher;

use App\Employee;
use App\Http\Controllers\ApiController;
use App\Phone;
use App\Teacher;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teachers = Teacher::with('info.phones')->get();
        return $this->showAll($teachers);
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
            'username' => 'required|min:2|max:150',
            'fullname' => 'required|min:2|max:150',
            'active' => 'boolean',
            'email' => 'required|email',
            'image' => 'required|image',
            'birthday' => 'required|date',
            'titleJob' => 'required|min:2|max:150',
            'salary' => 'required|integer',
            'phone' => 'array',
            'address' => 'required|min:2',
            'password' => 'required|confirmed|min:6',
            'startDate' => 'required|date'
        ]);
        $checkPhone = false;
        if(isset($request->phone)){
          foreach ($request->phone as $index => $value) {
            if(!preg_match("/(01)[0-9]{9}/",$value,$match) or strlen($value) != 11)
              return $this->errorResponse(['phone' => 'The Phone must be 11 integer and valid format'],422);
            $check = Phone::where('phoneNumber',$value)->get();
            if(isset($check[0])) return $this->errorResponse(['phone'=>'The phone number must be unique'],422);
          }
          $checkPhone = true;
        }
        
        $data = $request->only(['username','fullname','email','address','active','birthday', 'image']);
        $data['password'] = Hash::make($request->password);
        $data['userGroup'] = 2; //for teacher
        $newUser = User::create($data);
        if($checkPhone == true){
          foreach($request->phone as $index => $value){
            Phone::create(['phoneNumber'=>$value,'user_id'=>$newUser->id]);
          }
        }
        //$newUser->with('phones')->first();
        $data = $request->only([
          'titleJob', 'salary', 'startDate'
        ]);
        $data['user_id'] = $newUser->id;
        $teacher = Employee::create($data);
        $teacher = $teacher->where('id',$teacher->id)->with('info.phones')->first();
        return $this->showOne($teacher);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function show(Teacher $teacher)
    {
        return $this->showOne($teacher->where('id',$teacher->id)->with('info.phones')->first());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
          'fullname' => 'min:4|max:30',
          'username' => 'min:4|max:20|unique:users',
          'email' => 'email|unique:users',
          'address' => 'min:6',
          'active' => 'boolean',
          'birthday' => 'date',
          'password' => 'min:6|confirmed',
          'phone' => 'array',
          'image' => 'image',
          'titleJob' => 'min:2|max:100',
          'salary' => 'integer',
          'startDate' => 'date',
        ]);
        $checkPhone = false;
        if(isset($request->phone)){
          foreach ($request->phone as $index => $value) {
            if(!preg_match("/(01)[0-9]{9}/",$value,$match) or strlen($value) != 11)
              return $this->errorResponse(['phone' => 'The Phone must be 11 integer and valid format'],422);
          }
          $checkPhone = true;
        }
        
        $data = $request->only(['username','fullname','email','address','active','birthday', 'image']);
        if(isset($request->password))
          $data['password'] = Hash::make($request->password);
        $user = User::find($teacher->user_id);
        $user->fill($data);
        $user->save();
        if($checkPhone == true){
          $phones = $user->phones;
          //loop for add new phones
          foreach($request->phone as $index => $value){
            $phone = Phone::where('phoneNumber',$value)->first();
            if(isset($phone->phoneNumber)) continue;
            Phone::create([
              'phoneNumber'=>$value,
              'user_id' => $user->id,
            ]);
          }
          //loop for delete phone not exist
          foreach ($phones as $index => $phone) {
            if(!in_array($phone->phoneNumber , $request->phone))
              Phone::where('phoneNumber',$phone->phoneNumber)->delete();
          }
        }
        //$newUser->with('phones')->first();
        $data = $request->only([
          'titleJob', 'salary', 'startDate'
        ]);
        $teacher->fill($data);
        $teacher->save();
        $teacher = $teacher->where('id',$teacher->id)->with('info.phones')->first();
        return $this->showOne($teacher);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Teacher $teacher)
    {
        $teacher->delete();
        return $this->showOne($teacher);
    }
}
