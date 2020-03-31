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
    public function index(Request $request)
    {
        //return $this->errorResponse($request->all(),422);
        $request->validate([
            'searchby' => 'string|in:id,fullname,username,address,year_of_birthday,email',
            'sortby'   => 'string|in:id,fullname,username,address,birthday,email,created_at',
            'orderby'  => 'string|in:desc,asc'
        ]);
        //$students = Student::where('userGroup',3);
        //filter with search
        if(isset($request->search) and $request->search != '' and isset($request->searchby)){
            // if(!isset($request->searchby)){
            //     return $this->errorResponse('Invalid Search',422);
            // }
            if($request->searchby == 'year_of_birthday'){
                if(strlen($request->search) != 4 or !is_numeric($request->search))
                    return $this->errorResponse('Invalid year input',422);
                $students = Student::where('birthday','like', '%'.$request->search.'%');
            }
            else if($request->searchby == 'id'){
                //check if number
                if(!is_numeric($request->search))
                    return $this->errorResponse('The search field should be integer',422);
                //return $this->successResponse($request->all());
                $students = Student::where('id',$request->search);
            }
            else{
                $students = Student::where($request->searchby, 'like', '%'.$request->search.'%');
            }
        }else
            $students = Student::where('userGroup',3);
        //filter with sort
        if(isset($request->sortby)){
            $order = isset($request->orderby) ? $request->orderby : 'desc';
            $students = $students->orderBy($request->sortby , $order);
        }else{
            $students = $students->orderby('created_at','desc');
        }
        $students = $students->get();
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
          'username' => 'min:4|max:20',
          'email' => 'email',
          'address' => 'min:6',
          'active' => 'boolean',
          'birthday' => 'date',
          'password' => 'confirmed',
          'phone' => 'array',
          'image' => 'image',
        ]);
        //check unique of username, email
        $check = User::where('id','!=',$student->id);
        $checkUsername = $check->where('username', $request->username)->first();
        if(isset($checkUsername->id))
            return $this->errorResponse('The username must be unique',422);
        $checkEmail = $check->where('email',$request->email)->first();
        if(isset($checkEmail->id))
            return $this->errorResponse('The email must be unique',422);
        //check phone
        if(isset($request->phone)){
          foreach ($request->phone as $index => $value) {
            if(!preg_match("/(01)[0-9]{9}/",$value,$match) or strlen($value) != 11)
              return $this->errorResponse(['phone' => 'The Phone must be 11 integer and valid format'],422);
          }
        }

        $data = $request->only(['username','fullname','email','address','active','birthday']);
        //check if there password updated or not and check count of character
        if(isset($request->password)){
          if(strlen($request->password) < 8 and strlen($request->password) != 0)
            return $this->errorResponse('The password must be min 8 charcter',422);
          $data['password'] = Hash::make($request->password);
        }
        $user = $student;
        if(isset($request->image))
            $user->image = $request->image->store('');
        if(count($data)  != 0){
            $user->fill($data);
            $user->save();
        }
        if(isset($request->phone)){
          $phones = $user->phones;
          //delete all phones of this user
          Phone::where('user_id',$student->id)->delete();
          //loop for add new phones
          foreach($request->phone as $index => $value){
            Phone::create([
              'phoneNumber'=>$value,
              'user_id' => $user->id,
            ]);
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
