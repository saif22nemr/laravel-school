<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('test','TestController@test');

//Academic Year
Route::resource('academic_year','AcademicYear\AcademicYearController')->except(['create', 'edit']);
Route::resource('academic_year.semester','AcademicYear\AcademicYearSemesterController')->except(['create', 'edit']);

//Teacher

Route::resource('teacher','Teacher\TeacherController')->except(['create','edit']);


//Student

Route::resource('student','Student\StudentController')->except(['create','edit']);

//Course

Route::resource('course','Course\CourseController')->only(['show','index']);
Route::resource('course.level','Course\CourseLevelController')->only(['index']);
Route::resource('course.semester.teacher','Course\CourseSemesterTeacherController')->only(['index']);
Route::resource('course.semester.student','Course\CourseSemesterStudentController')->only(['index']);

//Level

Route::resource('level','Level\LevelController')->except(['create','edit']);
Route::resource('level.course','Level\LevelCourseController')->except(['create','edit']);

//Semester

Route::resource('semester','Semester\SemesterController')->only(['show','index']);
Route::resource('semester.course','Semester\SemesterCourseController')->only(['show','index']);
Route::resource('semester.teacher','Semester\SemesterTeacherController')->only(['show','index']);
Route::resource('semester.teacher.course','Semester\SemesterTeacherCourseController')->except(['create','edit','update']);
Route::resource('semester.level','Semester\SemesterLevelController')->only(['index','show']);
Route::resource('semester.level.student','Semester\SemesterLevelStudentController')->except(['create','edit','update']);
Route::resource('semester.schedule','Semester\SemesterScheduleController')->only(['show','index']);
Route::resource('semester.course.teacher','Semester\SemesterCourseTeacherController')->only(['show','index']);
Route::resource('semester.course.schedule','Semester\SemesterCourseScheduleController')->only(['show','index']);

//Schedule

Route::resource('schedule','Schedule\ScheduleController')->only(['index','show']);

//Exam

Route::resource('exam','Exam\ExamController')->only(['index','show']);

//Employee

Route::resource('employee','Employee\EmployeeController')->only(['index','show']);
/*
        ** Admin **

*/
Route::resource('admin','Admin\AdminController')->except(['create','edit']);
Route::resource('admin.semester.exam','Admin\AdminSemesterExamController')->except(['create','edit']);
Route::resource('admin.semester.schedule','Admin\AdminSemesterScheduleController')->except(['create','edit']);
