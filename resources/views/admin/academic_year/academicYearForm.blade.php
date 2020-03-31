<?php $active = ['academicYear', 'form academicYear'];?>
@extends('admin.layout.app')

@section('title','Academic Year')
@section('sub-navbar')
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Academic Year</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('admin')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{route('admin')}}">Academic Year</a></li>
              <li class="breadcrumb-item active">Edit</li>
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
        <div class="card-body">
          <div class="content-message">

          </div>
          <div class="form-group">
            <label for="exampleInputEmail1">Academic Year Title</label>
            <input type="text" <?php if(isset($academic)) echo 'value="'.$academic->title.'"'; ?> class="form-control academic-title" name="academic-title" id="exampleInputEmail1" placeholder="Enter title" required="">
            <div class="academic-title alert alert-danger display-none">The title not must be empty</div>
          </div>
          <div class="another-input">
            <h3 class="input-title">Semester 1</h3>
            <div class="form-group">
              <label for="exampleInput2">Semester Title</label>
              <input type="text" @if(isset($academic)) value="{{$academic->semesters[0]->title}}" @endif class="form-control semester1-title" id="exampleInput2" name="semester1-title" placeholder="Enter semester title" required="">
              <div class="semester1-title alert alert-danger display-none">The semester title not must be empty</div>
            </div>
            <div class="form-group">
              <label>Semester Start Date</label>

               <div class="input-group">
                <input type="date" @if(isset($academic)) value="{{$academic->semesters[0]->start_date}}" @endif name="semester1-start-date" class="form-control input-datetime semester1-start" placeholder="Enter the end datetime of semester" required="">
              </div>
              <div class="semester1-start-date alert alert-danger display-none">The semester start date must be type of datetime and not in the past</div>
              <!-- /.input group -->
            </div>
            <div class="form-group">
              <label>Semester End Date</label>
              <div class="input-group">
                <input type="date" @if(isset($academic)) value="{{$academic->semesters[0]->end_date}}" @endif name="semester1-end-date" class="form-control input-datetime semester1-end" placeholder="Enter the end datetime of semester" required="">
              </div>
              <div class="semester1-end-date alert alert-danger display-none">The semester end date must be type of datetime and it should be bigger than start date</div>
            </div>
        </div>
        <div class="another-input">
            <h3 class="input-title">Semester 2</h3>
            <div class="form-group">
              <label for="exampleInput22">Semester Title</label>
              <input type="text" @if(isset($academic)) value="{{$academic->semesters[1]->title}}" @endif class="form-control semester2-title" id="exampleInput22" name="semester2-title" placeholder="Enter semester title" required="">
              <div class="semester2-title alert alert-danger display-none">The semester title not must be empty</div>
            </div>
            <div class="form-group">
              <label>Semester Start Date</label>

               <div class="input-group">
                <input type="date" name="semester2-start-date"  @if(isset($academic)) value="{{$academic->semesters[1]->start_date}}" @endif class="form-control input-datetime semester2-start" placeholder="Enter the end datetime of semester" required="">
              </div>
              <div class="semester2-start-date alert alert-danger display-none">The semester start date must be type of datetime and not in the past</div>
              <!-- /.input group -->
            </div>
            <div class="form-group">
              <label>Semester End Date</label>
              <div class="input-group">
                <input type="date"  @if(isset($academic)) value="{{$academic->semesters[1]->end_date}}" @endif name="semester2-end-date" class="form-control input-datetime semester2-end" placeholder="Enter the end datetime of semester" required="">
              </div>
              <div class="semester2-end-date alert alert-danger display-none">The semester end date must be type of datetime and it should be bigger than start date</div>
            </div>
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


@section('js')
<script type="text/javascript">
  $(function(){ // ready
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
    //console.log(compareDate('2020-02-20T22:40','2020-02-20T22:30')); // for test
    $('#form-add input[type=submit]').on('click',function(e){
      var input = $('input');
      var submit = true; //for check if validate input and check that is all right
      $.each(input,function(key,value){
        var field = $(this);
        if(field.prop('name') == 'academic-title'){
          if(field.val().length < 2){
              submit = false;
            $('div.academic-title').fadeIn(400).delay(5000).fadeOut(400);
          }
        }else if(field.prop('name') == 'semester1-title'){
          if(field.val().length < 2){
              submit = false;
            $('div.semester1-title').fadeIn(400).delay(5000).fadeOut(400);
          }
        }
        else if(field.prop('name') == 'semester1-start-date'){
          if(!compareDate(field.val()) || field.val() == ''){ //compare this datetime with the current date
              submit = false;
            $('div.semester1-start-date').fadeIn(400).delay(5000).fadeOut(400);
          }
        }
        else if(field.prop('name') == 'semester1-end-date'){
          var temp = $('input');
          if(!compareDate(field.val(),$('input.semester1-start').val()) || field.val() == ''){
            //compare this datetime with start date
              submit = false;
            $('div.semester1-end-date').fadeIn(400).delay(5000).fadeOut(400);
          }
        }else if(field.prop('name') == 'semester2-title'){
          if(field.val().length < 2){
              submit = false;
            $('div.semester2-title').fadeIn(400).delay(5000).fadeOut(400);
          }
        }
        else if(field.prop('name') == 'semester2-start-date'){
          if(!compareDate(field.val()) || field.val() == ''){ //compare this datetime with the current date
              submit = false;
            $('div.semester2-start-date').fadeIn(400).delay(5000).fadeOut(400);
          }
        }
        else if(field.prop('name') == 'semester2-end-date'){
          var temp = $('input');
          if(!compareDate(field.val(),$('input.semester2-start').val()) || field.val() == ''){
            //compare this datetime with start date
              submit = false;
            $('div.semester2-end-date').fadeIn(400).delay(5000).fadeOut(400);
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
