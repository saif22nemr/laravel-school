<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTeacherCourseSemester extends Migration
{
    /**
     * Run the migrations.
     * relationship : teachers , course
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_course_semester', function (Blueprint $table) {
            // $table->bigIncrements('id');
            $table->bigInteger('teacher_id')->unsigned();
            $table->bigInteger('course_id')->unsigned();
            $table->bigInteger('semester_id')->unsigned();
            $table->unique(['teacher_id','semester_id','course_id']);
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('employees')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teacher_course_semester');
    }
}
