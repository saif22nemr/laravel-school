<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeacherToTableExamCourse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exam_course', function (Blueprint $table) {
            $table->bigInteger('teacher_id')->unsigned();

            $table->foreign('teacher_id')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exam_course', function (Blueprint $table) {
            $table->dropForeign('exam_course_teacher_id_foreign');
            $table->dropColumn('teacher_id');
        });
    }
}
