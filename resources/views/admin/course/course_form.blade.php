<?php
use Illuminate\Support\Str;
$active = ['course', 'form course']; $title = isset($course)? 'Course Update' : 'New Course';?>
@extends('admin.layout.app')

@section('title',$title)
@section('sub-navbar')
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Course</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('/admin')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{url('/admin/course')}}">Course</a></li>
              <li class="breadcrumb-item active">Add</li>
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
        <h3 class="card-title">@if(isset($course)) Update Course @else Add New Course @endif</h3>
      </div>
      <!-- /.card-header -->
      <!-- form start -->
      <form id="form-add" role="form" enctype="multipart/form-data">
        @csrf
        @if(isset($course))
            <input type="hidden" name="_method" value="PATCH">
        @endif
        <div class="card-body">
          <div class="content-message">

          </div>
          <div class="form-group">
            <label for="title">Coruse Title</label>
            <input type="text" <?php if(isset($course)) echo 'value="'.$course->title.'"'; ?> class="form-control" name="title" id="title"  placeholder="Enter course title !!" >
            <div class="alert alert-danger display-none" id="title-message">The title must be not empty</div>
          </div>
          <div class="form-group">
            <label for="description">Description</label>
            <input type="text" <?php if(isset($course)) echo 'value="'.$course->description.'"'; ?> class="form-control" name="description" id="description"  placeholder="Enter course description !!" >
            <div class="alert alert-danger display-none" id="description-message">The description must be not empty</div>
          </div>
          <div class="form-group">
            <label for="level">Level</label>
            <select class="form-control clearfix" name="level_id" id="level">
                    @foreach($levels as $index => $level)
                        <option value="{{$level->id}}" @if(isset($course) and $course->levels->id == $level->id) selected @endif>{{$level->title}}</option>
                    @endforeach
                </select>
            <div class="alert alert-danger display-none" id="level-message">You should choose one of this levels</div>

          </div>
          <!-- <div class="another-input level-control clearfix">
              <h3 class="input-title">Level Control</h3>
                <div class="add clearfix">
                    <div class="form-group">
                        <label for="level_title">Level Title</label>
                        <input type="text" class="form-control" name="level_title" id="level_title"  placeholder="Enter level title !!" >
                        <div class="alert alert-danger display-none" id="level_title-message">The level title must be not empty</div>
                    </div>
                    <div class="form-group">
                        <label for="stage">Stage</label>
                        <input type="text" class="form-control" name="stage" id="stage"  placeholder="Enter level stage !!" >
                        <div class="alert alert-danger display-none" id="stage-message">The level stage must be not empty</div>
                    </div>
                    <div class="form-group">
                        <label for="level_number">Level Title</label>
                        <input type="text" class="form-control" name="level_number" id="level_number"  placeholder="Enter level level_number !!" >
                        <div class="alert alert-danger display-none" id="level_number-message">The level number must be not empty</div>
                    </div>
                    <div class="form-group text-center">
                        <button class="btn btn-info text-center" id="add-level">Add New Level</button>
                        <button id="delete-level" class="btn btn-danger">Delete this one from selection box</button>
                    </div>
                </div>
            </div>



        </div> -->
        <!-- /.card-body -->

        <div class="card-footer text-center">
          <input type="submit" class="btn btn-primary text-center" @if(isset($course)) value="Save Updated" @else value="Add New" @endif>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection


@section('js')
<script type="text/javascript">
  $(function(){ // ready
    var token = "{{Auth()->user()->api_token}}";
    var editForm = false;
    @if(isset($course)) editForm = true; @endif
    //validate input of datetime , of class [input-datetime]
    function compareDate(datetime,datetime2 = null){
      //the function will recive datetime vaildate and it will compare it between it
      var date = datetime.split('T')[0]; //get date only
      if(datetime2 == null){
        var cdate = (new Date()).toISOString().split('T')[0]; //current date
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
/*
        $('#add-level').on('click',function(e){
        //validate input
        var allInput = $('.another-input input');
        var isValidate = true;
        var inputs = {};
        $.each(allInput, function(key, value){
            if($(this).prop('name') == 'level_title' && $(this).val().length < 2){
                $('#level_title-message').fadeIn(400).delay(5000).fadeOut(400);
                isValidate = false;
            }
            else if($(this).prop('name') == 'stage' && ($(this).val() == '' || !$.isNumeric($(this).val()))){
                $('#stage-message').fadeIn(400).delay(5000).fadeOut(400);
                isValidate = false;
            }
            else if($(this).prop('name') == 'level_number' && ($(this).val() == '' || !$.isNumeric($(this).val()))){
                $('#level_number-message').fadeIn(400).delay(5000).fadeOut(400);
                isValidate = false;
            }else{
                if($(this).prop('name') == 'level_title') var column = 'title';
                else var column = $(this).prop('name');
                inputs[column] = $(this).val();
            }
        });
        //store data by request
        if(isValidate){
             $.ajax({
                url: "{{route('level.store')}}",
                method: 'post',
                cache: false,
                headers : {'Authorization' : 'Bearer '+token,'Accept' : 'application/json'},
                data:inputs,
                success: function(data, status, xhr){
                    console.log(xhr);
                    showNavigator('<h6>Success Store New Level '+inputs.title+'</h6>');
                },
                error: function(xhr, status, message){
                    var content = '<h6>Fail to store Level</h6>';
                    content += '<ul><li>Error Message: '+message+'<li></ul>';
                    showNavigator(content, 'error',5000);
                }
            });

        }

        return false;
    });
*/

    $('#form-add').submit(function(e){
        e.preventDefault();
        return false;
    });
    $('input[type=submit]').on('submit',function(e){
        return false;
    });
    $('#form-add input[type=submit]').on('click',function(e){
      var input = $('input');
      var submit = true; //for check if validate input and check that is all right
      $.each(input,function(key,value){
        var field = $(this);
        if(field.prop('name') == 'title'){
          if(field.val().length < 2){
              submit = false;
            $('#title-message').fadeIn(400).delay(5000).fadeOut(400);
          }
        }
        else if(field.prop('name') == 'description'){
          if(field.val().length < 2){
              submit = false;
            $('#description-message').fadeIn(400).delay(5000).fadeOut(400);
          }
        }
        else if(field.prop('name') == 'level_id'){
          if(field.val().length == '' || !$.isNumeric(field.val())){
              submit = false;
            $('#level-message').fadeIn(400).delay(5000).fadeOut(400);
          }
        }

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
        var token = "{{Auth()->user()->api_token}}";
        var header =  {'Authorization' : 'Bearer '+token,'Accept' : 'application/json'};

        // };
        var formData = new FormData($('#form-add')[0]);//for get all input from this form and files too.
        // if(editForm)
        //   formData['_method'] = 'PATCH';
        @if(isset($course))
          var url = "{{url('/api/level')}}/"+$('#level').val()+'/course/{{$course->id}}';
        @else
          var url = "{{url('/api/level')}}/"+$('#level').val()+'/course';
        @endif
        console.log(url);
        $.ajax({
          url: url,
          method: 'POST',
          headers:header,
          datatype: 'application/json',
          data: formData,
          processData: false,
          contentType: false,
          success: function(jsonData, status,xhr){

            if(editForm){
              var content = '<h6 class="text-center">Successfull Update course '+jsonData.data.title+'</h6>';
            }
            else
              var content = '<h6 class="text-center">Successfull Insert New course '+jsonData.data.title+'</h6>';
            console.log($('#level').val());
            console.log(xhr);

            showNavigator(content);
            //to redirect to academic year list by default
            window.location.replace("{{url('/admin/course')}}");
          },
          error: function(xhr, status, message){ // this error of store academic year

            console.log(xhr);
            console.log(message);
            if(editForm)
              var content = '<h6 style="text-decoration: underline;" class="text-center">Error to Update course</h6>';
            else
              var content = '<h6 style="text-decoration: underline;" class="text-center">Error Delete course</h6>';
            content+= '<ul>';
            $.each(xhr['responseJSON'],function(key, value){
                console.log(typeof value);
                if(key == 'error' && typeof value == 'object'){
                    content += '<li>'+key.charAt(0).toUpperCase() + key.substr(1).toLowerCase()+':';
                    content += '<ul>';
                        $.each(value, function(index, message){
                            content += '<li>'+index.charAt(0).toUpperCase() + index.substr(1).toLowerCase()+' : '+message+'</li>';
                        });
                    content += '</ul>';
                    content += '</li>';
                }else
                    content += '<li>'+key.charAt(0).toUpperCase() + key.substr(1).toLowerCase()+': '+value+'</li>';
            });
            content+= '</ul>';

            showNavigator(content,'error', 7000);
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
