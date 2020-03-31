<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Log;
use App\Exam;
use App\Room;
use App\User;
use App\Admin;
use App\Level;
use App\Phone;
use App\Course;
use App\Teacher;
use App\Fullyear;
use App\Schedule;
use App\Semester;
use Carbon\Carbon;
use App\AcademicYear;
use App\ScheduleDate;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/
$factory->define(User::class, function (Faker $faker) {
  $day = $faker->numberBetween(1,28);
  $month = $faker->numberBetween(1,12);
  $year = $faker->numberBetween(1980,2010);
  $fakeDate = ''.$year.'-'.$month.'-'.$day;
    return [
        'username' => $faker->unique()->userName,
        'fullname' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'address' => $faker->address,
        'active' => $faker->randomElement([0,1]),
        'birthday' => $fakeDate,
        'image' => 'img.png',
        'userGroup' => $faker->randomElement([1,2,3]),
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});
$factory->define(Teacher::class, function (Faker $faker) {
    $titleJobs = ['arabic','secince','english','art','math','other'];
    $day = $faker->numberBetween(1,28);
    $month = $faker->numberBetween(1,12);
    $year = $faker->numberBetween(1980,2019);
    $fakeDate = ''.$year.'-'.$month.'-'.$day;
    //$user = User::where('userGroup','>=',2)->get()->random()->id;
    return [
        'titleJob' => $faker->randomElement($titleJobs),
        'startDate' => $fakeDate,
        'salary' => $faker->numberBetween(1000,6000),
        'admin'    => 0,
        //'user_id' => $user,
    ];
});

$factory->define(Admin::class, function (Faker $faker) {
    $titleJobs = ['Super Admin','Register', 'Stuff Manager','Workers Manager'];
    $day = $faker->numberBetween(1,28);
    $month = $faker->numberBetween(1,12);
    $year = $faker->numberBetween(1980,2000);
    $fakeDate = ''.$year.'-'.$month.'-'.$day;
    //$user = User::where('userGroup','>=',2)->get()->random()->id;
    return [
        'titleJob' => $faker->randomElement($titleJobs),
        'startDate' => $fakeDate,
        'salary' => $faker->numberBetween(1000,6000),
        'admin'    => 1,
        //'user_id' => $user,
    ];
});



$factory->define(Log::class, function (Faker $faker) {
  $day = $faker->numberBetween(1,28);
  $month = $faker->numberBetween(1,12);
  $year = $faker->numberBetween(2018,2019);
  $fakeDate = ''.$year.'-'.$month.'-'.$day;
    return [
        'loginDate' => $fakeDate,
        'user_id' => User::where('userGroup', '!=', 1)->all()->random()->id,
        'admin_side' => $faker->randomElement([0,1]),
    ];
});

//academic year and semester

$factory->define(AcademicYear::class, function (Faker $faker) {
  $day = $faker->numberBetween(1,28);
  $month = $faker->numberBetween(1,12);
  $year = $faker->numberBetween(2018,2019);
  $fakeDate = ''.$year.'-'.$month.'-'.$day;
    return [
        'title' => $faker->unique()->userName,
    ];
});

$factory->define(Semester::class, function (Faker $faker) {
  $day = $faker->numberBetween(1,28);
  $month = $faker->numberBetween(1,12);
  $year = $faker->numberBetween(2018,2019);
  $fakeDate = ''.$year.'-'.$month.'-'.$day;
    return [
        'title' => $faker->unique()->userName,
        'start_date' => $fakeDate,
        'end_date' => $fakeDate,
        'type'   => $faker->randomElement([1,2]),
    ];
});

$factory->define(Course::class, function (Faker $faker) {
    return [
        'title' => $faker->unique()->name,
        'description' => $faker->paragraph(2),
        'level_id' => Level::all()->random()->id,
    ];
});

