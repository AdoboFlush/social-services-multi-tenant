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
    <div class="row">
        <!-- Graphs on the left -->
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title">Assistance Request Overview ({{ now()->year }})</h3>
                            <div class="dropdown ml-auto">
                                <button class="btn btn-sm btn-default dropdown-toggle" type="button" id="chartToggleDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-chart-bar"></i> By Count
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="chartToggleDropdown">
                                    <a class="dropdown-item active" href="#" data-chart-type="count">
                                        <i class="fas fa-chart-bar"></i> By Count
                                    </a>
                                    <a class="dropdown-item" href="#" data-chart-type="amount">
                                        <i class="fas fa-coins"></i> By Amount
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="assistance-request-chart-toggle" style="height: 318px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Total Assistance Released By Month ({{ now()->year }})</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex">
                                <p class="d-flex flex-column">
                                    <span class="text-bold text-lg">₱ {{ $current_year_total_formatted ?? '0.00' }}</span>
                                    <span class="text-muted">Total assistance amount this year</span>
                                </p>
                                <p class="ml-auto d-flex flex-column text-right">
                                    {{-- preserved empty column for layout balance --}}
                                </p>
                            </div>
                            <!-- /.d-flex -->

                            <div class="position-relative mb-4">
                                <canvas id="revenue-chart-landlord" height="200"></canvas>
                            </div>

                            <div class="d-flex flex-row justify-content-end">
                                <span class="mr-2">
                                    <i class="fas fa-square" style="color: #007bff;"></i> {{ now()->year }}
                                </span>
                                <span>
                                    <i class="fas fa-square" style="color: #6c757d;"></i> {{ now()->year - 1 }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tenant cards on the right -->
        <div class="col-md-6">
            <div class="row">
                @foreach($tenants_data as $tenant)
                <div class="col-md-6">
                    <div class="card card-widget widget-user">
                        <div class="widget-user-header bg-olive">
                            <h3 class="widget-user-username">{{$tenant['name']}}</h3>
                            <h5 class="widget-user-desc">{{$tenant['description']}}</h5>
                        </div>
                        <div class="widget-user-image">
                            <img class="img-circle elevation-2" src="{{ asset('images/juan-connect-favicon.png') }}" alt="User Avatar" style="background-color: #e0e0e0; display: block;">
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-4 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header">{{$tenant['voter_count']}}</h5>
                                        <span class="description-text">Registered Voters</span>
                                    </div>
                                </div>
                                <div class="col-sm-4 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header">{{$tenant['released_assistance_count']}}</h5>
                                        <span class="description-text">Assistances Released</span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="description-block">
                                        <h5 class="description-header">{{$tenant['pending_assistance_count']}}</h5>
                                        <span class="description-text">Pending Assistances</span>
                                    </div>
                                    <!-- /.description-block -->
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-12">
                                    <a class="btn btn-block btn-default" target="_blank" href="{{$tenant['domains'][0]}}"><i class="fa fa-link mr-2"></i> Go to {{$tenant['name']}} Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.widget-user -->
                </div>
                @endforeach
            </div>

        </div>
    </div>
</div>

@endsection


@section('js-script')
<script src="{{ asset('adminLTE/plugins/chart.js/Chart.min.js') }}"></script>

<script>
    $(function() {

        // Data structures for both charts
        var assistanceChartDataByCount = {
            labels: [
                @foreach ($assistances_data_arr['request_types'] as $request_type)
                    '{{ $request_type }}',
                @endforeach
            ],
            datasets: [
                {
                    label: 'Total Assistance Requests',
                    backgroundColor: 'rgba(60,141,188,0.9)',
                    borderColor: 'rgba(60,141,188,0.8)',
                    data: [
                        @foreach ($assistances_data_arr['count'] as $count)
                            {{ $count }},
                        @endforeach
                    ],
                },
            ]
        };

        var assistanceChartDataByAmount = {
            labels: [
                @foreach ($assistances_data_arr['request_types'] as $request_type)
                    '{{ $request_type }}',
                @endforeach
            ],
            datasets: [
                {
                    label: 'Total Assistance Amount',
                    backgroundColor: 'rgba(70,190,100,0.9)',
                    borderColor: 'rgba(70,190,100,0.8)',
                    data: [
                        @foreach ($assistances_data_arr['amount'] as $amount)
                            {{ $amount }},
                        @endforeach
                    ],
                },
            ]
        };

        // Initialize chart with "by count" view
        var barChartCanvas = $('#assistance-request-chart-toggle').get(0).getContext('2d');
        var barChartInstance = new Chart(barChartCanvas, {
            type: 'bar',
            data: assistanceChartDataByCount,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                datasetFill: false,
                animation: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        // Handle dropdown toggle
        $('#chartToggleDropdown').on('click', function(e) {
            e.preventDefault();
        });

        $('.dropdown-item').on('click', function(e) {
            e.preventDefault();
            var chartType = $(this).data('chart-type');
            var $button = $('#chartToggleDropdown');
            var $items = $('.dropdown-item');

            // Update button text and icon
            if (chartType === 'count') {
                $button.html('<i class="fas fa-chart-bar"></i> By Count');
                barChartInstance.data = assistanceChartDataByCount;
            } else if (chartType === 'amount') {
                $button.html('<i class="fas fa-coins"></i> By Amount');
                barChartInstance.data = assistanceChartDataByAmount;
            }

            // Update active state
            $items.removeClass('active');
            $(this).addClass('active');

            // Refresh chart
            barChartInstance.update();
        });

        // Line chart for monthly data
        var ticksStyle = {
            fontColor: '#495057',
            fontStyle: 'bold'
        };
        var mode = 'index';
        var intersect = true;

        var revenueChartLandlord = new Chart($('#revenue-chart-landlord'), {
            type: 'line',
            data: {
                labels: [
                    @foreach ($monthly_data['months'] as $month)
                        '{{ $month }}',
                    @endforeach
                ],
                datasets: [{
                        type: 'line',
                        data: [
                            @foreach ($monthly_data['current_year'] as $amount)
                                {{ $amount }},
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
                            @foreach ($monthly_data['last_year'] as $amount)
                                {{ $amount }},
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
    });
</script>

@endsection