<?php $active = ['setting','school manager'];?>
@extends('admin.layout.app')

@section('title','Manager')
@section('sub-navbar')
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">School Manager</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('admin')}}">Home</a></li>
              <li class="breadcrumb-item active">School Manager</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
@endsection

@section('content')

         <div class="card">
            <div class="card-header">
              <h2 class="card-title">School Manager</h2>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="alert-message">

              </div>
                <div class="rule">
                    <h3>Currnet Academic Year and Semester</h3>
                    <p>It's very importent to choose what the current academic year, it will dependent on it another data.</p>
                    <select name="current_academic_year" id="academic-year" class="form-control academic-year">

                    </select>
                    <h6>Semester</h6>
                    <select name="current_academic_year" id="semester" class="form-control academic-year">

                    </select>
                    <div>
                        <button class="btn btn-primary btn-to-right" id="save-change">Save</button>
                    </div>

                </div>
            </div>
            <!-- /.card-body -->
          </div>
@endsection


@section('js')
<script type="text/javascript">
  $(function(){
      var token = "{{Auth::user()->api_token}}";
      var header = {'Authorization' : 'Bearer '+token,'Accept' : 'application/json'};
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
    //get all academic year that available of this current date and select the current academic year if it exist.
    function getAcademicYear(){
        $.ajax({
            url: "{{route('currentAcademicYear.show')}}",
            headers: header,
            method:'get',
            complete: function(xhr, status){
                console.log('test');
                console.log(xhr.responseJSON);
                var selected = status == 'success' ? xhr.responseJSON : -1;
                getAvailbleAcademicYear(selected);
            }
        });
    }
    function getAvailbleAcademicYear(id){
        console.log(id);
        $.ajax({
            url: "{{route('getAvailableAcademicYear')}}",
            method: 'get',
            headers: header,
            success: function(data, status,xhr){
                var academicYears = data;
                console.log(data);
                var content = '';
                if(Object.keys(academicYears).length == 0){
                    content += '<option value="-1" disable>Empty List</option>';
                }else{
                    if(id[0] <= 0) content += '<option value="0" disable>Not Selected Yet!</option>';
                    $.each(academicYears, function(key, academic){
                        content += '<option value="'+academic.id+'" ';
                        if(academic.id == id[0]){
                            content += 'selected ';
                            var semesterContent = '';
                            //append semesters to html
                            $.each(academic.semesters,function(key, semester){
                                semesterContent += '<option value="'+semester.id+'" ';
                                if(id[1] == semester.id) semesterContent +='selected';
                                semesterContent += ' >'+semester.title+'</option>';
                            });
                            $('#semester').html(semesterContent);
                        }
                        content += '>'+academic.title+'</option>';

                    });
                }
                //showNavigator('<h4>Successfull</h4>');
                $('#academic-year').addClass('exist').html(content);
            },
            error: function(xhr, status, message){
                showNavigator('<h6 class="text-center">Fail Save Changed</h6><p>- '+message+'</p>','error');
            }
        });
    }
    getAcademicYear();
    //for any change, it will get semseter
    $('body').on('change','#academic-year',function(){
        //for get semester automatic
        $.ajax({
            url: "{{url('/api/academic_year')}}/"+$(this).val(),
            method: 'get',
            headers: header,
            success: function(data, status){
                var content = '';
                console.log(data.data);
                var semesters = data.data.semesters;
                content += '<option value="'+semesters[0].id+'">'+semesters[0].title+'</option>';
                content += '<option value="'+semesters[1].id+'">'+semesters[1].title+'</option>';
                $('#semester').html(content);
            },
            error: function(xhr, status, message){
                showNavigator('<h6 class="text-center">Fail to get semesters<h6>');
            }
        });
    });
    //store and update academic year
    $('body').on('click','#save-change', function(){
        var academicId = $('#academic-year').val();
        var semesterId = $('#semester').val();
        if(academicId == null || semesterId == null) {
            showNavigator('<h6 class="text-center">You should select academic year and semester</h6>','error');
            return false;
        }
        $.ajax({
            url: "{{url('/api/current_academic_year')}}/"+$('#academic-year').val()+'/'+$('#semester').val(),
            method: 'post',
            headers: header,
            //data: {'current_academic': $('#academic-year').val()},
            success: function(data, status,xhr){

                showNavigator('<h6 class="text-center">The Current Academic Updated</h6>');
            },
            error: function(xhr, status, message){
                if(typeof xhr.responseJSON.error != 'undefined')
                    showNavigator('<h6 class="text-center">Fail Update '+message+'</h6><p>Error message: '+xhr.responseJSON.error+'</p>','error');
                else
                    showNavigator('<h6 class="text-center">Fail Update</h6><p>Error message: '+message+'</p>','error');
            }
        });
        return false;
    });
  });


</script>

@endsection
