<?php

use App\Level;
use App\Course;
use App\Student;
use App\Teacher;
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

    //Academic Year
    Route::get('/admin/academic/store_course_teacher',function(){
        return view('admin.academic_year.courses_teacher_form');
    })->name('academic.courseTeacher.store');
    Route::resource('/admin/academic', 'Web\AcademicYearController')->only(['index','create','edit','show']);


	//Course
	Route::get('admin/course',function(){
        return view('admin.course.course_list');
    });
    Route::get('admin/course/create',function(){
        $levels = Level::orderBy('title','asc')->get();
        return view('admin.course.course_form',compact('levels'));
    });
    Route::get('admin/course/{course}/edit',function(Course $course){
        $course->levels;
        $levels = Level::orderBy('title','asc')->get();
        return view('admin.course.course_form',compact('course','levels'));
    });


    //Level Route
    Route::get('admin/level',function(){
        return view('admin.course.level.level_list');
    });
    Route::get('admin/level/create',function(){
        return view('admin.course.level.level_form');
    });
    Route::get('admin/level/{level}/edit',function(Level $level){
        return view('admin.course.level.level_form', compact('level'));
    });

    //Teacher
    Route::get('admin/teacher',function(){ // index [list of all teacher]
        return view('admin.teacher.teacher_list');
    });
    Route::get('admin/teacher/create',function(){ // create teacher
        return view('admin.teacher.teacher_form');
    });
    Route::get('admin/teacher/{teacher}/edit',function(Teacher $teacher){ // edit teacher
        //$teacher = Teacher::findOrFail($id)->whereHas('info')->with('info')->first();
        $teacher->info->phones;
        //return $teacher;
        return view('admin.teacher.teacher_form',compact('teacher'));
    });
    //Student
    Route::get('admin/student',function(){
        return view('admin.student.student_list');
    });
    Route::get('admin/student/create',function(){
        return view('admin.student.student_form');
    });
    Route::get('admin/student/{student}/edit',function(Student $student){
        return view('admin.student.student_form',compact('student'));
    });
    //Setting -> School Manager
    Route::get('admin/school_manager',function(){
        return view('admin.setting.school_manager');
    });

    //Setting -> Logs
    Route::get('logs',function(){
        return view('admin.setting.login_history');
    });
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
