@extends('backend.poll.layout')

@section('card-header')
<h3 class="card-title"> 
Poll Overview @if($activeElection) - <a target="_blank" href="{{route('poll.elections.show', $activeElection->id, false)}}">{{$activeElection->name}} ({{ \Carbon\Carbon::parse($activeElection->election_date)->format('F j, Y') }}) 
    </a>
@endif
</h3>
<div class="float-right">
    <span id="last-updated-time" class="ml-2 text-muted mr-2" style="font-size: 0.9rem;">Last updated: {{ now()->format('F j, Y, g:i A') }}</span>
    <button id="refreshData" class="btn btn-primary btn-sm">
        <i class="fa fa-sync"></i> Refresh Data
    </button>
    <button id="toggleAutoRefresh" class="btn btn-secondary btn-sm ml-2">
        <i class="fa fa-play"></i> Auto Refresh
    </button>
</div>
@endsection

@section('tab-content')
<div class="container-fluid">
    @if($activeElection)
    <div id="loader" class="text-center my-3" style="display: none;">
        <i class="fa fa-spinner fa-spin fa-2x"></i>
        <p>Loading...</p>
    </div>
    <div class="row mt-3">
        <!-- Poll Votes Per Barangay -->
        <div class="col-md-12">
            <div class="card card-olive">
                <div class="card-header">
                    <h3 class="card-title">Vote Summary Per Barangay</h3>
                    <div class="float-right">
                        <select id="poll-votes-position-filter" class="form-control form-control-sm d-inline-block" style="width: auto;">
                            @foreach(\App\PollElectionCandidate::POSITIONS as $position)
                            <option value="{{ $position }}">{{ $position }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="chart">
                                <canvas id="barChart" style="min-height: 250px; height: 250px;"></canvas>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center mt-5">
                                <input type="text" class="knob" id="votesKnob" value="0" data-width="120" data-height="120" data-fgColor="#3c8dbc" data-readonly="true">
                                <div class="mt-3">
                                    <span>
                                        <i class="fa fa-circle" style="color: #3c8dbc;"></i> Total Votes: <span id="totalVotes">0</span>
                                    </span><br>
                                    <span>
                                        <i class="fa fa-circle" style="color: #d2d6de;"></i> Registered Voters: <span id="totalRegisteredVoters">0</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="row mt-3" id="leaderboardContainer">
                @foreach($positionRaceData as $position => $candidates)
                <div class="col-md-6">
                    <div class="card card-olive mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Leaderboard for <span style="font-weight:1000">{{ $position }}</span> position</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>Candidate</th>
                                            <th>Votes</th>
                                            <th>Completion %</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($candidates as $index => $candidate)
                                        <tr>
                                            <td>
                                                <strong>
                                                    @if($index === 0) 1st
                                                    @elseif($index === 1) 2nd
                                                    @elseif($index === 2) 3rd
                                                    @else {{ $index + 1 }}th
                                                    @endif
                                                </strong>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $candidate['image'] }}"
                                                        alt="{{ $candidate['name'] }}"
                                                        class="mr-2 rounded-circle"
                                                        style="width: 50px; height: 50px;">
                                                    <div>
                                                        <strong>{{ $candidate['name'] }}</strong><br>
                                                        <small>
                                                            {{ $position }}
                                                            @if(!empty($candidate['party']))
                                                                - {{ $candidate['party'] }}
                                                            @endif
                                                            @if(!empty($candidate['national_party']))
                                                                - {{ $candidate['national_party'] }}
                                                            @endif
                                                            @if(!empty($candidate['party_list']))
                                                                - {{ $candidate['party_list'] }}
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ number_format($candidate['votes']) }}</td>
                                            <td>
                                                <div class="progress" style="height: 20px; position: relative;">
                                                    <div class="progress-bar" role="progressbar"
                                                        style="width: {{ $candidate['completion_percentage'] ?? 0 }}%; position: relative;"
                                                        aria-valuenow="{{ $candidate['completion_percentage'] ?? 0 }}"
                                                        aria-valuemin="0"
                                                        aria-valuemax="100">
                                                    </div>
                                                    <span class="progress-text"
                                                        style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: black; font-weight: bold; white-space: nowrap;">
                                                        {{ number_format($candidate['completion_percentage'] ?? 0) }}%
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No data available.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    @else

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="alert alert-warning">
                No active election found. Please create an election first.
            </div>
        </div>
    </div>

    @endif

    @endsection

    @section('js-script')

    @if($activeElection)
    <script src="{{ asset('adminLTE/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('adminLTE/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
    <script src="{{ asset('adminLTE/plugins/chart.js/Chart.min.js') }}"></script>
    
    <script>
        $(function() {
            let autoRefreshInterval = null;
            const defaultPosition = 'Congressman';
            let barChartInstance = null;

            // Initialize KNOB with percentage display
            $(".knob").knob({
                format: function(value) {
                    return value + '%';
                }
            });

            function formatNumber(num) {
                num = Number(num) || 0;
                return num.toLocaleString();
            }

            function renderCharts(data) {
                // Poll Votes Per Barangay Data
                var pollVotesData = data.pollVotesData;

                if (barChartInstance) {
                    barChartInstance.destroy(); // Destroy the previous chart instance
                }

                if (pollVotesData.length === 0) {
                    // If no data, clear the chart
                    var barChartCanvas = $('#barChart').get(0).getContext('2d');
                    barChartInstance = new Chart(barChartCanvas, {
                        type: 'bar',
                        data: {
                            labels: [],
                            datasets: []
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            datasetFill: false,
                            animation: false // Disable animation
                        }
                    });
                    return;
                }

                // Ensure pollVotesData is correctly mapped to the chart with numeric values
                var barChartData = {
                    labels: pollVotesData.map(data => data.brgy),
                    datasets: [
                        {
                            label: 'Total Votes',
                            backgroundColor: 'rgba(60,141,188,0.9)',
                            borderColor: 'rgba(60,141,188,0.8)',
                            data: pollVotesData.map(data => Number(data.total_votes)) // Convert to number
                        },
                        // {
                        //     label: 'Registered Voters',
                        //     backgroundColor: 'rgba(210, 214, 222, 1)',
                        //     borderColor: 'rgba(210, 214, 222, 1)',
                        //     data: pollVotesData.map(data => Number(data.no_of_registered_voters)) // Convert to number
                        // }
                    ]
                };

                var barChartCanvas = $('#barChart').get(0).getContext('2d');
                barChartInstance = new Chart(barChartCanvas, {
                    type: 'bar',
                    data: barChartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        datasetFill: false,
                        animation: false // Disable animation
                    }
                });

                // Update KNOB and caption
                const totalVotes = Number(data.totalVotes); // Ensure numeric value
                const totalRegisteredVoters = Number(data.totalRegisteredVoters); // Ensure numeric value
                const percentage = (totalVotes > 0 && totalRegisteredVoters > 0) 
                    ? Math.round((totalVotes / totalRegisteredVoters) * 100) 
                    : 0;

                $('#votesKnob').val(percentage).trigger('change');
                $('#totalVotes').text(formatNumber(totalVotes));
                $('#totalRegisteredVoters').text(formatNumber(totalRegisteredVoters));

                // Update Leaderboards
                const positionRaceData = data.positionRaceData;
                const leaderboardContainer = $('#leaderboardContainer');
                leaderboardContainer.empty(); // Clear existing leaderboards

                $.each(positionRaceData, function(position, candidates) {
                    const leaderboardHtml = `
                    <div class="col-md-6">
                        <div class="card card-olive mb-4">
                            <div class="card-header">
                                <h3 class="card-title">Leaderboard for <span style="font-weight:1000">${position}</span> position</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Candidate</th>
                                                <th>Votes</th>
                                                <th>Completion %</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${candidates.length ? candidates.map((candidate, index) => `
                                                <tr>
                                                    <td>
                                                        <strong>
                                                            ${index === 0 ? '1st' : index === 1 ? '2nd' : index === 2 ? '3rd' : `${index + 1}th`}
                                                        </strong>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="${candidate.image}" 
                                                                alt="${candidate.name}" 
                                                                class="mr-2 rounded-circle" 
                                                                style="width: 50px; height: 50px;">
                                                            <div>
                                                                <strong>${candidate.name}</strong><br>
                                                                <small>
                                                                    ${position}
                                                                    ${candidate.party ? ' - ' + candidate.party : ''}
                                                                    ${candidate.national_party ? ' - ' + candidate.national_party : ''}
                                                                    ${candidate.party_list ? ' - ' + candidate.party_list : ''}
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>${formatNumber(candidate.votes)}</td>
                                                    <td>
                                                        <div class="progress" style="height: 20px; position: relative;">
                                                            <div class="progress-bar" role="progressbar" 
                                                                style="width: ${candidate.completion_percentage ?? 0}%; position: relative;" 
                                                                aria-valuenow="${candidate.completion_percentage ?? 0}" 
                                                                aria-valuemin="0" 
                                                                aria-valuemax="100">
                                                            </div>
                                                            <span class="progress-text" 
                                                                style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: black; font-weight: bold; white-space: nowrap;">
                                                                ${formatNumber(candidate.completion_percentage ?? 0)}%
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            `).join('') : `
                                                <tr>
                                                    <td colspan="4" class="text-center">No data available.</td>
                                                </tr>
                                            `}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                    leaderboardContainer.append(leaderboardHtml);
                });
            }

            function refreshData(position = defaultPosition) {
                $('#refreshData').html('<i class="fa fa-spinner fa-spin"></i> Refreshing...').prop('disabled', true);
                $.get('{{ route("poll.overview.refresh", [], false) }}', {
                    position
                }, function(data) {
                    renderCharts(data);

                    // Update last updated time
                    const now = new Date();
                    const formattedTime = now.toLocaleString();
                    $('#last-updated-time').text('Last updated: ' + formattedTime);
                }).always(function() {
                    $('#refreshData').html('<i class="fa fa-sync"></i> Refresh Data').prop('disabled', false);
                });
            }

            // Initial Render
            $('#last-updated-time').text('Last updated: ' + new Date().toLocaleString());
            refreshData();

            // Manual Refresh
            $('#refreshData').on('click', function() {
                const position = $('#poll-votes-position-filter').val();
                refreshData(position);
            });

            // Position Filter Change
            $('#poll-votes-position-filter').on('change', function() {
                const position = $(this).val();
                $('#votesKnob').val(0).trigger('change'); // Reset knob to 0
                refreshData(position);
            });

            // Toggle Auto Refresh
            $('#toggleAutoRefresh').on('click', function() {
                const $button = $(this);
                if (autoRefreshInterval) {
                    clearInterval(autoRefreshInterval);
                    autoRefreshInterval = null;
                    $button.html('<i class="fa fa-play"></i> Auto Refresh').removeClass('btn-success').addClass('btn-secondary');
                } else {
                    autoRefreshInterval = setInterval(() => {
                        const position = $('#poll-votes-position-filter').val(); // Get the selected position
                        refreshData(position);
                    }, 5000);
                    $button.html('<i class="fa fa-pause"></i> Stop Auto Refresh').removeClass('btn-secondary').addClass('btn-success');
                }
            });
        });
    </script>
    @endif
    @endsection