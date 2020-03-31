<?php
use Illuminate\Support\Str;
$active = ['teacher', 'form teacher']; $title = isset($teacher)? 'Teacher Update' : 'New Teacher';?>
@extends('admin.layout.app')

@section('title',$title)
@section('sub-navbar')
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Teacher</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('/admin')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{url('/admin/teacher')}}">Teacher</a></li>
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
        <h3 class="card-title">@if(isset($teacher)) Update Teacher @else Add New Teacher @endif</h3>
      </div>
      <!-- /.card-header -->
      <!-- form start -->
      <form id="form-add" role="form" enctype="multipart/form-data">
        @csrf
        @if(isset($teacher))
            <input type="hidden" name="_method" value="PATCH">
        @endif
        <div class="card-body">
          <div class="content-message">

          </div>
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" <?php if(isset($teacher)) echo 'value="'.$teacher->info->fullname.'"'; ?> class="form-control" name="fullname" id="fullname"  placeholder="Enter teacher name !!" >
            <div class="alert alert-danger display-none" id="name-message">The name must be not empty</div>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" <?php if(isset($teacher)) echo 'value="'.$teacher->info->email.'"'; ?> class="form-control" name="email" id="email"  placeholder="Enter teacher email !!" >
            <div class="alert alert-danger display-none" id="email-message">Invalid Mail</div>
          </div>
          <div class="form-group">
            <label for="birthday">birthday</label>
            <input type="date" <?php if(isset($teacher)) echo 'value="'.$teacher->info->birthday.'"'; ?> class="form-control" name="birthday" id="birthday"  placeholder="Enter teacher birthday !!" >
            <div class="alert alert-danger display-none" id="birthday-message">The birthday must be not empty and must be date</div>
          </div>
          <div class="form-group">
            <label for="username">username</label>
            <input type="text" <?php if(isset($teacher)) echo 'value="'.$teacher->info->username.'"'; ?> class="form-control" name="username" id="username"  placeholder="Enter teacher username !!" >
            <div class="alert alert-danger display-none" id="username-message">The username must be not empty and must be date</div>
          </div>
          <div class="form-group">
            <label for="address">address</label>
            <input type="text" <?php if(isset($teacher)) echo 'value="'.$teacher->info->address.'"'; ?> class="form-control" name="address" id="address"  placeholder="Enter teacher address !!" >
            <div class="alert alert-danger display-none" id="address-message">The address must be not empty</div>
          </div>
          <div class="form-group">
            <label for="titleJob">Job Description</label>
            <input type="text" <?php if(isset($teacher)) echo 'value="'.$teacher->titleJob.'"'; ?> class="form-control" name="titleJob" id="titleJob"  placeholder="Enter teacher titleJob !!" >
            <div class="alert alert-danger display-none" id="titleJob-message">The titleJob must be not empty</div>
          </div>
          <div class="form-group">
            <label for="startDate">Start Date</label>
            <input type="date" <?php if(isset($teacher)) echo 'value="'.$teacher->info->startDate.'"'; ?> class="form-control" name="startDate" id="startDate"  placeholder="Enter teacher start date !!" >
            <div class="alert alert-danger display-none" id="startDate-message">The start date must not be empty and should be date</div>
          </div>
          <div class="form-group">
            <label for="salary">salary</label>
            <input type="text" <?php if(isset($teacher)) echo 'value="'.$teacher->salary.'"'; ?> class="form-control" name="salary" id="salary"  placeholder="Enter teacher salary !!" >
            <div class="alert alert-danger display-none" id="salary-message">The salary must be not empty and must be number</div>
          </div>

          <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" <?php if(isset($teacher->info->phones[0])) echo 'value="'.$teacher->info->phones[0]->phoneNumber.'"'; ?> class="form-control" name="phone[]" id="phone"  placeholder="Enter teacher phone number !!" >
            <div class="alert alert-danger display-none" id="phone-message">The phone must be number with min number 11 and max too</div>
          </div>
          <div class="form-group">
            <label for="image">image</label>
            <input type="file" class="form-control" name="image" id="image"  placeholder="Enter teacher image !!" >
            <div class="alert alert-danger display-none" id="image-message">The image must be number with min number 11 and max too</div>
          </div>

          <div class="form-group">
            <label for="password">password</label>
            <div class="password-generator clearfix">
                <input type="text" class="form-control" name="password" id="password"  placeholder="@if(isset($teacher)) If you write new password, it will changed @else Enter teacher Password @endif" >
                <button class="btn btn-secondary" id="generate-password">Generate Password</button>
            </div>

            <div class="alert clearfix alert-danger display-none" id="password-message">The password must be not empty and not less than 8 character and should be matched</div>
          </div>
          <div class="form-group clearfix">
            <label for="password-confirm">confirm password</label>
            <input type="text" class="form-control" name="password_confirmation" id="password_confirmation"  placeholder="Enter teacher password confiramtion !!" >
            <div class="alert alert-danger display-none" id="password_confirmation-message">The password must be not empty and not less than 8 character and should be matched</div>
          </div>



        </div>
        <!-- /.card-body -->

        <div class="card-footer text-center">
          <input type="submit" class="btn btn-primary text-center" @if(isset($teacher)) value="Save Updated" @else value="Add New" @endif>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection


@section('js')
<script type="text/javascript">
  $(function(){ // ready
    function generatePassword(length = 10) {
        var result           = '';
        var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for ( var i = 0; i < length; i++ ) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }
    function runGeneratePassword(){
        var password = generatePassword();
        $('#password').val(password);
        $('#password_confirmation').val(password);
    }
    runGeneratePassword();
    //button generate password
    $('#generate-password').on('click',function(){
        runGeneratePassword();
    });
    var editForm = false;
    @if(isset($teacher)) editForm = true; @endif
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


    $('#startDate').attr('value',(new Date()).toISOString().split('T')[0]);
    //console.log(compareDate('2020-02-20T22:40','2020-02-20T22:30')); // for test
    $('#form-add').submit(function(e){
        e.preventDefault();
        return false;
    });
    $('input[type=submit]').on('submit',function(e){
        console.log('inside submit');
        return false;
    });
    $('#form-add input[type=submit]').on('click',function(e){
      var input = $('input');
      var submit = true; //for check if validate input and check that is all right
      $.each(input,function(key,value){
        var field = $(this);
        if(field.prop('name') == 'fullname'){
          if(field.val().length <= 2){
              submit = false;
            $('#name-message').fadeIn(400).delay(5000).fadeOut(400);
          }
        }
        else if(field.prop('name') == 'email'){
          if(field.val().length <= 2){
              submit = false;
            $('#email-message').fadeIn(400).delay(5000).fadeOut(400);
          }
        }
        else if(field.prop('name') == 'username'){
          if(field.val().length <= 2){
              submit = false;
            $('#username-message').fadeIn(400).delay(5000).fadeOut(400);
          }
        }
        else if(field.prop('name') == 'address'){
          if(field.val().length <= 2){
              submit = false;
            $('#address-message').fadeIn(400).delay(5000).fadeOut(400);
          }
        }
        else if(field.prop('name') == 'birthday'){
            if(compareDate(field.val())) //if the birthday value is bigger than the crrent date
            {//the birthday must be less than the current date
                submit = false;
                $('#birthday-message').fadeIn(400).delay(5000).fadeOut(400);
            }
        }
        else if(field.prop('name') == 'startDate'){
            console.log('checked'+field.val());
            if(!compareDate(field.val()) && field.val() != (new Date()).toISOString().split('T')[0])
            {
                submit = false;
                $('#startDate-message').fadeIn(400).delay(5000).fadeOut(400);
            }
        }
       else if(field.prop('name') == 'titleJob'){
            if(field.val().length <= 2)
            {
                submit = false;
                $('#titleJob-message').fadeIn(400).delay(5000).fadeOut(400);
            }
        }
       else if(field.prop('name') == 'salary'){
            if(!$.isNumeric(field.val()) || parseInt(field.val()) <= 0)
            {
                submit = false;
                $('#salary-message').fadeIn(400).delay(5000).fadeOut(400);
            }
        }
        else if(field.prop('name') == 'image'){
            if(!editForm){
                if(field.val().length == 0)
                {
                    submit = false;
                    $('#image-message').fadeIn(400).delay(5000).fadeOut(400);
                }
            }

        }
        else if(field.prop('name') == 'phone[]'){
            console.log('inside phone and welcome to me');
            if(field.val().length != 11 || !$.isNumeric(field.val()))
            {
                submit = false;
                $('#phone-message').fadeIn(400).delay(5000).fadeOut(400);
            }
        }
        else if(field.prop('name') == 'password'){
            if(editForm){
                if((field.val().length != 0 && field.val().length < 8) || $('input[name=password_confirmation]').val() != field.val())
                {
                    submit = false;
                    $('#password-message').fadeIn(400).delay(5000).fadeOut(400);
                }
            }else{
                if(field.val().length < 8 || $('input[name=password_confirmation]').val() != field.val())
                {
                    submit = false;
                    $('#password-message').fadeIn(400).delay(5000).fadeOut(400);
                }
            }

        }
        else if(field.prop('name') == 'password_confirmation'){
            if(editForm){
                if(($('input[name=password]').val().length == 0 && field.val().length != 0 && field.val().length < 8 ) || $('input[name=password]').val() != field.val())
                {
                    submit = false;
                    $('#password_confirmation-message').fadeIn(400).delay(5000).fadeOut(400);
                }
            }else{
                if(field.val().length < 8 || $('input[name=password]').val() != field.val())
                {
                    submit = false;
                    $('#password_confirmation-message').fadeIn(400).delay(5000).fadeOut(400);
                }
            }
        }
        //console.log("Input name: "+$(this).prop('name')+" , value: "+$(this).val());  //for test
      });

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
        console.log('token: '+token); //for test
        var header =  {'Authorization' : 'Bearer '+token,'Accept' : 'application/json'};
        // var data = {
        //     'fullname': $('#fullname').val(),
        //     'username': $('#username').val(),
        //     'email': $('#email').val(),
        //     'address': $('#address').val(),
        //     'birthday': $('#birthday').val(),
        //     'startDate': $('#startDate').val(),
        //     'titleJob': $('#titleJob').val(),
        //     'salary': $('#salary').val(),
        //     'password' : $('#password').val(),
        //     'password_confirmation': $('#password_confirmation').val(),

        // };
        var formData = new FormData($('#form-add')[0]);//for get all input from this form and files too.
        console.log(formData);
        // if(editForm)
        //   formData['_method'] = 'PATCH';
        @if(isset($teacher))
          var url = "{{route('teacher.update',$teacher->id)}}";
        @else
          var url = "{{route('teacher.index')}}";
        @endif

        $.ajax({
          url: url,
          method: 'POST',
          headers:header,
          datatype: 'application/json',
          data: formData,
          processData: false,
          contentType: false,
          success: function(jsonData, status){

            if(editForm){
              var content = '<h6 class="text-center">Successfull Update Teacher '+jsonData.data.info.username+'</h6>';
            }
            else
              var content = '<h6 class="text-center">Successfull Insert New Teacher '+jsonData.data.info.username+'</h6>';
            showNavigator(content);
            //to redirect to academic year list by default
            window.location.replace("{{url('/admin/teacher')}}");
          },
          error: function(xhr, status, message){ // this error of store academic year

            console.log(xhr);
            console.log(message);
            if(editForm)
              var content = '<h6 style="text-decoration: underline;" class="text-center">Error to Update Teacher</h6>';
            else
              var content = '<h6 style="text-decoration: underline;" class="text-center">Error Delete Teacher</h6>';
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
