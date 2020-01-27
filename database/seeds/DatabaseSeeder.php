<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Room;
use App\Exam;
use App\Level;
use App\Phone;
use App\Teacher;
use App\Semester;
use App\Fullyear;
use App\Schedule;
use App\ScheduleDate;

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
        Fullyear::truncate();
        Room::truncate();
        Level::truncate();
        Course::truncate();
        Phone::truncate();
        Schedule::truncate();
        ScheduleDate::truncate();
    }
}
