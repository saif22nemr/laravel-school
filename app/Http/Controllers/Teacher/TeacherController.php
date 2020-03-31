<?php

namespace App\Http\Controllers\Teacher;

use App\User;
use App\Phone;
use App\Teacher;
use App\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class TeacherController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'search'    => 'string|min:1',
            'searchby'  => 'string|in:id,name,job,yearOfBirthday,yearOfStartDate',
            'sortby'    => 'string|in:id,name,job,birthday,start_date,created_at,salary',
            'orderby'   => 'string|in:desc,asc',
        ]);
        //search part
        //return (!isset($request->search) or $request->search == '')? 'true' : 'false';
        $teachers = Teacher::with('info')->join('users','users.id','employees.id')->select('employees.*');
        //Searching
        if(isset($request->search) and isset($request->searchby)){
            if($request->searchby == 'id'){
                if(!is_numeric($request->search))
                    return $this->errorResponse('The search field should be integer',422);
                $teachers = $teachers->where('employees.id',$request->search);
            }
            elseif(in_array($request->searchby,['name', 'job'])){
                $searchby = $request->searchby == 'name'? 'users.fullname' : 'employees.'.'titleJob';
                $teachers = $teachers->where($searchby,$request->search);
            }
            elseif(in_array($request->searchby, ['yearOfBirthday', 'yearOfStartDate'])){
                $searchby = $request->searchby == 'yearOfBirthday'? 'users.birthday' : 'employees.startDate';
                if(strlen($request->search) != 4 or !is_numeric($request->search))
                    return $this->errorResponse('The search field should be integer and formatted as year date',422);
                $teachers = $teachers->where($searchby, $request->search);
            }
        }
        //Sorting
        $checkSort = false;
        if(isset($request->sortby) and isset($request->orderby)){
            $checkSort == true;
            if($request->sortby == 'name') $sort = 'users.fullname';
            elseif($request->sortby == 'job') $sort = 'employees.titleJob';
            elseif($request->sortby == 'birthday') $sort = 'users.birthday';
            elseif($request->sortby == 'id') $sort = 'employees.id';
            elseif($request->sortby == 'start_date') $sort = 'employees.startDate';
            elseif($request->sortby == 'created_at') $sort = 'employees.created_at';
            elseif($request->sortby == 'salary') $sort = 'employees.salary';

            $teachers = $teachers->orderBy($sort, $request->orderby);
        }
        if($checkSort)
            $teachers = $teachers->get();
        else
            $teachers = $teachers->orderBy('employees.created_at','desc')->get();
        if(isset($request->pre_page) and is_numeric($request->pre_page) and $request->pre_page <= 0)
            return $this->showall($teachers,200,-1);
        return $this->showall($teachers);

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
            'username' => 'required|min:2|max:150|unique:users',
            'fullname' => 'required|min:2|max:150',
            'active' => 'boolean',
            'email' => 'required|email|unique:users',
            'image' => 'required|image',
            'birthday' => 'required|date',
            'titleJob' => 'required|min:2|max:150',
            'salary' => 'required|integer',
            'phone' => 'array',
            'address' => 'required|min:2',
            'password' => 'required|confirmed|min:8',
            'startDate' => 'required|date'
        ]);
        //validate first:
        //loop for check if this number unique and formatted
        foreach($request->phone as $index => $phone){
            if(!preg_match("/(01)[0-9]{9}/",$phone,$match) or strlen($phone) != 11)
                return $this->errorResponse(['phone' => 'The Phone must be 11 integer and valid format'],422);
            $check = Phone::where('phoneNumber',$phone)->first();
            if(isset($check->phoneNumber)){
                return $this->errorResponse('The phone number must be unique, '.$phone,422);
            }
        }
        $data = $request->only(['username','fullname','email','address','active','birthday']);
        $data['password'] = Hash::make($request->password);
        $data['userGroup'] = 2; //for teacher
        $data['image'] = $request->image->store(''); //to store image
        $newUser = User::create($data);
        //check phone number
        if(isset($request->phone)){

          //delete all phone of this user and reinsert the new one;
          Phone::where('user_id',$newUser->id)->delete();
          //loop for add new phones
          foreach($request->phone as $index => $value){
            Phone::create([
              'phoneNumber'=>$value,
              'user_id' => $newUser->id,
            ]);
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
          'username' => 'min:4|max:20',
          'email' => 'email',
          'address' => 'min:6',
          'active' => 'boolean',
          'birthday' => 'date',
          'password' => 'confirmed',
          'phone' => 'array',
          'image' => 'image',
          'titleJob' => 'min:2|max:100',
          'salary' => 'integer',
          'startDate' => 'date',
        ]);
        //check unique of username and email
        $check = User::where('id', '!=' , $teacher->info->id);
        $checkUsername = $check->where('username',$request->username)->first();
        $checkEmail = $check->where('email',$request->email)->first();
        if(isset($checkUsername->id))
            return $this->errorResponse('The username should be unqiue', 422);
        if(isset($checkEmail->id))
            return $this->errorResponse('The email should be unqiue', 422);

        //check if the new password is exist
        if(($request->password != '' or strlen($request->password) != 0) and strlen($request->password) < 8){
            return $this->errorResponse('The password must be min of charchater 8',422);
        }

        $data = $request->only(['username','fullname','email','address','active','birthday']);
        if(isset($request->password))
          $data['password'] = Hash::make($request->password);
        $user = User::find($teacher->user_id);
        $user->fill($data);
        if(isset($request->image))
            $user->image = $request->image->store('');
        $user->save();

        if(isset($request->phone)){
          $phones = $user->phones;
          //loop for check if this number unique and formatted
          foreach($request->phone as $index => $phone){
            if(!preg_match("/(01)[0-9]{9}/",$phone,$match) or strlen($phone) != 11)
              return $this->errorResponse(['phone' => 'The Phone must be 11 integer and valid format'],422);
            $check = Phone::where('user_id', '!=', $teacher->info->id)->where('phoneNumber',$phone)->first();
            if(isset($check->phoneNumber)){
                return $this->errorResponse('The phone number must be unique, '.$phone,422);
            }
          }
          //delete all phone of this user and reinsert the new one;
          Phone::where('user_id',$teacher->info->id)->delete();
          //loop for add new phones
          foreach($request->phone as $index => $value){
            Phone::create([
              'phoneNumber'=>$value,
              'user_id' => $user->id,
            ]);
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
        $id = $teacher->info->id;
        User::destroy($id);
        if($teacher->info->image != null or $teacher->info->image == 'img.png')
            Storage::disk('image')->delete($teacher->info->image);
        return $this->showOne($teacher);
    }
}
