@extends('backend.report.layout')

@section('card-header')
<h3 class="card-title">Social Services Overview</h3>
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
    <div class="col-md-2">
        <div class="form-group">
            <label class="control-label">{{ _lang('Date From') }}</label>
            <input type="text" class="form-control datepicker" name="filter_date_from" id="filter_date_from" value="{{ isset($params['date_from']) ? $params['date_from'] : ''  }}">
            <span class="err-message">{{ _lang('Date From is required') }}</span>
        </div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label class="control-label">{{ _lang('Date To') }}</label>
            <input type="text" class="form-control datepicker" name="filter_date_to" id="filter_date_to" value="{{ isset($params['date_to']) ? $params['date_to'] : ''  }}">
            <span class="err-message">{{ _lang('Date To is required') }}</span>
        </div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label class="control-label">{{ _lang('Status') }}</label>
            <select class="form-control select2" name="filter_status" id="filter_status">
                <option value="">All</option>
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

    <div class="col-md-1">
        <div class="form-group">
            <label class="control-label">--</label>
            <button class="btn btn-primary form-control" id="btn-search" type="button"><i class="fa fa-search mr-2"></i>Search</button>
            <span class="err-message">{{ _lang('filter_field is required') }}</span>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <i class="fa fa-table mr-2"></i> Display Results {{ !empty($alert_str) ? $alert_str : ' For All Dates and Statuses ' }}
        </div>
    </div>
</div>
<!-- Info boxes -->
<div class="row">
    <div class="col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-primary elevation-1"><i class="fa fa-money-bill"></i></span>
            <div class="info-box-content">
            <span class="info-box-text">Total Amount Released</span>
            <span class="info-box-number">{{ $report_data['total_amount_released'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-keyboard"></i></span>
            <div class="info-box-content">
            <span class="info-box-text">Total Encoded Assistances</span>
            <span class="info-box-number">{{ $report_data['total_count'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fa fa-users"></i></span>
            <div class="info-box-content">
            <span class="info-box-text">Total of Voters</span>
            <span class="info-box-number">{{ isset($report_data['number_of_non_voters_and_voters']['voter']) ? $report_data['number_of_non_voters_and_voters']['voter'] : 0 }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-danger elevation-1"><i class="fa fa-users"></i></span>
            <div class="info-box-content">
            <span class="info-box-text">Total of Non-Voters</span>
            <span class="info-box-number">{{ isset($report_data['number_of_non_voters_and_voters']['non-voter']) ? $report_data['number_of_non_voters_and_voters']['non-voter'] : 0 }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row">

    <div class="col-md-6">
        <div class="card card-widget widget-user-2">
            <div class="pt-2 pb-1 pl-3 bg-olive">
            <h5>Assistances per Barangay</h5>
            </div>
            <div class="card-footer p-0 dashboard-demographics ">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped">
                        <thead>
                            <tr>
                              <th>Barangay</th>
                              <th>Voters</th>
                              <th>Non-Voters</th>
							  <th>Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($brgys as $brgy => $data)
                            <tr>
                                <td>{{ $brgy }}</td>
                                <td>{{ isset($data['voter']) ? $data['voter'] : 0 }} </td>
                                <td>{{ isset($data['non_voter']) ? $data['non_voter'] : 0 }}</td>
								<td>{{ isset($data['total_amount']) ? $data['total_amount'] : 0 }} </td>
                            </tr>
                            @endforeach
                        </tbody>    
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-widget widget-user-2">
            <div class="pt-2 pb-1 pl-3 bg-olive">
            <h5>Voters and Non Voters</h5>
            </div>
            <div class="card-footer p-0 dashboard-demographics ">
                <canvas id="pieChart" style="min-height: 250px; height: 250px; max-height: 300px; max-width: 100%; margin-bottom:2rem;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-widget widget-user-2">
            <div class="pt-2 pb-1 pl-3 bg-olive">
            <h5>Assistances per Request Type</h5>
            </div>
            <div class="card-footer p-0 dashboard-demographics ">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped">
                        <thead>
                            <tr>
                              <th>Request Type</th>
                              <th>Voters</th>
                              <th>Non-Voters</th>
							  <th>Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($request_types as $request_type => $data)
                            <tr>
                                <td>{{ $request_type }}</td>
                                <td>{{ isset($data['voter']) ? $data['voter'] : 0 }} </td>
                                <td>{{ isset($data['non_voter']) ? $data['non_voter'] : 0 }}</td>
								<td>{{ isset($data['total_amount']) ? $data['total_amount'] : 0 }} </td>
                            </tr>
                            @endforeach
                        </tbody>    
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-widget widget-user-2">
            <div class="pt-2 pb-1 pl-3 bg-olive">
            <h5>Assistances per Status</h5>
            </div>
            <div class="card-footer p-0 dashboard-demographics ">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped">
                        <thead>
                            <tr>
                              <th>Status</th>
                              <th>Voters</th>
                              <th>Non-Voters</th>
							  <th>Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statuses as $status => $data)
                            <tr>
                                <td>{{ $status }}</td>
                                <td>{{ isset($data['voter']) ? $data['voter'] : 0 }} </td>
                                <td>{{ isset($data['non_voter']) ? $data['non_voter'] : 0 }}</td>
								<td>{{ isset($data['total_amount']) ? $data['total_amount'] : 0 }} </td>
                            </tr>
                            @endforeach
                        </tbody>    
                    </table>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js-script')

<script src="{{asset('adminLTE/plugins/chart.js/Chart.min.js')}}"></script>

<script>

$(document).ready(function(){

    $('#btn-search').on('click', function(e){

        let filter_date_from = $('#filter_date_from').val();
        let filter_date_to = $('#filter_date_to').val();
        let filter_status = $('#filter_status').find('option:selected').val();
        let filter_source = $('#filter_source').find('option:selected').val();

        let search_url_arr = [];
        if(filter_date_from.length > 0){
            search_url_arr.push("filter[date_from]=" + filter_date_from);
        }
        
        if(filter_date_to.length > 0){
            search_url_arr.push("filter[date_to]=" + filter_date_to);
        }

        if(filter_status.length > 0){
            search_url_arr.push("filter[status]=" + filter_status);
        }

        if(filter_source.length > 0){
            search_url_arr.push("filter[source]=" + filter_source);
        }
        let new_url =  '{{ url("reports/social_services/overview") }}' + '?' + search_url_arr.join('&');

        window.location.replace(new_url);

    });

    var voterData = {
      labels: [
          'Voter',
          'Non-Voter',
      ],
      datasets: [
        {
          data: [
            {{ isset($report_data['number_of_non_voters_and_voters']['voter']) ? $report_data['number_of_non_voters_and_voters']['voter'] : 0 }}, 
            {{ isset($report_data['number_of_non_voters_and_voters']['non-voter']) ? $report_data['number_of_non_voters_and_voters']['non-voter'] : 0 }}
        ],
          backgroundColor : ['#00a65a', '#f56954', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
        }
      ]
    }

    var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
    var pieData        = voterData;
    var pieOptions     = {
      maintainAspectRatio : false,
      responsive : true,
    }
    new Chart(pieChartCanvas, {
      type: 'pie',
      data: pieData,
      options: pieOptions
    })

});

</script>
@endsection
