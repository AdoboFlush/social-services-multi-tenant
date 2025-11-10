@extends('layouts.app')

@php
$filter_source = request()->get('source');
@endphp

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Dashboard</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
@endsection

@section('content')

<div class="container-fluid">

  @if(!in_array(Auth::user()->user_access, ["voter_viewing", "encoder_voter"]))
  <!-- Info boxes -->
  <div class="row">
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-info elevation-1"><i class="fa fa-hourglass-half"></i></span>

        <div class="info-box-content">
          <span class="info-box-text">Pending Assistances</span>
          <span class="info-box-number"> {{ number_format($data['pending_social_service_count']) }}</span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->

    <!-- fix for small devices only -->
    <div class="clearfix hidden-md-up"></div>

    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-thumbs-up"></i></span>

        <div class="info-box-content">
          <span class="info-box-text">Approved Assistances</span>
          <span class="info-box-number"> {{ number_format($data['approved_social_service_count']) }}</span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->

    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-hand-holding-usd"></i></span>

        <div class="info-box-content">
          <span class="info-box-text">Released Assistances</span>
          <span class="info-box-number">{{ number_format($data['released_social_service_count']) }}</span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->

    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-users"></i></span>

        <div class="info-box-content">
          <span class="info-box-text">Total Voters</span>
          <span class="info-box-number">
            {{ number_format($data['voter_count']) }}
          </span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>

  </div>
  <!-- /.row -->

  @endif

  <div class="row">
    <div class="col-md-9">
      @if(!in_array(Auth::user()->user_access, ["voter_viewing", "encoder_voter"]))
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title mr-2">Filter Chart Data : </h3>
              <div class="float-right">
                 <select style="min-width:150px;" id="filter_source">
                  <option value="">ALL</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">{{ _lang('Total amount of assistances released this year ('.Carbon\Carbon::now()->format('Y').')') }}</h3>
              </div>
              <div class="card-body">
                <div class="d-flex">
                  <p class="d-flex flex-column">
                    <span class="text-bold text-lg">PHP
                      {{ number_format($data['current_released_amount']['grand_total'], 2) }}
                    </span>
                    <span>Total Amount</span>
                  </p>
                </div>
                <!-- /.d-flex -->

                <div class="position-relative mb-4">
                  <div class="chartjs-size-monitor">
                    <div class="chartjs-size-monitor-expand">
                      <div class=""></div>
                    </div>
                    <div class="chartjs-size-monitor-shrink">
                      <div class=""></div>
                    </div>
                  </div>
                  <canvas id="revenue-chart" height="200" width="764"
                    style="display: block; width: 764px; height: 200px;"
                    class="chartjs-render-monitor"></canvas>
                </div>

                <div class="d-flex flex-row justify-content-end">
                  <span class="mr-2">
                    <i class="fas fa-square text-primary"></i> This Year
                  </span>
                  <span class="mr-2">
                    <i class="fas fa-square text-secondary"></i> Last Year
                  </span>
                </div>
              </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">{{ _lang('Assistance Request Overview ('.Carbon\Carbon::now()->format('Y').')') }}</h3>
              </div>
              <div class="card-body">
                  <canvas id="assistance-request-chart" style="height: 318px; max-width: 100%;"></canvas>
              </div>
            </div>
        </div>
      </div>
      @endif

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Your Recent Activity</h3>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="ajax-table" class="table table-sm table-bordered table-striped">
              <thead>
                <tr>
                  <th>Log Name</th>
                  <th>Description</th>
                  <th>Date Created</th>
                </tr>
              </thead>
              <tfoot>
              </tfoot>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-3">

      @if(Auth::user()->user_access !== "encoder_voter")
      <div class="card">
        <div class="card-header">
          <h3 class="card-title mr-2">Quick Actions : </h3>
          <div class="float-right">
            <div class="btn-group">
              <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-qrcode"></i> Scan ID QR
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
      </div>
      @endif

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Announcements</h3>
        </div>
        <div class="card-body">
          {!! $data['welcome_message'] !!}
        </div>
      </div>

      @if(Auth::user()->user_access !== "encoder_voter")

      <!-- Demographics Toggle -->
      <div class="card mb-2">
        <div class="card-header">
          <h3 class="card-title">Show Demographics by:</h3>
          <div class="float-right">
            <select id="demographics-toggle" class="form-control form-control-sm">
              <option value="brgy" selected>Barangay</option>
              <option value="sectoral">Sectoral</option>
              <option value="civil_status">Civil Status</option>
              <option value="religion">Religion</option>
              <option value="affiliation">Affiliation</option>
              <option value="position">Position</option>
            </select>
          </div>
        </div>
      </div>

      <div id="demographics-affiliation">
        <div class="card card-widget widget-user-2">
          <div class="pt-2 pb-1 pl-3 bg-olive">
            <h5 class="">Affiliation</h5>
          </div>
          <div class="card-footer p-0 dashboard-demographics ">
            <ul class="nav flex-column">
              @foreach($data['affiliation'] as $dt)
              <li class="nav-item">
                <a href="{{url('voters')}}" target="_blank" class="nav-link">
                  {{ !empty($dt->affiliation) ? $dt->affiliation : 'Untagged/Not-Applicable'}} <span class="float-right badge  {{ !empty($dt->affiliation) ? 'bg-primary' : 'bg-danger'}}">{{$dt->count}}</span>
                </a>
              </li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>

      <div id="demographics-brgy">
        <div class="card card-widget widget-user-2">
          <div class="pt-2 pb-1 pl-3 bg-olive">
            <h5 class="">Barangay</h5>
          </div>
          <div class="card-footer p-0 dashboard-demographics ">
            <ul class="nav flex-column">
              @foreach($data['brgy'] as $dt)
              <li class="nav-item">
                <a href="{{url('voters')}}?a_query[brgy]={{$dt->brgy}}" target="_blank" class="nav-link">
                  {{ !empty($dt->brgy) ? $dt->brgy : 'Untagged/Not-Applicable'}} <span class="float-right badge  {{ !empty($dt->brgy) ? 'bg-primary' : 'bg-danger'}}">{{$dt->count}}</span>
                </a>
              </li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>

      <div id="demographics-sectoral">
        <div class="card card-widget widget-user-2">
          <div class="pt-2 pb-1 pl-3 bg-olive">
            <h5 class="">Sectoral</h5>
          </div>
          <div class="card-footer p-0 dashboard-demographics">
            <ul class="nav flex-column">
              @foreach($data['sectoral'] as $dt)
              <li class="nav-item">
                <a href="{{url('voters')}}?a_query[sectoral]={{$dt->sectoral}}" target="_blank" class="nav-link">
                  {{ !empty($dt->sectoral) ? $dt->sectoral : 'Untagged/Not-Applicable'}} <span class="float-right badge {{ !empty($dt->sectoral) ? 'bg-primary' : 'bg-danger'}}">{{$dt->count}}</span>
                </a>
              </li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>

      <div id="demographics-civil_status">
        <div class="card card-widget widget-user-2">
          <div class="pt-2 pb-1 pl-3 bg-olive">
            <h5 class="">Civil Status</h5>
          </div>
          <div class="card-footer p-0 dashboard-demographics">
            <ul class="nav flex-column">
              @foreach($data['civil_status'] as $dt)
              <li class="nav-item">
                <a href="{{url('voters')}}?a_query[civil_status]={{$dt->civil_status}}" target="_blank" class="nav-link">
                  {{ !empty($dt->civil_status) ? $dt->civil_status : 'Untagged/Not-Applicable'}} <span class="float-right badge {{ !empty($dt->civil_status) ? 'bg-primary' : 'bg-danger'}}">{{$dt->count}}</span>
                </a>
              </li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>

      <div id="demographics-religion">
        <div class="card card-widget widget-user-2">
          <div class="pt-2 pb-1 pl-3 bg-olive dashboard-demographics">
            <h5 class="">Religion</h5>
          </div>
          <div class="card-footer p-0">
            <ul class="nav flex-column">
              @foreach($data['religion'] as $dt)
              <li class="nav-item">
                <a href="{{url('voters')}}?a_query[religion]={{$dt->religion}}" target="_blank" class="nav-link">
                  {{ !empty($dt->religion) ? $dt->religion : 'Untagged/Not-Applicable'}} <span class="float-right badge {{ !empty($dt->religion) ? 'bg-primary' : 'bg-danger'}}">{{$dt->count}}</span>
                </a>
              </li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>

      <div id="demographics-position">
        <div class="card card-widget widget-user-2">
          <div class="pt-2 pb-1 pl-3 bg-olive dashboard-demographics">
            <h5 class="">Position</h5>
          </div>
          <div class="card-footer p-0">
            <ul class="nav flex-column">
              @foreach($data['position'] as $dt)
              <li class="nav-item">
                <a href="{{url('voters')}}?a_query[position]={{$dt->position}}" target="_blank" class="nav-link">
                  {{ !empty($dt->position) ? $dt->position : 'Untagged/Not-Applicable'}} <span class="float-right badge {{ !empty($dt->position) ? 'bg-primary' : 'bg-danger'}}">{{$dt->count}}</span>
                </a>
              </li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>

      @endif

    </div>

  </div>

</div>

@endsection

@section('content-modal')

<div id="scan_qr_modal" class="modal animated bounceInDown" tabindex="-1" role="dialog">
  <div class="modal-dialog" style="max-width:438px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Scan QR Code Via Camera </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close-scan-qr-modal">
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

@endsection

@section('js-script')
<script src="{{ asset('adminLTE/plugins/chart.js/Chart.min.js') }}"></script>
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
  $(function() {

    $("#filter_source").on('change', function(e) {
      let source = $(this).val();
      let url = "{{ url('admin/dashboard') }}?source=" + source;
      window.location.href = url;
    });

    // SCAN QR VIA CAMERA

    $('#btn-scan-qr').on('click', function(e) {
      var html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader", {
          fps: 10,  
          qrbox: 250
        });
      html5QrcodeScanner.render(onScanSuccess);
      $('#scan_qr_modal').show();

      function onScanSuccess(decodedText, decodedResult) {
        html5QrcodeScanner.clear().then(_ => {
          $(location).attr("href", "/id_system/requests/scan/" + decodedText);
          $('#scan_qr_modal').hide();
        }).catch(error => {
          console.log('Unexpected Error. Please try again.');
        });
      }
    });

    $('#close-scan-qr-modal').on('click', function() {
      $('#scan_qr_modal').hide();
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
      let url = "/id_system/requests/scan/" + id_number;
      $(location).attr("href", url);
      $('#scan_qr_modal_alt').hide();
    });

    $('#close-scan-qr-modal-alt').on('click', function() {
      $('#scan_qr_modal_alt').hide();
    });

    var table = $("#ajax-table").DataTable({
      'orderCellsTop': true,
      'fixedHeader': true,
      'responsive': true,
      "lengthChange": false,
      "autoWidth": false,
      'buttons': ["copy", "csv", "excel", "pdf", "print", "pageLength"],
      'lengthMenu': [
        [10, 25, 50],
        ['10 rows', '25 rows', '50 rows']
      ],
      'processing': true,
      'serverSide': true,
      'searching': false,
      'ordering': false,
      'serverMethod': 'post',
      'initComplete': function(settings, json) {
        this.api()
          .columns()
          .every(function() {
            var that = this;
            $('input.table-filter', this.footer()).on('keyup change clear', function() {
              if (that.search() !== this.value) {
                that.search(this.value).draw();
              }
            });
          });
      },
      'ajax': {
        'url': '{{url("activity")}}',
        'data': {
          '_token': '{{csrf_token()}}'
        },
        "dataSrc": function(json) {
          for (var i = 0, ien = json.data.length; i < ien; i++) {}
          return json.data;
        }
      },
      'columns': [{
          data: 'log_name'
        },
        {
          data: 'description'
        },
        {
          data: 'created_at'
        },
      ],
    });

      var ticksStyle = {
          fontColor: '#495057',
          fontStyle: 'bold'
      };
      var mode = 'index';
      var intersect = true;
      var revenueChart = new Chart($('#revenue-chart'), {
        data: {
            labels: [
                @foreach ($data['current_released_amount']['months'] as $month)
                    '{{ $month }}',
                @endforeach
            ],
            datasets: [{
                    type: 'line',
                    data: [
                        @foreach ($data['current_released_amount']['total_amount'] as $total_amount)
                            {{ $total_amount }},
                        @endforeach
                    ],
                    backgroundColor: 'transparent',
                    borderColor: '#007bff',
                    pointBorderColor: '#007bff',
                    pointBackgroundColor: '#007bff',
                    fill: false
                },
                {
                    type: 'line',
                    data: [
                        @foreach ($data['last_released_amount']['total_amount'] as $total_amount)
                            {{ $total_amount }},
                        @endforeach
                    ],
                    backgroundColor: 'transparent',
                    borderColor: '#6c757d',
                    pointBorderColor: '#6c757d',
                    pointBackgroundColor: '#6c757d',
                    fill: false
                },
            ]
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                mode: mode,
                intersect: intersect
            },
            hover: {
                mode: mode,
                intersect: intersect
            },
            legend: {
                display: false
            },
            scales: {
                yAxes: [{
                    gridLines: {
                        display: true,
                        lineWidth: '4px',
                        color: 'rgba(0, 0, 0, .2)',
                        zeroLineColor: 'transparent'
                    },
                    ticks: $.extend({
                        beginAtZero: true,
                        suggestedMax: 200
                    }, ticksStyle)
                }],
                xAxes: [{
                    display: true,
                    gridLines: {
                        display: false
                    },
                    ticks: ticksStyle
                }]
            }
        }
      });

      var assistanceChartData = {
            labels: [
                @foreach ($data['request_type_data']['request_types'] as $request_type)
                    '{{ $request_type }}',
                @endforeach
            ],
            datasets: [
                {
                    label: 'Total Released Assistances',
                    backgroundColor: 'rgba(60,141,188,0.9)',
                    borderColor: 'rgba(60,141,188,0.8)',
                    data: [
                        @foreach ($data['request_type_data']['count'] as $count)
                            {{ $count }},
                        @endforeach
                    ],
                },
            ]
        };

        var barChartCanvas = $('#assistance-request-chart').get(0).getContext('2d');
        barChartInstance = new Chart(barChartCanvas, {
            type: 'bar',
            data: assistanceChartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                datasetFill: false,
                animation: false // Disable animation
            }
        });

        $('#demographics-toggle').on('change', function() {
          var val = $(this).val();
          $('#demographics-brgy').hide();
          $('#demographics-sectoral').hide();
          $('#demographics-civil_status').hide();
          $('#demographics-religion').hide();
          $('#demographics-position').hide();
          $('#demographics-affiliation').hide();
          $('#demographics-' + val).show();
        });
        // Default: show only brgy
        $('#demographics-brgy').show();
        $('#demographics-sectoral').hide();
        $('#demographics-civil_status').hide();
        $('#demographics-religion').hide();
        $('#demographics-affiliation').hide();
        $('#demographics-position').hide();

  });
</script>

@endsection