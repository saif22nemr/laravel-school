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
	Route::resource('/admin/academic', 'Web\AcademicYearController')->only(['index','create','edit','show']);
	//Course
	Route::resource('/admin/course', 'Web\CourseController',['names'=>[
		'store'  => 'courses.store',
		'index'  => 'courses.index',
		'edit'   => 'courses.edit',
		'destroy'=> 'courses.destroy',
		'update' => 'courses.update',
		'create' => 'courses.create',
		'show'   => 'courses.show',
	]]);

	//Student
	Route::get('/admin/student', 'Web\StudentController@index')->name('admin.student.index');
	Route::get('/admin/student/create', 'Web\StudentController@create')->name('admin.student.create');
	Route::get('/admin/student/edit', 'Web\StudentController@edit')->name('admin.student.edit');
	Route::post('/admin/student/store', 'Web\StudentController@store')->name('admin.student.store');
	Route::patch('/admin/student/update', 'Web\StudentController@update')->name('admin.student.update');
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
