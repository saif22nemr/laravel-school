<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Log;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if(!isset($request->email) or !isset($request->password))
            return view('auth.login');
        if (Auth::attempt(['email'=>$request->email,'password'=>$request->password,'userGroup'=>1])) {
            // Authentication passed...
            $user = Auth::user();
            do{
                $token = Str::random(80);
                $check = User::where('api_token',$token)->first();
                if(!isset($check->id))
                    break;
            }while (true);
            $user->api_token = $token;
            //session(['apiToken'=>$user->api_token]);
            $user->save();
            Log::create([
                'user_id'   => $user->id,
                'admin_side'=> 1,
            ]);
            if(isset($request->admin))
                return redirect()->intended('admin');
            return redirect()->intended('home');
        }
        return view('auth.login');
        // return response()->json([
        //     'error' => 'Invalid Authentication',
        //     'request' => $request->email
        // ],402);
    }
    public function logout(){
        
        Auth::logout();
        return view('auth.login');
    }
}
