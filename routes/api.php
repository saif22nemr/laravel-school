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

Route::middleware(['auth:api'])->group(function(){
	Route::post('/user', function () {
	    return response()->json(['status'=>'success'],200);
	});
	Route::resource('academic_year','AcademicYear\AcademicYearController')->except(['create', 'edit']);
	Route::resource('academic_year.semester','AcademicYear\AcademicYearSemesterController')->except(['create', 'edit']);
	//All route that need authentcation
	//Route::group();

	//Academic Year

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
	Route::resource('semester.exam','Semester\SemesterExamController')->only(['index']);

	//Schedule

	Route::resource('schedule','Schedule\ScheduleController')->only(['index','show']);

	//Exam

	Route::resource('exam','Exam\ExamController')->only(['index','show']);
	Route::resource('exam.semester','Exam\ExamSemesterController')->only(['index']);
	Route::resource('exam.course','Exam\ExamCourseController')->only(['index']);
	Route::resource('exam.detail','Exam\ExamDetailController')->only(['index']);
	Route::resource('exam.course.grade','Exam\ExamCourseDegreeController')->only(['index','store']);

	//Employee

	Route::resource('employee','Employee\EmployeeController')->only(['index','show']);
	/*
	        ** Admin **

	*/
	Route::resource('admin','Admin\AdminController')->except(['create','edit']);
	Route::resource('admin.semester.exam','Admin\AdminSemesterExamController')->except(['create','edit']);
	Route::resource('admin.semester.schedule','Admin\AdminSemesterScheduleController')->except(['create','edit']);


});
Route::get('test','TestController@test');

