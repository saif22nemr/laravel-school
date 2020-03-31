<?php $active = ['course','all level'];?>
@extends('admin.layout.app')

@section('title','Levels')
@section('sub-navbar')
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Level</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('admin')}}">Home</a></li>
              <li class="breadcrumb-item active">Level</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
@endsection

@section('content')

         <div class="card">
            <div class="card-header">
              <h2 class="card-title">Level List</h2>
              <div class="button-control">
                <a href="{{url('/admin/level/create')}}" class="add"><i class="fas fa-plus"></i></a>
                <button class="delete deleteAll" id="deleteItems" type="button"><i class="fas fa-trash"></i></button>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="alert-message">
                <h4 class="alert alert-warning text-center"><i class="fas fa-exclamation-triangle"></i> Take care, When you delete level, you will delete all course of this level</h4>
              </div>
              <form id="filter-form" class="form-list">
                <!-- <input type="hidden" name="_method" value="DELETE"> -->

                <h3 class="text-center alert alert-warning" id="empty-message" >The Coruse Table is Empty</h3>
                <h3 class="text-center alert alert-secondary " id="wait-message" >Wait To Loading .. </h3>
                <div class="row search-form front">
                    <div class="col-sm-6">
                        <label for="sortby">Sort By</label>
                        <select name="sortBy" id="sortby">
                            <option value="created_at" selected>Created At</option>
                            <option value="id">Id</option>
                            <option value="title">Title</option>
                            <option value="level_number">Level Number</option>
                            <option value="stage">Stage</option>
                        </select>
                        <select name="orderBy" id="orderby">
                            <option value="desc">DESC</option>
                            <option value="asc">ASC</option>
                        </select>
                    </div>
                    <div class="col-sm-6  text-right">
                        <label for="searchby">Search by</label>
                        <select name="searchby" id="searchby" class="searchby">
                            <option value="id">Id</option>
                            <option value="title" selected>Title</option>
                            <option value="stage">Stage</option>
                            <option value="level_number">Level Number</option>
                        </select>
                        <input type="search" name="search" id="search" placeholder="Enter you search value!!">
                    </div>
                </div>
                <div id="showList">
                </div>

            </form>  <!-- /#filter-form-->

            </div>
            <!-- /.card-body -->
          </div>
@endsection


@section('js')
<script type="text/javascript">
  $(function(){ // ready
    //global var
    var token = "{{Auth()->user()->api_token}}";
    var levels = null;
    var apiUrl = "{{route('level.index')}}";
    var emptyMessage = $('#empty-message');
    var waitMessage = $('#wait-message');
    var currentPage = parseInt($('#filter-form').attr('currentPage'));
    var searchForm = $('.search-form.front');


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
    //get all level by ajax
    function getLevels(url= apiUrl, filters = {}){
        $.ajax({
            url: url,
            method: 'get',
            headers : {'Authorization' : 'Bearer '+token,'Accept' : 'application/json'},
            data: filters,
            success: function(data){
                console.log(data);
                var levels = data.data.data;
                $('#filter-form').attr('currentPage',data.data.current_page);
                $('#filter-form').attr('lastPage',data.data.last_page);
                waitMessage.slideUp(300);
                if(levels.length == 0){
                    emptyMessage . slideDown(400);
                    $('#showList').html('');
                }
                else{
                    emptyMessage.slideUp(400);
                    searchForm.css('visibility','visible');
                    var content = '<table id="academicList" class="table table-bordered table-hover">'+
                    '<thead>'+
                    '<tr>'+
                    '<th class="text-center">Selected<br><input type="checkbox" name="selectAll" class="selectAll"></th>'+
                    '<th class="text-center"><a>Id</a></th>'+
                    '<th class="text-center"><a>Title</a></th>'+
                    '<th class="text-center"><a>Stage</a></th>'+
                    '<th class="text-center"><a>Level Number</a></th>'+
                    '<th class="text-center"><a>Created At</a></th>'+
                    '<th class="text-center" style="font-weight: bold;font-family: "Source Sans Pro";">Controll</th>'+
                    '</tr>'+
                    '</thead>'+
                    '<tbody>';
                        var rows = '';
                        $.each(levels, function(index,level){
                            rows += '<tr> '+
                                '<td class="text-center"><input type="checkbox" data-id="'+level.id+'" class="checked" name="checked[]" value="'+level.id+'"></td>'+
                                '<td>'+level.id+'</td>'+
                                '<td>'+level.title+'</td>'+
                                '<td>'+level.level_number+'</td>'+
                                '<td>'+level.stage+'</td>'+
                                '<td>'+level.created_at+'</td>'+
                                '<td class="text-center control">'+
                                    '<a class="btn btn-info" href="{{url("/level")}}/'+level.id+'/edit"><i class="fas fa-eye"></i></a>'+
                                    '<a class="btn btn-success" href="{{url("/admin/level")}}/'+level.id+'/edit"><i class="fas fa-edit"></i></a>'+
                                    '<a class="btn btn-danger deleteOne" data-id="'+level.id+'" href="#"><i class="fas fa-trash"></i></a>'+
                                '</td>'+
                        '</tr>';
                        });
                        content += rows;
                    content += '</tbody>'+
                '</table>'+
                '<div class="pages">'+
                        '<button class="btn" id="goPrevious"><i class="fas fa-arrow-left"></i></button>'+
                        '<span> Current Page '+ data.data.current_page +' of '+ data.data.last_page +' </span>'+
                        '<button class="btn " id="goNext"><i class="fas fa-arrow-right"></i></button>'+
                '</div>';
                $('#showList').html(content);
                }
            },
            error: function(xhr, status, message){
                console.log('error happen');
                console.log(xhr);
                var content = '<h6 style="text-decoration: underline;" class="text-center">Error Get level Table</h6>';
                content+= '<ul>';
                $.each(xhr['responseJSON'],function(key, value){
                    content += '<li>'+key.charAt(0).toUpperCase() + key.substr(1).toLowerCase()+': '+value+'</li>';
                });
                content+= '</ul>';
                showNavigator(content,'error');
            },
        });
    }
    getLevels();
    function getFilters(page = 1){
        var filter = {
            sortby : $('#sortby').val(),
            orderby: $('#orderby').val(),
        }
        var search = $('#search');
        var searchBy = $('.searchby');
        if(search.val() != '' || search.val().length != 0){
            filter['search']   = search.val();
            filter['searchby'] = $('.searchby').val();
        }
        filter['page'] = page;
        return filter;
    }
    //controll pages

    $('div').on('click','#goNext',function(e){
        var filterForm = $('#filter-form');
        var currentPage = parseInt(filterForm.attr('currentPage'));
        var lastPage = parseInt(filterForm.attr('lastPage'));
        console.log('current page: '+ currentPage+', lastPage: '+lastPage);
        if(currentPage >= lastPage) {
            //show this is last page must stop here
            console.log('this last page must stop .. !');
        }
        else{

            getLevels(apiUrl, getFilters(currentPage+1));
        }
        return false; // for not repeate function by here self
    });
    $('div').on('click','#goPrevious',function(e){
        var filterForm = $('#filter-form');
        var currentPage = parseInt(filterForm.attr('currentPage'));
        var lastPage = parseInt(filterForm.attr('lastPage'));
        if(currentPage > lastPage || currentPage <= 0) {
            // it should show message of this last page
            console.log('show message error you go lower from that .. !');
        }
        else{
            getLevels(apiUrl, getFilters(currentPage-1));
        }
        return false; // for not repeate function by here self
    });
    //start search part
    //first sorted and redirect automatically and sorted
    $('#filter-form').submit(function(e){
        e.preventDefault();
        return false;
    });

    $('.search-form').on('change', '#sortby', function(e){
        var currentPage = parseInt($('#filter-form').attr('currentPage'));
        var filter = getFilters(currentPage);
        console.log(filter);
        //console.log(filter);
        getLevels(apiUrl,filter);
    });
    $('.search-form').on('change', '#orderby', function(e){
        //var sortPart = '?sortby='+$(this).siblings('#sortby').val() + '&orderby='+$(this).val();

        //var currentPage = parseInt($('#filter-form').attr('currentPage'));
        //var filter = getFilters(currentPage);
        var filter = getFilters();
        //console.log(filter);
        getLevels(apiUrl,filter);
    });
    $('.search-form').on('keyup','#search',function(e){
        var key = e.which;
        if(key == 13)// when click enter
        {
            var currentPage = parseInt($('#filter-form').attr('currentPage'));
            var filter = getFilters(currentPage);
            //console.log(filter);
            getLevels(apiUrl, filter);
        }
    });
    //end search part


    //checkbox
    //for make all check box checked
    $('form').delegate('input.selectAll','click',function(){
      var checkedBox = $('input.checked');
      if($(this).prop('checked') == true){
        checkedBox.prop('checked',true);
      }else{
        checkedBox.prop('checked',false);
      }
    });
    //when any check box is uncheced, the check box of all will be unchecked too.
    $('form').delegate('input.checked','click',function(){
      var checked = $(this);
      if(checked.prop('checked') == false){
        $('.selectAll').prop('checked',false);
      }
    });
    //delete one
    $('div').on('click', '.deleteOne',function(e){
        e.preventDefault();
        if(confirm("Take care, When you delete level, you will delete all course of this level\nAre you sure to delete this level?")){
            var thisItem = $(this);
            var header =  {'Authorization' : 'Bearer '+token,'Accept' : 'application/json'};
            var url = "{{url('/api/level')}}/"+thisItem.data('id');
            $.ajax({
                method: "POST",
                datatype: 'json',
                headers: header,
                data: {'_method':'DELETE'},
                url: url,
                success: function(data, status, xhr){
                    thisItem.parent().parent().fadeOut(50);
                },
                error: function(xhr, status){
                    console.log('status: '+status);
                    console.log(xhr);
                    var content = '<h6 style="text-decoration: underline;" class="text-center">Error Delete level</h6>';
                    content+= '<ul>';
                    $.each(xhr['responseJSON'],function(key, value){
                        content += '<li>'+key.charAt(0).toUpperCase() + key.substr(1).toLowerCase()+': '+value+'</li>';
                    });
                    content+= '</ul>';
                    showNavigator(content,'error');
                }
            });//end ajax
        }

        return false;
    });

    // on click the button delete all item that checked it.
    $('div').delegate('#deleteItems','click',function(e){
      var allItem = $('.checked');
      var check = false;
      $.each(allItem, function(value){
          if($(this).prop('checked') == true){
            check = true;
          }
      });
      if(!check) return false;
      else if(confirm("Take care, When you delete level, you will delete all course of this level\nAre you sure to delete all of it?")){
        $.each(allItem,function(index){
          var thisItem = $(this);
          if((thisItem.prop('type') == 'checkbox' && thisItem.prop('checked') == true)){

            var header =  {'Authorization' : 'Bearer '+token,'Accept' : 'application/json'};
            var url = "{{url('/api/level')}}/"+thisItem.data('id');
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
                  var content = '<h6 style="text-decoration: underline;" class="text-center">Error Delete level</h6>';
                content+= '<ul>';
                    $.each(xhr['responseJSON'],function(key, value){
                        content += '<li>'+key.charAt(0).toUpperCase() + key.substr(1).toLowerCase()+': '+value+'</li>';
                    });
                    content+= '</ul>';
                    showNavigator(content,'error');
                }
              });//end ajax
          }
        });//end each
        var content = '<div class="alert alert-success" style="display:none">Successfull Delete</div>';
        $('.alert-message').append(content);

      }
      return false;
    }); //end function delete


  });


</script>

@endsection
