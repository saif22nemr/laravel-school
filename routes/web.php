<?php

use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/






Route::middleware('auth')->group(function(){
	// Admin
	Route::get('/admin', 'Web\DashboardController@index')->name('admin');
	Route::resource('/admin/academic', 'Web\AcademicYearController');
	Route::resource('/admin/course', 'Web\CourseController');
});
Route::get('/',function(){
	return view('welcome');
})->name('/');



Route::get('test', 'TestController@test');
Route::get('test/page',function(){
	return view('admin.layout.app');
});
Route::get('/logout','Auth\LoginController@logout');
// Route::get('/login',function(){return })
Auth::routes(['register' => false]);


Route::get('/home', 'HomeController@index')->name('home');
