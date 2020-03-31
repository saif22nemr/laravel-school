<?php $active = ['academicYear', 'store course and teacher'];?>
@extends('admin.layout.app')

@section('title','Academic Year')
@section('sub-navbar')
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Store Courses And Teacher Of Academic Year</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('admin')}}">Home</a></li>
              <li class="breadcrumb-item active">Manage Academic Year</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
@endsection

@section('content')
<div class="row">
  <div class="container-fluid">
    <div class="card add-form">
      <div class="card-header">
        <h3 class="card-title">@if(isset($academic)) Edit Academic Year @else New Academic Year @endif</h3>
      </div>
      <!-- /.card-header -->
      <!-- form start -->
      <form id="form-add" role="form"  enctype="multipart/form-data">
        @csrf
        <div class="card-body teacher-course-form">
          <div class="content-message">

          </div>
        <div class="form-body">

        </div>




        </div>
        <!-- /.card-body -->

        <div class="card-footer text-center">
          <input type="submit" class="btn btn-primary text-center" @if(isset($academic)) value="Save Updated" @else value="Add New" @endif>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
<!-- <div class="row">
                <div class="col-sm-6">
                    <select name="course" readonly id="" class="form-control">
                        <option value="val" disable>Course</option>
                    </select>
                </div>
                <div class="col-sm-6">
                    <select id="select-beast" class="operator form-control">
                            <option value="">Select a Page...</option>
                            <option value="alpha">alpha</option>
                            <option value="beta">beta</option>
                            <option value="theta">theta</option>
                            <option value="omega">omega</option>
                    </select>
                </div>
            </div>
         -->

@section('js')
<script type="text/javascript">

  $(function(){ // ready
    var token = "{{Auth()->user()->api_token}}";
    var header =  {'Authorization' : 'Bearer '+token,'Accept' : 'application/json'};
    var teachers = {};

    var editForm = false;
    @if(isset($academic)) editForm = true; @endif
    //validate input of datetime , of class [input-datetime]
    function compareDate(datetime,datetime2 = null){
      //the function will recive datetime vaildate and it will compare it between it
      var date = datetime.split('T')[0]; //get date only
      if(datetime2 == null){
        var current = (new Date()).toISOString(); //for convert to date
        var cdate = (new Date()).toISOString().split('T')[0];
      }else{
        var cdate = datetime2.split('T')[0];
      }

      if(date <= cdate){ // if the date is lower then the current date
        return false;
      }
      return true;
    } // end function compareDate
    function showNavigator(content, status = 'success', time = 3000) {
        var thisTag = $(".navigator");
        thisTag.css('display', 'block');
        if (status == "error") {
            //for change background-color of navigator
            $(".navigator .body")
                .removeClass("success-color")
                .addClass("error-color");
        } else {
            $(".navigator .body")
                .removeClass("error-color")
                .addClass("success-color");
        }

        $(".navigator .body").css("left", "10px");
        $(".navigator .body .content").html(content);
        setTimeout(function() {
            $(".nofigation").css("left", "-330px");
            thisTag.css("display", "none");
        }, time);
    }
    $.ajax({
                url: "{{route('teacher.index')}}?pre_page=-2",
                method: 'get',
                headers: header,
                success: function(d){
                    teachers = d.data;
                    console.log('teachers : ');
                    console.log(teachers);
                    $.ajax({
                        url: "{{route('levels.courses')}}",
                        method: 'get',
                        headers: header,
                        beforeSend: function(){
                            console.log('defore');

                        },
                        success: function(data, status, xhr){
                            var content = '';

                            console.log(teachers);
                            if(Object.keys(data.data.data).length == 0)
                                content = '<h5 class="alert alert-warning text-center">Empty Data !</h5>';
                            else{

                                var levels = data.data.data;
                                $.each(levels, function(index, level){
                                    content += '<div class="another-input"><h3 class="input-title">'+level.title+'</h3><div class="row"><h3 class="col-sm-6 text-center">Course</h3><h3 class="col-sm-6 text-center">Teacher</h3></div><div class="form-body">';
                                    var courses = level.courses;
                                    if(Object.keys(courses).length == 0)
                                        content += '<h5 class="alert alert-warning text-center">There no courses of this level</h5>';
                                    $.each(courses, function(index, course){
                                        content += '<div class="row"><div class="col-sm-6"><select name="course '+course.title+'" readonly  class="form-control"><option value="'+course.id+'" disable>'+course.title+'</option></select></div><div class="col-sm-6">';
                                        content += '<select name="teacher '+course.id+'" class="operator form-control"><option value="0" disable>Select a Page...</option>';
                                        $.each(teachers, function(key, teacher){
                                            content += '<option value="'+teacher.id+'">'+teacher.info.fullname+' --> '+teacher.titleJob+'</option>';
                                        });
                                        content += '</select>';
                                        content += '</div></div>';
                                    });
                                    content += '</div></div>';
                                });
                                $('.form-body').html(content);
                            }
                        },
                        error: function(xhr, status){
                            console.log('level error');
                            console.log(xhr);
                        },
                    });
                },
                error: function(xhr, status, message){
                    showNavigator('<h5 class="text-center">Fail to get teachers</h5><p>- Error Message: '+message+'</p>','error');
                    console.log(xhr);
                }
            });

    //console.log(compareDate('2020-02-20T22:40','2020-02-20T22:30')); // for test
    $('#form-add input[type=submit]').on('click',function(e){
      var input = $('input');
      var submit = true; //for check if validate input and check that is all right
      $.each(input,function(key,value){
        var field = $(this);

        //console.log("Input name: "+$(this).prop('name')+" , value: "+$(this).val());  //for test
      });

    $('.navigator .body ').on('click','.close', function(e){
        e.preventDefault();
        $('.navigator .body').css('left' , '-400px');
        $('.navigator').css("display", "none");
    });
     $('#form-add').submit(function(){
        return false;
      });
      if(submit){ // it will make requests to store new academi year
        //first store the academic year

        var data = {
            'title'               : $('input.academic-title').val(),
            'semester1_title'     : $('input.semester1-title').val(),
            'semester1_start_date': $('input.semester1-start').val(),
            'semester1_end_date': $('input.semester1-end').val(),
            'semester2_title'     : $('input.semester2-title').val(),
            'semester2_start_date': $('input.semester2-start').val(),
            'semester2_end_date': $('input.semester2-end').val(),

        };
        if(editForm)
          data['_method'] = 'PATCH';
        @if(isset($academic))
          var url = "{{route('academic_year.update',$academic->id)}}";
        @else
          var url = "{{route('academic_year.index')}}";
        @endif

        $.ajax({
          url: url,
          method: 'POST',
          headers:header,
          datatype: 'application/json',
          data: data,
          success: function(jsonData, status){
            if(editForm)
              var content = '<h6 class="text-center">Successfull Update Academic Year</h6>';
            else
              var content = '<h6 class="text-center">Successfull insert academic year</h6>';
            showNavigator(content);
            //to redirect to academic year list by default
            window.location.replace("{{url('/admin/academic')}}");
          },
          error: function(xhr, status, message){ // this error of store academic year

            console.log(xhr);
            console.log(message);
            if(editForm)
              var content = '<h6 style="text-decoration: underline;" class="text-center">Error to Update Academic Year</h6>';
            else
              var content = '<h6 style="text-decoration: underline;" class="text-center">Error Store New Academic Year</h6>';
            content+= '<ul><li>Error Message : '+message+'</li>';
            content+= '<li>Description: '+xhr['responseJSON']['error']+'</li></ul>';

            showNavigator(content,'error', 5000);
            $('#form-add').submit(function(){
              return false;
            });
          },
        });

      }
    });

  });


</script>

@endsection
