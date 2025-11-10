@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">Events</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item active">Events</li>
      </ol>
    </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                  <h3 class="card-title"><i class="fa fa-calendar mr-2"></i>Event List</h3>
                    @can('social_service_create')
                    <span class="float-right"><a class="btn btn-primary btn-sm" data-title="{{ _lang('Add New Event') }}" href="{{url('events/create')}}" data-fullscreen="true">{{ _lang('Add New Event') }}</a></span>
                    @endcan
                </div>
                <!-- /.card-header -->
                <div class="card-body">

                    <ul class="nav nav-tabs" id="custom-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-tabs-calendar-tab" data-toggle="pill" href="#custom-tabs-calendar" role="tab" aria-controls="custom-tabs-calendar" aria-selected="true">Calendar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-table-tab" data-toggle="pill" href="#custom-tabs-table" role="tab" aria-controls="custom-tabs-table" aria-selected="false">Table View</a>
                        </li>
                    </ul>

                    <div class="tab-content" id="custom-tabs-tabContent">
                        <div class="tab-pane fade active show" id="custom-tabs-calendar" role="tabpanel" aria-labelledby="custom-tabs-calendar-tab">
                            <div id="calendar"></div>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-table" role="tabpanel" aria-labelledby="custom-tabs-table-tab">

                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Filter Field') }}</label>
                                        <select class="form-control select2" name="filter_field" id="filter_field">
                                            <option value="">Please select a Field</option>
                                            <option value="name">Event Name</option>
                                            <option value="hosted_by">Hosted By</option>
                                            <option value="venue">Venue</option>
                                        </select>
                                        <span class="err-message">{{ _lang('filter_field is required') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Enter what to search') }}</label>
                                        <input type="text" class="form-control" name="filter_search" id="filter_search" placeholder="Search" />
                                        <span class="err-message">{{ _lang('Search Value is required') }}</span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('From Start Date') }}</label>
                                        <input type="date" class="form-control" name="filter_start_date" id="filter_start_date" placeholder="Search" />
                                        <span class="err-message">{{ _lang('From Start Date is required') }}</span>
                                    </div>
                                </div>

                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label class="control-label">--</label>
                                        <button class="btn btn-primary form-control" id="btn-search" type="button"><i class="fa fa-search mr-2"></i>Search</button>
                                        <span class="err-message">{{ _lang('filter_field is required') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="ajax-table" class="table table-bordered table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Actions</th>
                                            <th>Name</th>
                                            <th>Hosted By</th>
                                            <th>Venue</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Created Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group mt-3">
                                <label class="control-label mr-3">All Checked Items : </label>
                                <button class="btn btn-danger btn-multi-delete">Delete</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-css')
    <link rel="stylesheet" href="{{ asset('adminLTE/plugins/fullcalendar/main.css') }}">
@endsection

@section('js-script')
<script src="{{ asset('adminLTE/plugins/fullcalendar/main.js') }}"></script>
<script>
$(function () {

    $('#custom-tabs-table-tab').on('click', function(e){
        $('.select2').select2({width: '100%'});
    });

    $('#btn-search').on('click', function(e){
        let field_name = $('#filter_field').val();
        let filter_start_date = $('#filter_start_date').val();
        let search_value = $('#filter_search').val();
        if(field_name.length > 0 || filter_start_date.length > 0){
            let url = "?filter["+field_name+"]=" + search_value;
            if(filter_start_date.length > 0) {
                url += '&filter[start_date]=' + filter_start_date;
            }
            $('#ajax-table').DataTable().ajax.url(url).load();
        }else{
            $('#ajax-table').DataTable().ajax.url("").load();
        }
    });

    var date = new Date()
    var d    = date.getDate(),
        m    = date.getMonth(),
        y    = date.getFullYear()
    var Calendar = FullCalendar.Calendar;
    var calendarEl = document.getElementById('calendar');

    var fillCalendar = [
        @foreach($events as $event)
            {
            title          : '{{$event->name}}',
            start          : '{{$event->start_at}}',
            end            : '{{$event->end_at}}',
            backgroundColor: '{{$event->color}}',
            borderColor    : '{{$event->color}}',
            url            : "{{url('events/show').'/'.$event->id}}",
            },
        @endforeach
    ];

    var calendar = new Calendar(calendarEl, {
      headerToolbar: {
        left  : 'prev,next today',
        center: 'title',
        right : 'dayGridMonth,timeGridWeek,timeGridDay'
      },    
      themeSystem: 'bootstrap',
      events: fillCalendar,
      editable  : true,
      contentHeight: "auto"
    });

    calendar.render();

    /////////////

    var table = $("#ajax-table").DataTable({
        'searching': false,
        'orderCellsTop': true,
        'fixedHeader': true,
        'responsive': true, 
        "lengthChange": false, 
        "autoWidth": false,
        'lengthMenu': [
            [ 10, 25, 50],
            [ '10 rows', '25 rows', '50 rows']
        ],
        'processing': true,
        'serverSide': true,
        'serverMethod': 'get',
        'ajax': {
            'url':'{{url("events")}}',
            'data':{
                '_token' : '{{csrf_token()}}',
            },
            "dataSrc": function ( json ) {

                for ( var i=0, ien=json.data.length ; i<ien ; i++ ) {
                    let columnHtml = '<div class="btn-group">';
                    columnHtml += '<input type="checkbox" data-id="'+json.data[i]['id']+'" class="row-checkbox ml-2 mr-3 mt-2">';
                    columnHtml += '<a class="btn btn-success btn-sm" href="{{url("events/show")}}/'+json.data[i]['id']+'">View</a>';
                    columnHtml += '</div>';
                    columnHtml += '<a class="btn btn-primary btn-sm ml-1" data-title="{{ _lang("Edit Event") }}" href="{{url("events/edit")}}/'+json.data[i]['id']+'">Edit</a>';
                    json.data[i]['id'] = columnHtml;
                    json.data[i]['active'] = json.data[i]['active'] > 0 ? 'Active' : 'Inactive';
                }
                return json.data;
            }
        },
        'columns': [
            { data: 'id' },
            { data: 'name' },
            { data: 'hosted_by' },
            { data: 'venue' },
            { data: 'start_at' },
            { data: 'end_at' },
            { data: 'created_at' },
            { data: 'active' },
        ]
    });

    $('.btn-multi-delete').on('click', function(e){
        var data_arr = [];
        $('.row-checkbox:checked').each( function () {
            data_arr.push($(this).data('id'));
        });
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover the records",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url : "{{url('events/delete')}}",
                    type: "post",
                    data : {
                        'selected_ids' : data_arr,
                        '_token' : '{{csrf_token()}}'
                    },
                    success: function(data, textStatus, jqXHR)
                    {
                        swal("Records has been deleted!", {
                            icon: "success",
                        });
                        $("#ajax-table").DataTable().ajax.reload();
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        swal("Deletion failed!", {
                            icon: "danger",
                        });
                    }
                });
            }
        });
    });

});
</script>
@endsection