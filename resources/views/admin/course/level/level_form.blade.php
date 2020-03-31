<?php
use Illuminate\Support\Str;
$active = ['course', 'form level'];
if(isset($level)){
    //, 'Edit Level',"{{route('level.update',$level->id)}}"
    $active[] = 'Edit Level';
    $active[] = "{{url('/admin/level')}}";
}
$title = isset($level)? 'Level Update' : 'New Level';?>
@extends('admin.layout.app')

@section('title',$title)
@section('sub-navbar')
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Level</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('/admin')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{url('/admin/level')}}">Level</a></li>
              @if(!isset($level))
                <li class="breadcrumb-item active">Add</li>
              @else
                <li class="breadcrumb-item active">Edit</li>
              @endif
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
        <h3 class="card-title">@if(isset($level)) Update Level @else Add New Level @endif</h3>
      </div>
      <!-- /.card-header -->
      <!-- form start -->
      <form id="form-add" role="form" enctype="multipart/form-data">
        @csrf
        @if(isset($level))
            <input type="hidden" name="_method" value="PATCH">
        @endif
        <div class="card-body">
          <div class="content-message">

          </div>
          <div class="form-group">
            <label for="title">Level Title</label>
            <input type="text" <?php if(isset($level)) echo 'value="'.$level->title.'"'; ?> class="form-control" name="title" id="title"  placeholder="Enter level title !!" >
            <div class="alert alert-danger display-none" id="title-message">The title must be not empty</div>
          </div>
          <div class="form-group">
            <label for="stage">Level stage</label>
            <input type="text" <?php if(isset($level)) echo 'value="'.$level->stage.'"'; ?> class="form-control" name="stage" id="stage"  placeholder="Enter level stage !!" >
            <div class="alert alert-danger display-none" id="stage-message">The level stage must be not empty and should be integer</div>
          </div>
          <div class="form-group">
            <label for="level_number">Level Number</label>
            <input type="text" <?php if(isset($level)) echo 'value="'.$level->level_number.'"'; ?> class="form-control" name="level_number" id="level_number"  placeholder="Enter level number !!" >
            <div class="alert alert-danger display-none" id="level_number-message">The level number must be not empty and should be integer</div>
          </div>


        </div>
        <!-- /.card-body -->

        <div class="card-footer text-center">
          <input type="submit" class="btn btn-primary text-center" @if(isset($level)) value="Save Updated" @else value="Add New" @endif>
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
    @if(isset($level)) editForm = true; @endif
    //validate input of datetime , of class [input-datetime]

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
        else if(field.prop('name') == 'level_number'){
          if(field.val().length == '' || !$.isNumeric(field.val())){
              submit = false;
            $('#level_number-message').fadeIn(400).delay(5000).fadeOut(400);
          }
        }
        else if(field.prop('name') == 'stage'){
          if(field.val().length == '' || !$.isNumeric(field.val())){
              submit = false;
            $('#stage-message').fadeIn(400).delay(5000).fadeOut(400);
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
        @if(isset($level))
          var url = "{{route('level.update',$level->id)}}";
        @else
          var url = "{{route('level.store')}}";
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
              var content = '<h6 class="text-center">Successfull Update Level '+jsonData.data.title+'</h6>';
            }
            else
              var content = '<h6 class="text-center">Successfull Insert New Level '+jsonData.data.title+'</h6>';
            console.log($('#level').val());
            console.log(xhr);

            showNavigator(content);
            //to redirect to academic year list by default
            window.location.replace("{{url('/admin/level')}}");
          },
          error: function(xhr, status, message){ // this error of store academic year

            console.log(xhr);
            console.log(message);
            if(editForm)
              var content = '<h6 style="text-decoration: underline;" class="text-center">Error to Update Level</h6>';
            else
              var content = '<h6 style="text-decoration: underline;" class="text-center">Error Delete Level</h6>';
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
