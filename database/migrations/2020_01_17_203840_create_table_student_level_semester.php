<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableStudentLevelSemester extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_level_semester', function (Blueprint $table) {
            // $table->bigIncrements('id');
            $table->bigInteger('student_id')->unsigned();
            $table->bigInteger('level_id')->unsigned();
            $table->bigInteger('semester_id')->unsigned();
            $table->unique(['student_id','level_id','semester_id']);
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('student_level_semester');
    }
}
