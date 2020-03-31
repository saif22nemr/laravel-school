<?php

namespace App\Http\Controllers\Web;

use App\Log;
use App\Exam;
use App\User;
use App\Admin;
use App\Course;
use App\Student;
use App\Teacher;
use App\Employee;
use App\AcademicYear;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index(){
    	//get the
    	$data = [];
    	$data['count']['students'] =  Student::count();
    	$data['count']['teachers'] = Teacher::count();
    	$data['count']['courses'] = Course::count();
        $data['count']['academicYears'] = AcademicYear::count();
        $data['count']['admin'] = Admin::count();
        $data['count']['allUser'] = User::count();
        $data['count']['exam'] = Exam::count();
        $data['count']['log'] = Log::count();
    	return view('admin.dashboard',compact('data'));
    }
}
