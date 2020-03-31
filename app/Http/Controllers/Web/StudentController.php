<?php

namespace App\Http\Controllers\Web;

use App\Student;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Exception\RequestException;


class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // it will get list of student
        //$data['students'] = Student::paginate(20);
        $client = new Client();
        $data = [
            'headers' => [
                'Authorization' => 'Bearer '.Auth()->user()->api_token,
                'Accept' => 'application/json',
            ],
        ];
        $response = $client->request('GET',route('student.index'),$data);
        
        //$data['students'] = $d['data'];
        $d =json_decode((string) $response->getBody());
        //echo Psr7\str($response);
        $data['students'] = $d->data->data;
        // print_r($d);
        // exit();
        return view('admin.student.student_list',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        
        return view('admin.student.student_form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $token = Auth::user()->api_token;
        $client = new Client();//new GuzzleHttp\Client();
        //echo 'token: '. $token;
        $request->validate(['image' => 'required|image']);
        //$imagePath = $request->image->pathName;
        // print_r($request->image);
        // exit();
        $imagePath = $request->image;
        $imageName = $request->image->getClientOriginalName();
        $imageType = $request->image->getmimeType(); 
        $data = [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Accept' => 'application/json',
            ],
            //'form_params' => $request->all(),
            'multipart' => [
                [
                    'name' => 'image',
                    'filename' => $imageName,
                    'Mime-Type'=> $imageType,
                    'contents' => fopen($imagePath,'r'),
                    //'filename' => end(explode('/', $filepath)),
                ],
                [
                    'name'     => 'username',
                    'contents' => $request->username,
                ],
                [
                    'name'     => 'fullname',
                    'contents' => $request->fullname,
                ],
                [
                    'name'     => 'email',
                    'contents' => $request->email,
                ],
                [
                    'name'     => 'address',
                    'contents' => $request->address,
                ],
                [
                    'name'     => 'password',
                    'contents' => $request->password,
                ],
                [
                    'name'     => 'password_confirmation',
                    'contents' => $request['password_confirmation'],
                ],
                [
                    'name'     => 'birthday',
                    'contents' => $request['birthday'],
                ],
            ],
        ];
        //$data['form_params']['image'] = $request->file('image');;
        //var_dump($data);
        try{
            //print_r($request->all());
            $response = $client->request('POST',route('student.index'),$data);
            $data['message'] = '<div class="alert alert-success">Successfull add new student</div>';
            //$data['student'] = 
            return $response->getBody();
        }catch(RequestException $e){
            //echo Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                //echo 'error: ';
                $res = $e->getResponse();
                //var_dump((string) $res->getBody());
                //$stringError = ''+(string) $res->getBody();
                $error = json_decode((string) $res->getBody());

                //var_dump($error);
            }
        }
        
        //dd($response);
        //return $response->getBody();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit(Student $student)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Student $student)
    {
        //
    }
}
