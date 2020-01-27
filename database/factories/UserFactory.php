<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use App\Room;
use App\Exam;
use App\Level;
use App\Phone;
use App\Teacher;
use App\Semester;
use App\Fullyear;
use App\Course;
use App\Schedule;
use App\ScheduleDate;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
  $year = $faker->numberBetween(1980,2019);
  $fakeDate = ''.$year.'-'.$day.'-'.$month;
    return [
        'username' => $faker->userName,
        'fullname' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'address' => $faker->address,
        'active' => $faker->randomElement([0,1]),
        'birthday' => $fakeDate,
        'userGroup' => $faker->randomElement([0,1,2,3]),
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
    $fakeDate = ''.$year.'-'.$day.'-'.$month;
    $user = User::where('userGroup','>=',2)->get()->random()->id;
    return [
        'titleJob' => $faker->randomElement($titleJobs),
        'startDate' => $fakeDate,
        'salary' => $faker->numberBetween(1000,6000),
        'user_id' => $user,
    ];
});
$factory->define(Level::class, function (Faker $faker) {
    $count = 0;
    do{
        $levelNumber = $faker->randomElement([1,2,3]);
        $stage = $faker->randomElement([1,2,3]);
        $level = Level::where('level_number',$levelNumber)->where('stage',$stage)->first();
        if(!isset($level->id))
            break;
        if($count > 200) return ;
        $count++;

    }while(1);
    return [
        'title' => $faker->word,
        'level_number' => $levelNumber,
        'stage' =>  $stage
    ];
});


$factory->define(Log::class, function (Faker $faker) {
  $day = $faker->numberBetween(1,28);
  $month = $faker->numberBetween(1,12);
  $year = $faker->numberBetween(2018,2019);
  $fakeDate = ''.$year.'-'.$day.'-'.$month;
    return [
        'loginDate' => $fakeDate,
        'user_id' => User::all()->random()->id,
        'admin_side' => $faker->randomElement([0,1]),
    ];
});

