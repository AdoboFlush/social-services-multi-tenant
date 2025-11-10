@extends('backend.report.layout')

@section('card-header')
<h3 class="card-title">Social Services Beneficiaries List</h3>
@endsection

@section('tab-content')

@php
$params = request()->get('filter');
$filter_status = isset($params['status']) ? $params['status'] : '';
$filter_source = isset($params['source']) ? $params['source'] : '';
$alert_str = '';
$alert_str .= isset($params['date_from']) ? ' From '. $params['date_from'] : '';
$alert_str .= isset($params['date_to']) ? ' To '. $params['date_to'] : '';
$alert_str .= isset($params['status']) ? ' with status '. $params['status'] : '';
$alert_str .= isset($params['source']) ? ' from source '. $params['source'] : '';
@endphp

<div class="row">

    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label">{{ _lang('Barangay') }}</label>
            <select class="form-control select2" name="filter_brgy" id="filter_brgy" required>
                <option value="">All Barangays</option>
                @foreach($brgys as $brgy)
                <option value="{{$brgy}}">{{$brgy}}</option>
                @endforeach
            </select>
            <span class="err-message">{{ _lang('Barangay is required.') }}</span>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label">{{ _lang('Request Type') }}</label>
            <select class="form-control select2" name="filter_request_type_id" id="filter_request_type_id" required>
                <option value="">All Request types</option>
                @foreach($purposes as $purpose)
                <option value="{{$purpose->id}}" data-name="{{$purpose->name}}">{{$purpose->name}}</option>
                @endforeach
            </select>
            <span class="err-message">{{ _lang('Request type is required') }}</span>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label">{{ _lang('Status') }}</label>
            <select class="form-control select2" name="filter_status" id="filter_status">
                <option value="">All Statuses</option>
                <option value="{{App\SocialServiceAssistance::STATUS_APPROVED}}" {{ $filter_status == App\SocialServiceAssistance::STATUS_APPROVED ? 'selected' : '' }}>{{App\SocialServiceAssistance::STATUS_APPROVED}}</option>
                <option value="{{App\SocialServiceAssistance::STATUS_ON_HOLD}}" {{ $filter_status == App\SocialServiceAssistance::STATUS_ON_HOLD ? 'selected' : '' }}>{{App\SocialServiceAssistance::STATUS_ON_HOLD}}</option>
                <option value="{{App\SocialServiceAssistance::STATUS_REJECTED}}" {{ $filter_status == App\SocialServiceAssistance::STATUS_REJECTED ? 'selected' : '' }}>{{App\SocialServiceAssistance::STATUS_REJECTED}}</option>
                <option value="{{App\SocialServiceAssistance::STATUS_PENDING}}" {{ $filter_status == App\SocialServiceAssistance::STATUS_PENDING ? 'selected' : '' }}>{{App\SocialServiceAssistance::STATUS_PENDING}}</option>
                <option value="{{App\SocialServiceAssistance::STATUS_RELEASED}}" {{ $filter_status == App\SocialServiceAssistance::STATUS_RELEASED ? 'selected' : '' }}>{{App\SocialServiceAssistance::STATUS_RELEASED}}</option>
                <option value="{{App\SocialServiceAssistance::STATUS_FOR_VALIDATION}}" {{ $filter_status == App\SocialServiceAssistance::STATUS_FOR_VALIDATION ? 'selected' : '' }}>{{App\SocialServiceAssistance::STATUS_FOR_VALIDATION}}</option>
                <option value="{{App\SocialServiceAssistance::STATUS_FOR_DELETE}}" {{ $filter_status == App\SocialServiceAssistance::STATUS_FOR_DELETE ? 'selected' : '' }}>{{App\SocialServiceAssistance::STATUS_FOR_DELETE}}</option>
            </select>
            <span class="err-message">{{ _lang('Status is required') }}</span>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label">{{ _lang('Is a Voter?') }}</label>
            <select class="form-control select2" name="filter_is_voter" id="filter_is_voter">
                <option value="">All</option>
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label">{{ _lang('Date Field') }}</label>
            <select class="form-control select2" name="filter_date_field" id="filter_date_field">
                <option value="created_at" data-name="Date Created">Date Created</option>
                <option value="processed_date" data-name="Date Processed">Date Processed</option>
                <option value="file_date" data-name="Date Filed">Date Filed</option>
                <option value="release_date" data-name="Date Released">Date Released</option>
            </select>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label">{{ _lang('Date From') }}</label>
            <input type="text" class="form-control datepicker" name="filter_date_from" id="filter_date_from" value="{{ isset($params['date_from']) ? $params['date_from'] : ''  }}">
            <span class="err-message">{{ _lang('Date From is required') }}</span>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label">{{ _lang('Date To') }}</label>
            <input type="text" class="form-control datepicker" name="filter_date_to" id="filter_date_to" value="{{ isset($params['date_to']) ? $params['date_to'] : ''  }}">
            <span class="err-message">{{ _lang('Date To is required') }}</span>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label">{{ _lang('Source') }}</label>
            <select class="form-control" name="filter_source" id="filter_source">
                <option value="">
                    ALL
                </option>
                <option value="{{App\SocialServiceAssistance::SOURCE_ARJO}}">
                    {{App\SocialServiceAssistance::SOURCE_ARJO}}
                </option>
                <option value="{{App\SocialServiceAssistance::SOURCE_GAB}}">
                    {{App\SocialServiceAssistance::SOURCE_GAB}}
                </option>
            </select>
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
            <button class="btn btn-success form-control" id="btn-export" type="button"><i class="fa fa-table mr-2"></i>Export</button>
            <span class="err-message">{{ _lang('filter_field is required') }}</span>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <i class="fa fa-table mr-2"></i> 
            <span id="alert-message">Beneficiaries {{ !empty($alert_str) ? $alert_str : ' For All Dates and Statuses ' }} </span>
        </div>
        <div class="table-responsive">
            <table id="ajax-table" style="white-space: nowrap;" class="table table-sm table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Control Number</th>
                        <th>Beneficiary Name</th>
                        <th>Requestor Name</th>
                        <th>Barangay</th>
                        <th>Address</th>
                        <th>Request Type</th>
                        <th>Purpose</th>
                        <th>Date Created</th>
                        <th>Date Filed</th>
                        <th>Date Processed</th>
                        <th>Date Released</th>
                        <th>Status</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('js-script')

<script src="{{asset('adminLTE/plugins/chart.js/Chart.min.js')}}"></script>

<script>

$(document).ready(function(){

    $('#btn-search').on('click', function(e){

        let filter_brgy = $('#filter_brgy').find('option:selected').val();
        let filter_request_type_id = $('#filter_request_type_id').find('option:selected').val();
        let filter_request_type_name = $('#filter_request_type_id').find('option:selected').data("name");
        let filter_is_voter = $('#filter_is_voter').find('option:selected').val();

        let filter_date_field = $('#filter_date_field').find('option:selected').val();
        let filter_date_field_name = $('#filter_date_field').find('option:selected').data("name");
        let filter_date_from = $('#filter_date_from').val();
        let filter_date_to = $('#filter_date_to').val();
        let filter_status = $('#filter_status').find('option:selected').val();
        let filter_source = $('#filter_source').find('option:selected').val();

        let search_url_arr = [];
        let alert_message = "";
        search_url_arr.push("filter[brgy]=" + filter_brgy);
        search_url_arr.push("filter[request_type_id]=" + filter_request_type_id);
        
        if(filter_is_voter.length > 0){
            search_url_arr.push("filter[is_voter]=" + filter_is_voter);
        }
        if(filter_brgy.length > 0){
            alert_message += " from Barangay '" + filter_brgy + "'";
        }
        if(filter_request_type_id.length > 0){
            alert_message += " with Request Type '" + filter_request_type_name + "'";
        }
        if(filter_date_field.length > 0){
            search_url_arr.push("filter[date_field]=" + filter_date_field);
        }
        if(filter_date_from.length > 0){
            search_url_arr.push("filter[date_from]=" + filter_date_from);
            alert_message += " from " + filter_date_field_name + " " + filter_date_from;
        }
        if(filter_date_to.length > 0){
            search_url_arr.push("filter[date_to]=" + filter_date_to);
            alert_message += " to " + filter_date_field_name + " " + filter_date_to;
        }
        
        search_url_arr.push("filter[status]=" + filter_status);
        if(filter_status.length > 0){
            alert_message += " with Status " + filter_status;
        }

        search_url_arr.push("filter[source]=" + filter_source);
        if(filter_status.length > 0){
            alert_message += " from Source " + filter_source;
        }

        let voter_str = "All";
        if(filter_is_voter.length > 0){
            if(filter_is_voter == "1"){
                voter_str = "All Voter";
            }else{
                voter_str = "All Non-Voter";
            }
        }

        $("#alert-message").html(voter_str + " Beneficiaries " + alert_message);
        $('#ajax-table').DataTable().ajax.url('?' + search_url_arr.join('&')).load();

    });

    $('#btn-export').on('click', function(e){

        let filter_brgy = $('#filter_brgy').find('option:selected').val();
        let filter_request_type_id = $('#filter_request_type_id').find('option:selected').val();
        let filter_is_voter = $('#filter_is_voter').find('option:selected').val();

        let filter_date_field = $('#filter_date_field').find('option:selected').val();
        let filter_date_from = $('#filter_date_from').val();
        let filter_date_to = $('#filter_date_to').val();
        let filter_status = $('#filter_status').find('option:selected').val();
        let filter_source = $('#filter_source').find('option:selected').val();
        let search_url_arr = [];
        search_url_arr.push("filter[brgy]=" + filter_brgy);
        search_url_arr.push("filter[request_type_id]=" + filter_request_type_id);
        search_url_arr.push("filter[is_voter]=" + filter_is_voter);
        if(filter_date_field.length > 0){
            search_url_arr.push("filter[date_field]=" + filter_date_field);
        }
        if(filter_date_from.length > 0){
            search_url_arr.push("filter[date_from]=" + filter_date_from);
        }
        if(filter_date_to.length > 0){
            search_url_arr.push("filter[date_to]=" + filter_date_to);
        }
        search_url_arr.push("filter[status]=" + filter_status);
        search_url_arr.push("filter[source]=" + filter_source);
        let ajax_url = "{{url("reports/social_services/beneficiaries/export")}}" + '?' + search_url_arr.join('&');
        exportCSV(ajax_url, "social-service-beneficiaries-{{date('Y-m-d H:i:s')}}.csv");
        
    });

    var table = $("#ajax-table").DataTable({
        'searching': false,
        'ordering': false,
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
            'url':'{{url("reports/social_services/beneficiaries")}}',
            'data':{
                '_token' : '{{csrf_token()}}',
            },
            "dataSrc": function ( json ) {
                for ( var i=0, ien=json.data.length ; i<ien ; i++ ) {
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
        'columns': [
            { data: 'control_number' },
            { data: 'full_name' },
            { data: 'requestor_full_name' },
            { data: 'brgy' },
            { data: 'address' },
            { data: 'request_type' },
            { data: 'purpose_text' },
            { data: 'created_at' },
            { data: 'file_date' },
            { data: 'processed_date' },
            { data: 'release_date' },
            { data: 'status' },
            { data: 'amount' },
        ]
    });

});

</script>
@endsection