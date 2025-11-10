@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">Social Service Assistance</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item active">Social Service Assistance</li>
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
                  <h3 class="card-title">Social Service Assistance List</h3>
                    @can('social_service_create')

                    <span class="float-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-qrcode"></i> Release by ID QR
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#" id="btn-scan-qr" data-title="{{ _lang('Via Camera') }}">
                                <i class="fa fa-qrcode mr-2"></i> via Camera
                                </a>
                                <a class="dropdown-item" href="#" id="btn-scan-qr-alt" data-title="{{ _lang('Via Device / Manual') }}">
                                <i class="fa fa-qrcode mr-2"></i> via Device / Manual
                                </a>
                                <!-- Add more dropdown items here if needed -->
                            </div>
                        </div>
                    </span>

                    <span class="float-right"><a class="btn btn-primary btn-sm mr-2" data-title="{{ _lang('Add New Request') }}" href="{{url('social_services/create')}}" data-fullscreen="true"><i class="fa fa-plus mr-2"></i>Add New (Manual)</a></span>

                    <span class="float-right"><a class="btn btn-primary btn-sm mr-2" href="#" data-target="#add_new_modal" data-toggle="modal"><i class="fa fa-plus mr-2"></i>Add New From Voters</a></span>
                    @endcan
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    
                    <ul class="nav nav-tabs mb-3">
						<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#all" data-status="">{{ _lang('All') }}</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#pending" data-status="Pending">{{ _lang('Pending') }}</a></li>
						<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#approved" data-status="Approved">{{ _lang('Approved') }}</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#released" data-status="Released">{{ _lang('Released') }}</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#on_hold" data-status="On-hold">{{ _lang('On-Hold') }}</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#rejected" data-status="Rejected">{{ _lang('Rejected') }}</a></li>
                        <li class="nav-item"><a class="nav-link text-danger" data-toggle="tab" href="#for_validation" data-status="For-validation">{{ _lang('For-Validation') }}</a></li>
                        <li class="nav-item"><a class="nav-link text-danger" data-toggle="tab" href="#for_delete" data-status="For-Delete">{{ _lang('For-Delete') }}</a></li>
					</ul>
                    <div class="tab-content">
                        <div class="table-responsive">
                            <div class="row">

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Request Type') }}</label>
                                        <select class="form-control select2" name="request_type_search" id="request_type_search">
                                            <option value="">All Request Type</option>
                                            @foreach($request_types as $request_type)
                                                <option value="{{$request_type->id}}">{{$request_type->name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="err-message">{{ _lang('Request Type is required') }}</span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Encoder') }}</label>
                                        <select class="form-control select2" name="encoder_search" id="encoder_search">
                                            <option value="">All Encoders</option>
                                            @foreach($users as $user)
                                                <option value="{{$user->id}}">{{$user->full_name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="err-message">{{ _lang('Encoder is required') }}</span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Approver') }}</label>
                                        <select class="form-control select2" name="approver_search" id="approver_search">
                                            <option value="">All Approvers</option>
                                            @foreach($users as $user)
                                                <option value="{{$user->id}}">{{$user->full_name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="err-message">{{ _lang('Approver is required') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Releaser') }}</label>
                                        <select class="form-control select2" name="releaser_search" id="releaser_search">
                                            <option value="">All Releasers</option>
                                            @foreach($users as $user)
                                                <option value="{{$user->id}}">{{$user->full_name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="err-message">{{ _lang('Releaser is required') }}</span>
                                    </div>
                                </div>

                                <!-- <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Source') }}</label>
                                        <select class="form-control" name="source_search" id="source_search">
                                            <option value="">
                                                ALL
                                            </option>
                                        </select>
                                    </div>
                                </div> -->
                            </div>

                            <table id="ajax-table" class="table table-bordered table-sm table-striped" style="white-space: nowrap;">
                                <thead>
                                    <tr>
                                        <th>Actions</th>
                                        <th>Status</th>
                                        <th>Control Number</th>
										<th>Requestor</th>
										<th>Received By</th>
                                        <th>First Name</th>
                                        <th>Middle Name</th>
                                        <th>Last Name</th>
                                        <th>Suffix</th>
                                        <th>Contact Number</th>
                                        <th>Barangay</th>
                                        <th>Address</th>
                                        <th>Organization</th>
                                        <th>Purpose</th>
                                        <th>Remarks</th>
                                        <th>Referrred By</th>
                                        <th>Date Filed</th>
                                        <th>Date Processed</th>
                                        <th>Date Released</th>
                                        <th>Amount</th>
                                        <th>Approved By</th>
                                        <th>Encoder</th>
                                        <th>Source</th>
                                        <th>Created At</th>
                                    </tr>
                                    <tr>
                                        <th>Actions</th>
                                        <th>Status</th>
                                        <th>Control Number</th>
										<th>Requestor</th>
										<th>Received By</th>
                                        <th>First Name</th>
                                        <th>Middle Name</th>
                                        <th>Last Name</th>
                                        <th>Suffix</th>
                                        <th>Contact Number</th>
                                        <th>Barangay</th>
                                        <th>Address</th>
                                        <th>Organization</th>
                                        <th>Purpose</th>
                                        <th>Remarks</th>
                                        <th>Referrred By</th>
                                        <th>Date Filed</th>
                                        <th>Date Processed</th>
                                        <th>Date Released</th>
                                        <th>Amount</th>
                                        <th>Approved By</th>
                                        <th>Encoder</th>
                                        <th>Source</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Actions</th>
                                        <th>Status</th>
                                        <th>Control Number</th>
										<th>Requestor</th>
										<th>Received By</th>
                                        <th>First Name</th>
                                        <th>Middle Name</th>
                                        <th>Last Name</th>
                                        <th>Suffix</th>
                                        <th>Contact Number</th>
                                        <th>Barangay</th>
                                        <th>Address</th>
                                        <th>Organization</th>
                                        <th>Purpose</th>
                                        <th>Remarks</th>
                                        <th>Referrred By</th>
                                        <th>Date Filed</th>
                                        <th>Date Processed</th>
                                        <th>Date Released</th>
                                        <th>Amount</th>
                                        <th>Approved By</th>
                                        <th>Encoder</th>
                                        <th>Source</th>
                                        <th>Created At</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="form-group standard-buttons mt-3">
                        <label class="control-label mr-3">All Checked Items : </label>
                        @can('social_service_status_update')
                        <button class="btn btn-success btn-change-status" data-status="{{App\SocialServiceAssistance::STATUS_APPROVED}}">Approved</button>
                        <button class="btn btn-warning btn-change-status" data-status="{{App\SocialServiceAssistance::STATUS_ON_HOLD}}">On-Hold</button>
                        <button class="btn btn-danger btn-change-status" data-status="{{App\SocialServiceAssistance::STATUS_REJECTED}}">Rejected</button>
                        <button class="btn btn-primary btn-change-status" data-status="{{App\SocialServiceAssistance::STATUS_PENDING}}">Pending</button>
                        @endcan
                        <button class="btn btn-danger btn-change-status" data-status="{{App\SocialServiceAssistance::STATUS_FOR_DELETE}}">For-Delete</button>
                    </div>

                    <div class="form-group for-delete-buttons mt-3" style="display:none;">
                        @can('social_service_status_update')
                        <label class="control-label mr-3">All Checked Items : </label>
                        <button class="btn btn-danger btn-multi-delete"><i class="fa fa-trash"></i> Hard Delete</button>
                        @endcan
                    </div>
                    
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
        </div>
    </div>
</div>
@endsection

@section('content-modal')

<div id="add_new_modal" class="modal animated bounceInDown" role="dialog">
    <div class="modal-dialog modal-dialog-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ _lang('Add New Social Service Assistance') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="alert alert-danger" style="display:none; margin: 15px;"></div>
        <div class="alert alert-success" style="display:none; margin: 15px;"></div>			  
        <div class="modal-body" style="overflow:hidden;">
            <div class="row mb-4">
                <div class="col-md-12">
                    <label class="control-label">{{ _lang('Search From Voters') }} : </label>
                    <select class="form-control select-2-ajax" id="voter_list">
                        <option value="">Please select from existing records</option>
                    </select>
                </div>
            </div>
            <div class="voter-info row mb-4" style="overflow-y:auto;max-height:500px;display:none;">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        Voter Information
                    </div>
                    <table class="table table-sm table-bordered mb-3">
                        <tr><td><strong>{{ _lang('Full Name') }}</strong></td><td id="full_name"></td></tr>
                        <tr><td><strong>{{ _lang('Barangay') }}</strong></td><td id="brgy"></td></tr>
                        <tr><td><strong>{{ _lang('Address') }}</strong></td><td id="address"></td></tr>
                        <tr><td><strong>{{ _lang('Birth date') }}</strong></td><td id="birth_date"></td></tr>
                        <tr><td><strong>{{ _lang('Gender') }}</strong></td><td id="gender"></td></tr>
                        <tr><td><strong>{{ _lang('Precinct') }}</strong></td><td id="precinct"></td></tr>
                    </table>
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-success btn-lg btn-add-voter-to-ss shadow" style="min-width:260px;font-size:1.1rem;">
                            <i class="fa fa-plus mr-2"></i>{{ _lang('Create Social Service') }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-12">
                    <!-- <div class="form-group">
                        <a class="btn btn-warning" data-title="{{ _lang('Add Non-Voter') }}" href="{{url('social_services/create')}}" data-fullscreen="true">{{ _lang('Create New Beneficiary') }}</a>
                    </div> -->
                </div>
            </div>
        </div>
      </div>
  </div>
</div>

<div id="scan_qr_modal" class="modal animated bounceInDown" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="max-width:438px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> Scan QR Code Via Camera </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="qr-reader" style="width: 400px"></div>
            </div>
        </div>
    </div>
</div>

<div id="member_assistances_list_modal" class="modal animated bounceInDown" tabindex="-1" role="dialog" style="z-index: 1040;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> Release Assistance (Member) </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="member_ss_list"></div>
            </div>
        </div>
    </div>
</div>

<div id="scan_qr_modal_alt" class="modal animated bounceInDown" tabindex="-1" role="dialog">
  <div class="modal-dialog" style="max-width:438px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Scan QR Code Via Device / Manual </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close-scan-qr-modal-alt">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="input-group">
          <input type="text" class="form-control" placeholder="Enter ID Number Code or QR Value" id="scan_id_number" autofocus>
          <div class="input-group-append">
            <button class="btn btn-primary btn-sm" type="button" id="submit_scan_id_number">
              <i class="fa fa-paper-plane"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('js-script')

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
$(function () {

    $('.btn-change-status').on('click', function(e){
        var data_arr = [];
        var selected_status = $(this).data('status');
        $('.row-checkbox:checked').each( function () {
            data_arr.push($(this).data('id'));
        });
        swal({
            title: "Are you sure?",
            text: "You want to update all checked records to "+selected_status+" ?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url : "{{url('social_service/update_status_multiple')}}",
                    type: "post",
                    data : {
                        'selected_ids' : data_arr,
                        'status' : selected_status,
                        '_token' : '{{csrf_token()}}'
                    },
                    success: function(data, textStatus, jqXHR)
                    {
                        $("#ajax-table").DataTable().ajax.reload();
                        swal("Records has been updated!", {
                            icon: "success",
                        });
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        swal("Update failed!", {
                            icon: "danger",
                        });
                    }
                });
            }
        });
    });

    $('#ajax-table thead tr:eq(1) th').each( function () {
        var title = $(this).text();
        if(title != 'Encoder' && title != 'Approved By' && title != 'Actions' && title.length > 0){
            $(this).html( '<input type="text" placeholder="Search '+title+'" class="column_search" />' );
        }else{
            $(this).html('');
        }
    });

    var selected_status = '';

    $('.nav-tabs .nav-link').on('click', function(e){
        let search_status = $(this).data('status');
        if(search_status == 'For-Delete'){
            $('.standard-buttons').hide();
            $('.for-delete-buttons').show();
        }else{
            $('.standard-buttons').show();
            $('.for-delete-buttons').hide();
        }
        selected_status = search_status;
        let encoder = $('#encoder_search').val();
        let approver = $('#approver_search').val();
        let request_type = $('#request_type_search').val();
        let releaser = $('#releaser_search').val();
        let source = $('#source_search').val();
        $('#ajax-table').DataTable().ajax.url("?search_status=" + search_status + "&encoder_search=" + encoder + "&approver_search=" + approver  + "&releaser_search=" + releaser + "&request_type_search=" + request_type + "&source_search=" + source).load();
    });

    $('#encoder_search').on('change', function(e){
        let encoder = $(this).val();
        let approver = $('#approver_search').val();
        let request_type = $('#request_type_search').val();
        let releaser = $('#releaser_search').val();
        let source = $('#source_search').val();
        $('#ajax-table').DataTable().ajax.url("?search_status=" + selected_status + "&encoder_search=" + encoder + "&approver_search=" + approver  + "&releaser_search=" + releaser + "&request_type_search=" + request_type + "&source_search=" + source).load();
    });

    $('#approver_search').on('change', function(e){
        let encoder = $('#encoder_search').val();
        let approver = $(this).val();
        let request_type = $('#request_type_search').val();
        let releaser = $('#releaser_search').val();
        let source = $('#source_search').val();
        $('#ajax-table').DataTable().ajax.url("?search_status=" + selected_status + "&encoder_search=" + encoder + "&approver_search=" + approver  + "&releaser_search=" + releaser + "&request_type_search=" + request_type + "&source_search=" + source).load();
    });

    $('#releaser_search').on('change', function(e){
        let encoder = $('#encoder_search').val();
        let approver = $('#approver_search').val();
        let request_type = $('#request_type_search').val();
        let releaser = $(this).val();
        let source = $('#source_search').val();
        $('#ajax-table').DataTable().ajax.url("?search_status=" + selected_status + "&encoder_search=" + encoder + "&approver_search=" + approver  + "&releaser_search=" + releaser + "&request_type_search=" + request_type + "&source_search=" + source).load();
    });

    $('#request_type_search').on('change', function(e){
        let encoder = $('#encoder_search').val();
        let approver = $('#approver_search').val();
        let request_type = $(this).val();
        let releaser = $('#releaser_search').val();
        let source = $('#source_search').val();
        $('#ajax-table').DataTable().ajax.url("?search_status=" + selected_status + "&encoder_search=" + encoder + "&approver_search=" + approver  + "&releaser_search=" + releaser + "&request_type_search=" + request_type + "&source_search=" + source).load();
    });

    $('#source_search').on('change', function(e){
        let encoder = $('#encoder_search').val();
        let approver = $('#approver_search').val();
        let request_type = $('#request_type_search').val();
        let releaser = $('#releaser_search').val();
        let source =  $(this).val();
        $('#ajax-table').DataTable().ajax.url("?search_status=" + selected_status + "&encoder_search=" + encoder + "&approver_search=" + approver  + "&releaser_search=" + releaser + "&request_type_search=" + request_type + "&source_search=" + source).load();
    });

    var table = $("#ajax-table").DataTable({
        'orderCellsTop': true,
        'fixedHeader': true,
        'responsive': true, 
        "lengthChange": false, 
        "autoWidth": false,
        "order": [[21, 'desc']],
        'buttons': ["copy", "csv", "excel", "pdf", "print", "pageLength"],
        'lengthMenu': [
            [ 10, 25, 50],
            [ '10 rows', '25 rows', '50 rows']
        ],
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'initComplete': function(settings, json) {
            table.buttons().container().appendTo('#ajax-table_wrapper .col-md-6:eq(0)');
            this.api()
                .columns()
                .every(function () {
                    var that = this;
                    $('input', this.footer()).on('keyup change clear', function () {
                        if (that.search() !== this.value) {
                            that.search(this.value).draw();
                        }
                    });
                });
        },
        'ajax': {
            'url':'{{url("social_services")}}',
            'data':{
                '_token' : '{{csrf_token()}}',
            },
            "dataSrc": function ( json ) {

                for ( var i=0, ien=json.data.length ; i<ien ; i++ ) {
                    
                    let columnHtml = '<div class="btn-group">';
					
					if(json.data[i]['status'] !== 'Released') {
						columnHtml += '<input type="checkbox" data-id="'+json.data[i]['id']+'" class="row-checkbox ml-2 mr-3 mt-2">';
                    }
					columnHtml += '<a class="btn btn-success btn-sm ajax-modal" href="#" data-title="{{ _lang('Show Request Information') }}" data-href="{{url('social_service/show')}}/'+json.data[i]['id']+'">View</a>';
                    
                    @if(Auth::user()->can('social_service_update'))

                    columnHtml += '<a class="btn btn-primary btn-sm ml-1" data-title="{{ _lang('Edit Request') }}" href="{{url('social_services/edit')}}/'+json.data[i]['id']+'">Edit</a>';

                    @else
                    
                    if(json.data[i]['status'] == 'Pending' || json.data[i]['status'] == 'For-validation'){
                        columnHtml += '<a class="btn btn-primary btn-sm ml-1" data-title="{{ _lang('Edit Request') }}" href="{{url('social_services/edit')}}/'+json.data[i]['id']+'">Edit</a>';
                    }

                    @endif

                    if(json.data[i]['status'] == 'Approved'){
                        columnHtml += '<a class="btn btn-warning btn-sm ml-1 ajax-modal" href="#" data-title="{{ _lang('Release Request - Control Number:') }} '+json.data[i]['control_number']+'" data-href="{{url('social_service/release')}}/'+json.data[i]['id']+'">Release</a>';
                    }

                    columnHtml += '</div>';
                    json.data[i]['id'] = columnHtml;
                    
                    switch(json.data[i]['status']){
                        case 'Pending':
                            json.data[i]['status'] = '<span class="badge badge-info">Pending</span>';
                        break
                        case 'On-hold':
                            json.data[i]['status'] = '<span class="badge badge-warning">On-hold</span>';
                        break
                        case 'Rejected':
                            json.data[i]['status'] = '<span class="badge badge-danger">Rejected</span>';
                        break
                        case 'Approved':
                            json.data[i]['status'] = '<span class="badge badge-success">Approved</span>';
                        break
                        case 'For-validation':
                            json.data[i]['status'] = '<span class="badge badge-danger">For-Validation</span>';
                        break
                        case 'For-delete':
                            json.data[i]['status'] = '<span class="badge badge-danger">For-Delete</span>';
                        break
                        case 'Released':
                            json.data[i]['status'] = '<span class="badge badge-primary">Released</span>';
                        break
                        default:
                            json.data[i]['status'] = '<span class="badge badge-secondary">'+json.data[i]['status']+'</span>';
                    }

                }
                return json.data;
            }
        },
        'columns': [
            { data: 'id' },
            { data: 'status' },
            { data: 'control_number' },
			{ data: 'requestor_full_name' },
			{ data: 'received_by' },
            { data: 'first_name' },
            { data: 'middle_name' },
            { data: 'last_name' },
            { data: 'suffix' },
            { data: 'contact_number' },
            { data: 'brgy' },
            { data: 'address' },
            { data: 'organization' },
            { data: 'purpose_text' },
            { data: 'remarks' },
            { data: 'referred_by' },
            { data: 'file_date' },
            { data: 'processed_date' },
            { data: 'release_date' },
            { data: 'amount' },
            { data: 'approver.full_name' },
            { data: 'encoder.full_name' },
            { data: 'source' },
            { data: 'created_at' },
        ]
    });

    // Apply the search
    $( '#ajax-table thead').on( 'keyup', ".column_search",function () {
        table
            .column( $(this).parent().index() )
            .search( this.value )
            .draw();
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
                    url : "{{url('social_service/delete')}}",
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

    $(".select-2-ajax").select2({
        width: "100%",
        ajax: {
            url: "{{ url('voter/search') }}?voters_only=1",
            dataType: 'json',
            delay: 250,
            data: function (params) {
            return {
                q: params.term, // search term
                page: params.page
            };
            },
            processResults: function (data, params) {
            params.page = params.page || 1;
            return {
                results: data,
                pagination: {
                more: (params.page * 30) < data.total_count
                }
            };
            },
            cache: true
        },
        placeholder: 'Search from Voters',
        minimumInputLength: 4,
    });

    // Remove Search button click handler, move logic to voter_list change
    $("#voter_list").on('change', function(e){
        let voter_id = $(this).val();
        if(voter_id) {
            $('.voter-info').show();
            $.ajax("{{ url('voter/get') }}/" + voter_id, {
                type: 'GET',
                success: function (data, status, xhr) {
                    $('.voter-info #full_name').html(data.full_name);
                    $('.voter-info #birth_date').html(data.birth_date);
                    $('.voter-info #address').html(data.address);
                    $('.voter-info #precinct').html(data.precinct);
                    $('.voter-info #contact_number').html(data.contact_number);
                    $(".voter-info #gender").html(data.gender);
                    $(".voter-info #brgy").html(data.brgy);
                },
                error: function (jqXhr, textStatus, errorMessage) {
                    alert('Voter not found');
                }
            });
        } else {
            $('.voter-info').hide();
        }
    });


    $('.btn-add-voter-to-ss').on('click', function(e){
        let voter_id = parseInt($("#voter_list").find('option:selected').val());
        if(voter_id > 0) {
            $(location).attr('href', "{{url('social_services/create')}}/" + voter_id);
        }
    });

    // QR scan logic for releasing assistance
    let html5QrcodeScanner;
    $('#btn-scan-qr').off('click').on('click', function(e){
        if(html5QrcodeScanner) html5QrcodeScanner.clear();
        html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
        $('#scan_qr_modal').show();
        function onScanSuccess(decodedText, decodedResult) {
            html5QrcodeScanner.clear().then(_ => {
                let id_number = decodedText.split('/').pop();
                fetchIDForReleasing(id_number);
                $('#scan_qr_modal').hide();
            }).catch(error => {
                swal("Unexpected Error. Please try again.", { icon: "error" });
            });
        }
    });

    $('#scan_qr_modal .close').on('click', function(e){
        $('#scan_qr_modal').hide();
    });

    $('#member_assistances_list_modal .close').on('click', function(e){
        $('#member_assistances_list_modal').hide();
    });

    // SCAN / MANUAL INPUT

    $("#btn-scan-qr-alt").on('click', function(e) {
      $('#scan_qr_modal_alt').show();
      $('#scan_id_number').val('');
      $('#scan_id_number').focus();
    });

    $('#submit_scan_id_number').on('click', function(e) {
      var id_number = $('#scan_id_number').val();
      if (id_number.trim() === '') {
        alert('Please enter a valid ID Number Code.');
        return;
      }
      fetchIDForReleasing(id_number);
      $('#scan_qr_modal_alt').hide();
    });

    $('#close-scan-qr-modal-alt').on('click', function() {
      $('#scan_qr_modal_alt').hide();
    });

    function fetchIDForReleasing(id_number) {
        $.ajax({
            url: "{{ url('/social_services/release/scan-id') }}?id=" + id_number,
            type: 'GET',
            success: function (data) {
                if(data && data.status == 1) {
                    let assistances = data.assistances || [];
                    let tableHtml = `
                        <table class="table table-bordered table-sm table-striped" style="white-space: nowrap;">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>Beneficiary</th>
                                    <th>Requestor</th>
                                    <th>Request Type</th>
                                    <th>Purpose</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${
                                    assistances.length > 0
                                    ? assistances.map(a => `
                                        <tr>
                                            <td>

                                            <a class="btn btn-success btn-sm ajax-modal" href="#" data-title="{{ _lang('Show Request Information') }}" data-href="{{url('social_service/show')}}/${a.id}">View</a>

                                            <a class="btn btn-warning btn-sm ml-1 ajax-modal" id="member_release_${a.id}" href="#" data-title="Release Request - Control Number: ${a.control_number}" data-href="{{url('social_service/release')}}/${a.id}">Release</a>

                                            </td>
                                            <td>${a.full_name}</td>
                                            <td>${a.requestor_full_name}</td>
                                            <td>${a.request_type}</td>
                                            <td>${a.purpose_text}</td>
                                            <td>${a.amount}</td>
                                        </tr>
                                    `).join('')
                                    : `<tr><td colspan="5" class="text-center">No assistance found.</td></tr>`
                                }
                            </tbody>
                        </table>
                    `;
                    $("#member_ss_list").html(tableHtml);

                    if(assistances.length > 1) {
                        $("#member_assistances_list_modal").show();
                    } else if(assistances.length == 1) {
                        // open the release amount if the approve assistance is only one.
                        $(`#member_release_${assistances[0]?.id}`).trigger("click");
                    }
                } else {
                    swal(data?.message || "Invalid or unregistered QR code.", { icon: "error" });
                }
            },
            error: function (xhr) {
                let msg = "Invalid or unregistered QR code.";
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                swal(msg, { icon: "error" });
            }
        });
    }

});
</script>
@endsection