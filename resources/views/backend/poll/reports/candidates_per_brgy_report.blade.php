@extends('backend.poll.reports.layout')

@section('sub-tab-content')
<div class="row mb-3">
    <div class="col-md-4">
        <label>Election</label>
        <select id="election-select" class="form-control">
            <option value="">Select Election</option>
            @foreach(App\PollElection::get() as $election)
                <option value="{{ $election->id }}">{{ $election->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label>Position</label>
        <select id="position-select" class="form-control">
            <option value="">Select Position</option>
            @foreach(App\PollElectionCandidate::POSITIONS as $position)
                <option value="{{ $position }}">{{ $position }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-1">
        <label>---</label>
        <select id="report-type-select" class="form-control mr-2">
            <option value="graph">Graph</option>
            <option value="table">Table</option>
        </select>
    </div>
    <div class="col-md-1">
        <label>---</label>
        <button id="generate-report" class="btn btn-primary form-control"><i class="fa fa-chart-bar mr-2"></i>Generate</button>
    </div>
    <div class="col-md-1">
        <label>---</label>
        <button id="export-table" class="btn btn-success ml-2 form-control" style="display:none;"><i class="fa fa-download mr-1"></i>Export</button>
    </div>
</div>
<div id="report-content"></div>
@endsection

@section('js-script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<script>
$(function() {
    let $generateBtn = $('#generate-report');
    let $exportBtn = $('#export-table');
    let originalBtnHtml = $generateBtn.html();
    let lastTableData = null;

    $('#report-type-select').on('change', function() {
        $('#report-content').html('');
        if($(this).val() === 'table') {
            $exportBtn.show();
        } else {
            $exportBtn.hide();
        }
    });

    $('#generate-report').on('click', function(e) {
        e.preventDefault();
        let electionId = $('#election-select').val();
        let position = $('#position-select').val();
        let reportType = $('#report-type-select').val();
        if(electionId && position) {
            $generateBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Loading...');
            $.getJSON(`{{ route('poll.reports.candidates_per_brgy_data', [], false) }}?election_id=${electionId}&position=${encodeURIComponent(position)}`)
                .done(function(resp) {
                    let data = resp.data || [];
                    let masterCandidates = resp.master_candidates || [];
                    if(reportType === 'table') {
                        renderTableReport(data, masterCandidates);
                        $exportBtn.show();
                    } else {
                        renderReport(data, masterCandidates);
                        $exportBtn.hide();
                    }
                })
                .fail(function() {
                    $('#report-content').html('<div class="alert alert-danger">Failed to fetch data. Please try again.</div>');
                })
                .always(function() {
                    $generateBtn.prop('disabled', false).html(originalBtnHtml);
                });
        } else {
            $('#report-content').html('<div class="alert alert-warning">Please select both Election and Position.</div>');
        }
    });

    $exportBtn.on('click', function() {
        if(!lastTableData) return;
        let csv = '';
        let headers = lastTableData.headers;
        let rows = lastTableData.rows;
        // Prepend BOM for Excel UTF-8 compatibility
        csv += '\uFEFF';
        // Always wrap all values in double quotes and double any internal quotes
        csv += headers.map(x => '"'+String(x).replace(/"/g,'""')+'"').join(',') + '\n';
        rows.forEach(function(row) {
            csv += row.map(x => '"'+String(x).replace(/"/g,'""')+'"').join(',') + '\n';
        });
        let blob = new Blob([csv], {type: 'text/csv'});
        let url = window.URL.createObjectURL(blob);
        let a = document.createElement('a');
        a.href = url;
        a.download = 'candidates_per_barangay_report_' + (new Date().toISOString().slice(0,10)) + '.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    });

    function renderTableReport(data, masterCandidates) {
        if(!data || !data.length) {
            $('#report-content').html('<div class="alert alert-info">No data available.</div>');
            lastTableData = null;
            return;
        }
        let candidateNames = masterCandidates.map(c => c.name);
        let headers = ['Barangay'].concat(candidateNames).concat(['Total']);
        let rows = [];
        data.forEach(function(brgy) {
            let row = [brgy.name];
            let total = 0;
            masterCandidates.forEach(function(c) {
                let found = brgy.candidates.find(x => x.id == c.id);
                let v = found ? found.votes : 0;
                row.push(v);
                total += Number(v);
            });
            row.push(total);
            rows.push(row);
        });
        // Build HTML table
        let candidateColspan = candidateNames.length;
        let table = `<div class='table-responsive' style='overflow-x:auto;max-width:100vw;'>`;
        table += `<table class='table table-bordered table-striped text-center table-sm align-middle mb-0' style='min-width:900px;'>`;
        table += `<thead>`;
        table += `<tr class='bg-olive'>`;
        table += `<th>Barangay</th>`;
        table += `<th colspan='${candidateColspan}'>Candidate Votes</th>`;
        table += `<th>Sub Total</th>`;
        table += `</tr>`;
        table += `<tr><th></th>`;
        candidateNames.forEach(h => table += `<th>${h}</th>`);
        table += `<th></th></tr>`;
        table += `</thead><tbody>`;
        rows.forEach(row => {
            table += '<tr>' + row.map(x => `<td>${x}</td>`).join('') + '</tr>';
        });
        // Add totals row
        let candidateTotals = Array(candidateNames.length).fill(0);
        let grandTotal = 0;
        rows.forEach(row => {
            for(let i=0; i<candidateNames.length; i++) {
                candidateTotals[i] += Number(row[i+1]);
            }
            grandTotal += Number(row[row.length-1]);
        });
        table += `<tr class='font-weight-bold bg-light'>`;
        table += `<td>Total</td>`;
        candidateTotals.forEach(t => table += `<td>${t}</td>`);
        table += `<td>${grandTotal}</td>`;
        table += `</tr>`;
        table += '</tbody></table></div>';
        $('#report-content').html(table);
        lastTableData = { headers, rows };
    }

    function renderReport(data, masterCandidates) {
        let html = '';
        if(!data || !data.length) {
            $('#report-content').html('<div class="alert alert-info">No data available.</div>');
            return;
        }
        for(let i=0; i<data.length; i+=2) {
            html += `<div class='row mb-4'>`;
            for(let j=0; j<2; j++) {
                if(data[i+j]) {
                    let brgy = data[i+j];
                    html += `<div class='col-md-6'>
                        <div class='card card-body p-2'>
                            <h6 class='mb-1 text-center'>Barangay ${brgy.name}</h6>
                            <div style='height:250px;'>
                                <canvas id='brgy-chart-${brgy.id}' style='height:200px !important;'></canvas>
                            </div>
                        </div>
                    </div>`;
                }
            }
            html += `</div>`;
        }
        $('#report-content').html(html);
        // Use color from backend for each candidate
        data.forEach(function(brgy) {
            let ctx = document.getElementById('brgy-chart-'+brgy.id).getContext('2d');
            // Sort candidates by votes descending for graph view only
            let sortedCandidates = brgy.candidates.slice().sort((a, b) => Number(b.votes) - Number(a.votes));
            // Add rank to candidate labels
            let getRankSuffix = idx => {
                if(idx === 0) return '1st';
                if(idx === 1) return '2nd';
                if(idx === 2) return '3rd';
                return (idx+1) + 'th';
            };
            let labels = sortedCandidates.map((c, idx) => {
                let partyInfo = c.party + (c.party_list ? ' / ' + c.party_list : '');
                let rank = getRankSuffix(idx);
                return `[ ${rank} ] ${c.name} (${partyInfo})`;
            });
            let votes = sortedCandidates.map(c => Number(c.votes));
            let colors = sortedCandidates.map(c => c.color);
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Votes',
                        data: votes,
                        backgroundColor: colors,
                        borderColor: colors,
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: { right: 40 } },
                    plugins: {
                        legend: { display: false },
                        title: { display: false },
                        datalabels: {
                            anchor: 'center',
                            align: 'center',
                            color: '#fff',
                            font: { weight: 'bold', size: 13 },
                            offset: 0,
                            clip: false,
                            formatter: function(value) { return value.toLocaleString(); }
                        }
                    },
                    scales: { x: { beginAtZero: true } }
                },
                plugins: [ChartDataLabels]
            });
        });
    }
});
</script>
@endsection
