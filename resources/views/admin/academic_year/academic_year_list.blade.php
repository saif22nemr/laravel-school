<?php $active = ['academicYear','all academicYear'];?>
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
              <li class="breadcrumb-item active">Academic Year</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
@endsection

@section('content')

         <div class="card">
            <div class="card-header">
              <h2 class="card-title">Academic Year List</h2>
              <div class="button-control">
                <a href="{{route('academic.create')}}" class="add"><i class="fas fa-plus"></i></a>
                <button class="delete deleteAll" id="deleteItem" type="button"><i class="fas fa-trash"></i></button>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="alert-message">

              </div>
              <form id="academicYear" class="form-list" action="{{route('academic.index')}}" method="get">
                <!-- <input type="hidden" name="_method" value="DELETE"> -->
                @if(count($data['academicYear']) <= 0 and !isset($_GET['search']))
                    <h3 class="text-center alert alert-warning">There No Academic Year Recoded Until</h3>
                @elseif(count($data['academicYear']) == 0 and isset($_GET['search']))
                @else
                <div class="row search-form">
                    <div class="col-sm-6">
                        <label for="sortby">Sort By</label>
                        <select name="sortBy" id="sortby">
                            <option value="created_at" @if($sort == 'created_at') selected @endif>Created At</option>
                            <option value="id" @if($sort == 'id') selected @endif>Academic Id</option>
                            <option value="title" @if($sort == 'title') selected @endif>Academic Title</option>
                        </select>
                        <select name="orderBy" id="orderby">
                            <option value="desc" @if($order == 'desc') selected @endif>DESC</option>
                            <option value="asc" @if($order == 'asc') selected @endif>ASC</option>
                        </select>
                    </div>
                    <div class="col-sm-6  text-right">
                        <label for="searchby">Search by</label>
                        <select name="searchby" id="searchby">
                            <option value="academic_id" @if($search == 'academic_id') selected @endif>Academic Id</option>
                            <option value="academic_title" @if($search == 'academic_title') selected @endif>Academic Title</option>
                            <option value="semester_title" @if($search == 'semester_title') selected @endif>Semester Title</option>
                            <option value="year" @if($search == 'year') selected @endif>Year</option>
                        </select>
                        <input type="search" name="search" placeholder="Enter you search value!!">
                    </div>
                </div>

              <table id="academicList" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th class="text-center">Selected<br><input type="checkbox" name="selectAll" class="selectAll"></th>
                  <th class="text-center"><a href="{{route('academic.index')}}?sort=id">Id</a></th>
                  <th class="text-center"><a href="{{route('academic.index')}}?sort=title">Title</a></th>
                  <th class="text-center"><a href="{{route('academic.index')}}?sort=status">First Semester</a></th>
                  <th class="text-center"><a href="{{route('academic.index')}}?sort=status">Second Semester</a></th>
                  <th class="text-center"><a href="{{route('academic.index')}}?sort=created_at">Start Date</a></th>
                  <th class="text-center"><a href="{{route('academic.index')}}?sort=updated_at">End Date</a></th>
                  <th class="text-center" style="font-weight: bold;font-family: 'Source Sans Pro';">Controll</th>

                </tr>
                </thead>
                <tbody>
                    @foreach($data['academicYear'] as $academic)
                        @if(count($academic->semesters) != 2)
                      <tr class="error-row">
                        @else
                      <tr>
                        @endif
                        <td class="text-center"><input type="checkbox" data-id="{{$academic->id}}" class="checked" name="checked[]" value="{{$academic->id}}"></td>
                        <td class="text-center">{{$academic->id}}</td>
                        <td>{{$academic->title}}</td>
                        <td>{{$academic->semesters[0]->title ?? ''}}</td>
                        <td>{{$academic->semesters[1]->title ?? ''}}</td>
                        <td class="text-center">{{$academic->semesters[0]->start_date ?? ''}}</td>
                        <td class="text-center">{{$academic->semesters[1]->end_date ?? ''}}</td>
                        <td class="text-center control">
                          <a class="btn btn-info" href="{{route('academic.edit',$academic->id)}}"><i class="fas fa-eye"></i></a>
                          <a class="btn btn-success" href="{{route('academic.edit',$academic->id)}}"><i class="fas fa-edit"></i></a>
                          <a class="btn btn-danger deleteOne deleteAll" data-id="{{$academic->id}}" href="{{route('academic.edit',$academic->id)}}"><i class="fas fa-trash"></i></a>
                        </td>
                      </tr>
                    @endforeach
                </tbody>
                <!-- <tfoot>
                <tr>
                  <th>Rendering engine</th>
                  <th>Browser</th>
                  <th>Platform(s)</th>
                  <th>Engine version</th>
                  <th>CSS grade</th>
                </tr>
                </tfoot> -->
              </table>
              @endif
            </form>  <!-- /#academicYear-->
            {{$data['academicYear']->links()}}
            </div>
            <!-- /.card-body -->
          </div>
@endsection

@section('footer')

<footer class="main-footer">
	<strong>Copyright &copy; 2014-2019 <a href="http://adminlte.io">AdminLTE.io</a>.</strong>
	All rights reserved.
		<div class="float-right d-none d-sm-inline-block">
		<b>Version</b> 3.0.1
	</div>
</footer>

@endsection
@section('js')
<script type="text/javascript">

  $(function(){ // ready
    //start search part
    //first sorted by to redirect automatically and sorted

    $('.search-form').on('change', '#sortby', function(e){
        var sortPart = '?sortby='+$(this).val() + '&orderby='+$(this).next('#orderby').val();
        window.location.replace("{{url('/admin/academic')}}"+ sortPart);
    });
    $('.search-form').on('change', '#orderby', function(e){
        var sortPart = '?sortby='+$(this).siblings('#sortby').val() + '&orderby='+$(this).val();
        window.location.replace("{{url('/admin/academic')}}"+ sortPart);
    });
    //end search part


    //checkbox
    //for make all check box checked
    $('input.selectAll').on('click',function(){
      var checkedBox = $('input.checked');
      if($(this).prop('checked') == true){
        checkedBox.prop('checked',true);
      }else{
        checkedBox.prop('checked',false);
      }
    });
    //when any check box is uncheced, the check box of all will be unchecked too.
    $('input.checked').on('click',function(){
      var checked = $(this);
      if(checked.prop('checked') == false){
        console.log('inside');
        $('.selectAll').prop('checked',false);
      }
    });
    // on click of button delete all item that checked it.
    $('.deleteAll').on('click',function(e){
        if($(this).hasClass('deleteOne')){
            var comfirm = 'true';
            e.preventDefault();
            var allItem = {'deleteOne':$(this)};
        }
        else{
            var comfirm = confirm("Are you sure to delete all of it?");
            var allItem = $('.checked');
        }
      if(comfirm == 'true' || comfirm){
        var token = "{{Auth()->user()->api_token}}";

        $.each(allItem,function(index){
          var thisItem = $(this);
          if((thisItem.prop('type') == 'checkbox' && thisItem.prop('checked') == true) || comfirm == 'true'){

            var header =  {'Authorization' : 'Bearer '+token,'Accept' : 'application/json'};
            var url = "{{route('academic_year.index')}}/"+thisItem.data('id');
              $.ajax({
                method: "POST",
                datatype: 'json',
                headers: header,
                data: {'_method':'DELETE'},
                url: url,
                success: function(data, status, xhr){
                  thisItem.parent().parent().fadeOut(100);
                },
                error: function(xhr, status){
                  console.log('status: '+status);
                  console.log(xhr);
                  alert('There some errors, try again!!');
                }
              });//end ajax
          }
        });//end each
        var content = '<div class="alert alert-success" style="display:none">Successfull Delete</div>';
        $('.alert-message').append(content);


      }
    }); //end function delete


  });


</script>

@endsection
