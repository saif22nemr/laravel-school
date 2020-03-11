<?php

use App\Log;
use App\Exam;
use App\User;
use App\Admin;
use App\Level;
use App\Phone;
use App\Course;
use App\Teacher;
use App\Schedule;
use App\Semester;
use App\AcademicYear;
use App\ScheduleDate;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0'); // for disable foreign key for some time

        User::truncate();
        Teacher::truncate();
        Semester::truncate();
        AcademicYear::truncate();
        Level::truncate();
        Course::truncate();
        Phone::truncate();
        Schedule::truncate();
        ScheduleDate::truncate();

        //for not sending email createing fake data
        User::flushEventListeners();
        Teacher::flushEventListeners();
        Semester::flushEventListeners();
        AcademicYear::flushEventListeners();
        Level::flushEventListeners();
        Course::flushEventListeners();
        Phone::flushEventListeners();
        Schedule::flushEventListeners();
        ScheduleDate::flushEventListeners();

        DB::table('exam_student_degree')->truncate(); //used when the table not have model
        DB::table('student_level_semester')->truncate();
        DB::table('teacher_course_semester')->truncate();

        factory(User::class,2000)->create()->each(function($user) {
            if($user->userGroup == 2){
                $user->teacher()->save(factory(Teacher::class)->make());
            }
            elseif($user->userGroup == 1){
                $user->teacher()->save(factory(Admin::class)->make());
            }
        });
        factory(Log::class,1500)->create();

    }
}
