@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Event: {{ !empty($data['name']) ? $data['name'] : 'N/A' }}</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{url('events')}}">Events</a></li>
                    <li class="breadcrumb-item active">Event Summary</li>
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
                    <h3 class="card-title">Event Summary</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">

                    <div class="card card-olive">
                        <div class="card-header">
                            <h3 class="card-title"> <i class="fa fa-calendar mr-2"></i> Event Information</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <table class="table table-sm table-bordered">
                                        <tr>
                                            <td><strong>{{ _lang('Event Name') }}</strong></td>
                                            <td>{{ !empty($data['name']) ? $data['name'] : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ _lang('Venue') }}</strong></td>
                                            <td>{{ !empty($data['venue']) ? $data['venue'] : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ _lang('Start Date') }}</strong></td>
                                            <td>{{$data['start_at']}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ _lang('Minimum - Maximum Attendees') }}</strong></td>
                                            <td>{{ !empty($data['minimum_attendees']) && !empty($data['minimum_attendees']) ? $data['minimum_attendees'] . ' - ' . $data['maximum_attendees']  : 0 }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-4">
                                    <table class="table table-sm table-bordered">
                                        <tr>
                                            <td><strong>{{ _lang('Description') }}</strong></td>
                                            <td>{{ !empty($data['description']) ? $data['description'] : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ _lang('Hosted By') }}</strong></td>
                                            <td>{{ !empty($data['hosted_by']) ? $data['hosted_by'] : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ _lang('End Date') }}</strong></td>
                                            <td>{{$data['end_at']}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ _lang('Total Number of Attendees') }}</strong></td>
                                            <td>{{ !empty($data['total_attendees']) ? $data['total_attendees'] : 'N/A' }}
                                            </td>
                                        </tr>
                                         <tr>
                                            <td><strong>{{ _lang('Request Type') }}</strong></td>
                                            <td>{{!empty($data['request_type']) ? $data['request_type'] : 'N/A' }}</td>
                                        </tr>
                                        
                                    </table>
                                    <input type="hidden" id="request_type_id" value="{{$data['request_type_id'] }}">
                                </div>
                                <div class="col-md-2"> </div>
                                <div class="col-md-2">
                                    <input type="text" id="claimed-knob" class="knob" value="0" data-width="150" data-height="150" data-fgColor="#3c8dbc" data-readOnly="true" />
                                    <div class="mt-3">
                                        <span>
                                            <i class="fa fa-circle" style="color: #3c8dbc;"></i> Total Released: <span id="total_claimed">0</span>
                                        </span><br>
                                        <span>
                                            <i class="fa fa-circle" style="color: #d2d6de;"></i> Total Beneficiaries: <span id="total_filtered_voters">0</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                     
                    <div class="card card-olive">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-users mr-2"></i> Assistance Beneficiaries</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">{{ _lang('Beneficiary') }}</label>
                                            <input type="text" class="form-control" name="filter_beneficiary" id="filter_beneficiary" placeholder="Search Beneficiary" />
                                            <span class="err-message">{{ _lang('Search Value is required') }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">{{ _lang('Requestor') }}</label>
                                            <input type="text" class="form-control" name="filter_requestor" id="filter_requestor" placeholder="Search Requestor" />
                                            <span class="err-message">{{ _lang('Search Value is required') }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label class="control-label">--</label>
                                            <button class="btn btn-primary form-control" id="btn-search" type="button"><i class="fa fa-search mr-2"></i>Search</button>
                                            <span class="err-message">{{ _lang('filter_field is required') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label class="control-label">--</label>
                                            <button class="btn btn-secondary form-control" id="btn-reset" type="button"><i class="fa fa-undo mr-2"></i>Reset</button>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">--</label></br>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">--</label>
                                            <a class="btn btn-primary form-control" href="#" data-target="#add_new_modal" data-toggle="modal"><i class="fa fa-plus mr-2"></i>Add New From Voters</a>
                                        </div>
                                    </div>
                                    
                                </div>

                                <table id="ss-ajax-table" class="table table-bordered table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Actions</th>
                                            <th>Beneficiary</th>
                                            <th>Requestor</th>
                                            <th>Request Type</th>
                                            <th>Purpose</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>

                            </div>

                            <div class="form-group mt-3">
                                <label class="control-label mr-3">All Checked Items : </label>
                                @can('social_service_status_update')
                                <button class="btn btn-danger btn-change-status" data-status="{{App\SocialServiceAssistance::STATUS_FOR_DELETE}}">For-Delete</button>
                                @endcan
                            </div>
                        </div>
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

<div id="scan_qr_modal" class="modal animated bounceInDown" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="max-width:438px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> Scan QR Code via Camera </h5>
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

<!-- Release Assistance Modal (dynamic content) -->
<div id="release_assistance_modal" class="modal animated bounceInDown" tabindex="-1" role="dialog" style="display:none;">
    <div class="modal-dialog" style="min-width:800px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Release Assistance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close-release-modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="release-assistance-body">
                <!-- Dynamic content will be loaded here -->
                <div class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
            </div>
        </div>
    </div>
</div>

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

@endsection

@section('js-script')

<script src="https://unpkg.com/html5-qrcode"></script>
<script src="{{ asset('adminLTE/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('adminLTE/plugins/jquery-knob/jquery.knob.min.js') }}"></script>

<script>
    $(function() {

        $('#scan_qr_modal .close').on('click', function(e){
            $('#scan_qr_modal').hide();
        });

        $(".knob").knob({
            format: function(value) {
                return value.toFixed(2) + '%';
            },
            min: 0,
            max: 100,
            step: 0.01
        });

        updateKnob();

        function updateKnob() {
            $.get("{{ url('/events/' . $event_id . '/released-percentage') }} ", function(res) {
                $('#claimed-knob').val(res.percentage).trigger('change');
                $('#total_claimed').text(res.released);
                $('#total_filtered_voters').text(res.total);
            });
        }

        $('.btn-change-status').on('click', function(e) {
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
                        url : "{{url('/social_service/update_status_multiple')}}",
                        type: "post",
                        data : {
                            'selected_ids' : data_arr,
                            'status' : selected_status,
                            '_token' : '{{csrf_token()}}'
                        },
                        success: function(data, textStatus, jqXHR)
                        {
                            $("#ss-ajax-table").DataTable().ajax.reload();
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

        $('#btn-search').on('click', function(e) {
            let filter_beneficiary = $('#filter_beneficiary').val();
            let filter_requestor = $('#filter_requestor').val();
            let search_arr = [];
            if (filter_beneficiary.length > 0) {
                search_arr.push("beneficiary_search=" + filter_beneficiary);
            }
            if (filter_requestor.length > 0) {
                search_arr.push("requestor_search=" + filter_requestor);
            }

            $('#ss-ajax-table').DataTable().ajax.url('{{url("events/assistance-beneficaries/".$event_id)}}' + "?" + search_arr.join("&")).load();
        });

        // Reset button handler
        $('#btn-reset').on('click', function(e) {
            $('#filter_beneficiary').val('');
            $('#filter_requestor').val('');
            // Reset DataTable to default URL (no filters)
            $('#ss-ajax-table').DataTable().ajax.url('{{url("events/assistance-beneficaries/".$event_id)}}').load();
        });

        var ss_table = $("#ss-ajax-table").DataTable({
            'searching': false,
            'orderCellsTop': true,
            'fixedHeader': true,
            'responsive': true,
            "lengthChange": false,
            "autoWidth": false,
            'ordering': false, // Disable ordering for all columns
            'lengthMenu': [
                [10, 25, 50],
                ['10 rows', '25 rows', '50 rows']
            ],
            'processing': true,
            'serverSide': true,
            'serverMethod': 'get',
            'buttons': ["copy", "csv", "excel", "pdf", "print", "pageLength"],
            "initComplete": function(settings, json) {
                ss_table.buttons().container().appendTo('#ss-ajax-table_wrapper .col-md-6:eq(0)');
            },
            'ajax': {
                'url': '{{url("events/assistance-beneficaries/".$event_id)}}',
                "dataSrc": function(json) {
                    for (var i = 0, ien = json.data.length; i < ien; i++) {
                        let columnHtml = '<div class="btn-group">';
                        columnHtml += '<input type="checkbox" data-id="' + json.data[i]['id'] + '" class="row-checkbox ml-2 mr-3 mt-2">';
                        columnHtml += '<a class="btn btn-warning btn-sm ajax-modal" href="#" data-title="{{ _lang('Show Request Information') }}" data-href="{{url('social_service/show')}}/'+json.data[i]['id']+'">View</a>';

                        @can('social_service_update')
                        columnHtml += '<a class="btn btn-primary btn-sm ml-1" href="{{url('social_services/edit')}}/'+json.data[i]['id']+'?event_id={{$event_id}}">Edit</a>';
                        @endcan

                        columnHtml += '</div>';
                        json.data[i]['id'] = columnHtml;
                        json.data[i]['brgy'] = json.data[i]['brgy'].length > 0 ? json.data[i]['brgy'] : 'N/A';                 
                        json.data[i]['contact_number'] = json.data[i]['contact_number'].length > 0 ? json.data[i]['contact_number'] : 'N/A';
                        json.data[i]['is_voter'] = json.data[i]['is_voter'] > 0 ? 'Yes' : 'No';
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
                            default:
                                json.data[i]['status'] = '<span class="badge badge-primary">'+json.data[i]['status']+'</span>';
                        }
                    }
                    return json.data;
                }
            },
            'columns': [{
                    data: 'id'
                },
                {
                    data: 'full_name'
                },
                {
                    data: 'requestor_full_name'
                },
                {
                    data: 'request_type'
                },
                {
                    data: 'purpose_text'
                },
                {
                    data: 'amount',
                    render: function(data, type, row) {
                        if (data && !isNaN(data)) {
                            return '₱' + Number(data).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                        return data;
                    }
                }
            ],
            'rowCallback': function(row, data) {
                if (data.status && (data.status.toLowerCase() === 'released' || data.status.toLowerCase().includes('released'))) {
                    $(row).css('background-color', '#28a745');
                    $(row).css('color', '#fff');
                }
            }
        });

        // Close release modal
        $('#close-release-modal').on('click', function() {
            $('#release_assistance_modal').hide();
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
                    // Call scanBeneficiaryId endpoint
                    fetchIDForReleasing(id_number);
                    $('#scan_qr_modal').hide();
                }).catch(error => {
                    swal("Unexpected Error. Please try again.", { icon: "error" });
                });
            }
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
                url: "{{ url('/events/assistance-beneficaries/scan-id/'.$event_id) }}",
                type: 'POST',
                data: {
                    '_token': '{{csrf_token()}}',
                    'id': id_number
                },
                success: function (data) {
                    if(data && data.id) {
                        // Load release form modal with fetched data
                        loadReleaseAssistanceModal(data);
                        updateKnob();
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

        // Function to load the release modal with fetched data
        function loadReleaseAssistanceModal(data) {
            let formHtml = `
            <form id="release-assistance-form" method="post" autocomplete="off">
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="social_service_assistance_id" value="${data.id}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row" style="overflow-y:auto;max-height:500px;">
                            <div class="col-md-12">
                                <div class="font-weight-bold mb-3">Requestor Information</div>
                                <table class="table table-sm table-bordered">
                                    <tr><td><strong>Requestor First Name</strong></td><td>${data.requestor_first_name || 'N/A'}</td></tr>
                                    <tr><td><strong>Requestor Middle Name</strong></td><td>${data.requestor_middle_name || 'N/A'}</td></tr>
                                    <tr><td><strong>Requestor Last Name</strong></td><td>${data.requestor_last_name || 'N/A'}</td></tr>
                                    <tr><td><strong>Requestor Suffix</strong></td><td>${data.requestor_suffix || 'N/A'}</td></tr>
                                    <tr><td><strong>Requestor Relationship To Beneficiary</strong></td><td>${data.requestor_relationship_to_beneficiary || 'N/A'}</td></tr>
                                </table>
                                <div class="font-weight-bold mb-3">Request Information</div>
                                <table class="table table-sm table-bordered">
                                    <tr><td><strong>Control Number</strong></td><td>${data.control_number || 'N/A'}</td></tr>
                                    <tr><td><strong>Request Type</strong></td><td>${data.request_type || 'N/A'}</td></tr>
                                    <tr><td><strong>Purpose</strong></td><td>${data.purpose_text || 'N/A'}</td></tr>
                                    <tr><td><strong>Referred By</strong></td><td>${data.referred_by || 'N/A'}</td></tr>
                                    <tr><td><strong>Received By</strong></td><td>${data.received_by || 'N/A'}</td></tr>
                                    <tr><td><strong>Processed By</strong></td><td>${data.processed_by || 'N/A'}</td></tr>
                                    <tr><td><strong>Approved By</strong></td><td>${data.approver?.full_name || 'N/A'}</td></tr>
                                    <tr><td><strong>File Date</strong></td><td>${data.file_date || 'N/A'}</td></tr>
                                    <tr><td><strong>Processed Date</strong></td><td>${data.processed_date || 'N/A'}</td></tr>
                                    <tr><td><strong>Approved Date</strong></td><td>${data.approved_date || 'N/A'}</td></tr>
                                    <tr><td><strong>Received Date</strong></td><td>${data.received_date || 'N/A'}</td></tr>
                                    <tr><td><strong>Release Date</strong></td><td>${data.release_date || 'N/A'}</td></tr>
                                    <tr><td><strong>Amount</strong></td><td>${data.amount || 'N/A'}</td></tr>
                                    <tr><td><strong>Encoder</strong></td><td>${data.encoder?.full_name || 'N/A'}</td></tr>
                                    <tr><td><strong>Status</strong></td><td>${data.status || 'N/A'}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <div class="font-weight-bold mb-3">Beneficiary Information</div>
                                <table class="table table-sm table-bordered">
                                    <tr><td><strong>First Name</strong></td><td>${data.first_name || 'N/A'}</td></tr>
                                    <tr><td><strong>Middle Name</strong></td><td>${data.middle_name || 'N/A'}</td></tr>
                                    <tr><td><strong>Last Name</strong></td><td>${data.last_name || 'N/A'}</td></tr>
                                    <tr><td><strong>Suffix</strong></td><td>${data.suffix || 'N/A'}</td></tr>
                                    <tr><td><strong>Contact Number</strong></td><td>${data.contact_number || 'N/A'}</td></tr>
                                    <tr><td><strong>Brgy</strong></td><td>${data.brgy || 'N/A'}</td></tr>
                                    <tr><td><strong>Address</strong></td><td>${data.address || 'N/A'}</td></tr>
                                    <tr><td><strong>Organization</strong></td><td>${data.organization || 'N/A'}</td></tr>
                                    <tr><td><strong>Is a Voter?</strong></td><td>${data.is_voter == 1 ? 'Yes' : 'No'}</td></tr>
                                </table>
                                <div class="font-weight-bold mb-3">Remarks</div>
                                <div class="card">
                                    <div class="card-body">
                                        ${data.remarks || 'N/A'}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Amount</label>
                                    <input type="number" class="form-control" name="amount" id="amount" value="${data.amount || 0}" required>
                                    <span class="err-message">Amount is required.</span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Date Received</label>
                                    <input type="text" class="form-control datepicker" name="received_date" id="received_date" value="${data.received_date || (new Date().toISOString().slice(0,10))}" required>
                                    <span class="err-message">Date Processed is required.</span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Received By</label>
                                    <input type="text" class="form-control" name="received_by" id="received_by" value="${data.received_by || ''}" required>
                                    <span class="err-message">Received By is required.</span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Remarks</label>
                                    <textarea class="form-control" name="remarks" id="remarks" rows="3">${data.remarks || ''}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-save">Save</button>
                                    <button type="reset" class="btn btn-danger">Reset</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            `;
            $('#release-assistance-body').html(formHtml);
            $('#release_assistance_modal').show();
            // Initialize datepicker if needed
            if ($.fn.datepicker) {
                $('#received_date').datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                    todayHighlight: true
                });
            }
        }

        // Handle release assistance form submission
        $(document).on('submit', '#release-assistance-form', function(e) {
            e.preventDefault();
            let form = $(this);
            let formData = form.serialize();
            let assistanceId = form.find('input[name="social_service_assistance_id"]').val();
            $.ajax({
                url: `{{ url('/events/assistance-beneficaries/release') }}/${assistanceId}`,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if(data && data.status == 1) {
                        swal("Assistance released successfully!", { icon: "success" });
                        $('#release_assistance_modal').hide();
                        $("#ss-ajax-table").DataTable().ajax.reload();
                        updateKnob();
                    } else {
                        swal(data?.message || "Failed to release assistance.", { icon: "error" });
                    }
                },
                error: function () {
                    swal("Failed to release assistance.", { icon: "error" });
                }
            });
        });


        // Voter Lookup - start

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
                $(location).attr('href', "{{url('social_services/create')}}/" + voter_id + "?event_id={{$event_id}}");
            }
        });

        // Voter Lookup - end

    });
</script>

<style>
    input.knob {
        font-size: 1.2rem !important; 
    }
</style>
@endsection
