<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\ApiController;
use App\Phone;
use App\Student;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $students = Student::with('phones')->get();
        return $this->showAll($students);
    }

   
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
      //var_dump($request->all());

        $request->validate([
            'username' => 'required|min:2|max:150|unique:users',
            'fullname' => 'required|min:2|max:150',
            'active' => 'boolean',
            'email' => 'required|email|unique:users',
            'image' => 'required|image',
            'birthday' => 'required|date',
            'phone' => 'array',
            'address' => 'required|min:2',
            'password' => 'required|confirmed|min:6',
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
        
        $data = $request->only(['username','fullname','email','address','active','birthday']);
        $data['password'] = Hash::make($request->password);
        $data['userGroup'] = 3; //for student
        $data['image'] = $request->image->store('');
        $newUser = User::create($data);
        if($checkPhone == true){
          foreach($request->phone as $index => $value){
            Phone::create(['phoneNumber'=>$value,'user_id'=>$newUser->id]);
          }
        }
        $student = $newUser->where('id',$newUser->id)->with('phones')->first();
        return $this->showOne($student);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        $student = $student->where('id',$student->id)->with('phones')->first();
        return $this->showOne($student);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Student $student)
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
        $user = $student;
        if(count($data)  != 0){
            $user->fill($data);
            $user->save();
        }
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
        $student = $student->where('id',$student->id)->with('phones')->first();
        return $this->showOne($student);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Student $student)
    {

        $student->delete();
        Storage::disk('image')->delete($student->image);
        return $this->showOne($student);
    }
}
