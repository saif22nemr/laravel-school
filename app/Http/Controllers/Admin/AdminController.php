<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Employee;
use App\Http\Controllers\ApiController;
use App\Phone;
use App\Teacher;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admins = Admin::with('info.phones')->get();
        
        return $this->showAll($admins);
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
          'fullname' => 'required|min:4|max:30',
          'username' => 'required|min:4|max:20|unique:users',
          'email' => 'required|email|unique:users',
          'address' => 'required|min:6',
          'active' => 'required|boolean',
          'birthday' => 'required|date',
          'password' => 'required|min:6|confirmed',
          'phone' => 'array',
          'image' => 'required|image',
          'titleJob' => 'required|min:2|max:100',
          'salary' => 'required|integer',
          'startDate' => 'required|date',
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
        $data['userGroup'] = 1; //for admin
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
        $data['admin']  = 1;
        $data['user_id'] = $newUser->id;
        $admin = Employee::create($data);
        $admin = $admin->where('id',$admin->id)->with('info.phones')->first();
        return $this->showOne($admin);
    }
    public function show(Admin $admin)
    {
        $admin = $admin->where('id',$admin->id)->with('info.phones')->first();
        return $this->showOne($admin);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\admin  $admin
     * @return \Illuminate\Http\Response
     */
     public function update(Request $request, Admin $admin)
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
        $user = User::find($admin->user_id);
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
        $admin->fill($data);
        $admin->save();
        $admin = $admin->where('id',$admin->id)->with('info.phones')->first();
        return $this->showOne($admin);
     }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admin $admin)
    {
      $show = $admin->where('id',$admin->id)->with('info.phones')->first();
      User::findOrFail($admin->user_id)->delete();
      return $this->showOne($show);
        //return $this->successResponse('This user is deleted successfully');
    }
}
