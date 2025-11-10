@extends('backend.poll.reports.layout')

@section('sub-tab-content')
<div class="row mb-3">
    <div class="col-md-3">
        <label>Election</label>
        <select id="election-select" class="form-control">
            <option value="">Select Election</option>
            @foreach(App\PollElection::get() as $election)
                <option value="{{ $election->id }}">{{ $election->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label>Position</label>
        <select id="position-select" class="form-control">
            <option value="">Select Position</option>
            @foreach(App\PollElectionCandidate::POSITIONS as $position)
                <option value="{{ $position }}">{{ $position }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label>Candidate</label>
        <select id="candidate-select" class="form-control candidate-search-select2" disabled></select>
    </div>
    <div class="col-md-2">
        <label>Mode</label>
        <select id="mode-select" class="form-control">
            <option value="barangay">By Barangay</option>
            <option value="cluster">By Cluster</option>
        </select>
    </div>
    <div class="col-md-1">
        <label>---</label>
        <button id="export-table" class="btn btn-success form-control" style="display:none;"><i class="fa fa-download mr-1"></i>Export</button>
    </div>
</div>
<div id="report-content"></div>
@endsection

@section('js-script')
<script>
$(function() {
    let $exportBtn = $('#export-table');
    let lastTableData = null;

    function enableCandidateSelect(enable) {
        $('#candidate-select').prop('disabled', !enable).val(null).trigger('change');
    }

    $('#election-select, #position-select').on('change', function() {
        let electionId = $('#election-select').val();
        let position = $('#position-select').val();
        enableCandidateSelect(false);
        $('#report-content').html('');
        if(electionId && position) {
            enableCandidateSelect(true);
        }
    });

    function getCandidateAjaxConfig() {
        return {
            width: '100%',
            placeholder: 'Search Candidate',
            allowClear: true,
            ajax: {
                url: "{{ route('poll.candidates.search', [], false) }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page || 1,
                        election_id: $('#election-select').val(),
                        position: $('#position-select').val()
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: (data.data || []).map(function(candidate) {
                            return {
                                id: candidate.id,
                                text: candidate.name,
                                image: candidate.image ? '/uploads/' + candidate.image : '/images/avatar-classic.png'
                            };
                        }),
                        pagination: { more: false }
                    };
                },
                cache: true
            },
            templateResult: function(candidate) {
                if (!candidate.id) return candidate.text;
                return $('<span><img src="'+candidate.image+'" style="width:30px;height:30px;" class="rounded-circle mr-2" />'+candidate.text+'</span>');
            },
            templateSelection: function(candidate) {
                if (!candidate.id) return candidate.text;
                return candidate.text;
            },
            minimumInputLength: 2
        };
    }
    $('#candidate-select').select2(getCandidateAjaxConfig());

    $('#mode-select').on('change', function() {
        $('#report-content').html('');
        $exportBtn.hide();
    });

    $('#election-select, #position-select, #candidate-select, #mode-select').on('change', function() {
        let electionId = $('#election-select').val();
        let position = $('#position-select').val();
        let candidateId = $('#candidate-select').val();
        let mode = $('#mode-select').val();
        if(electionId && position && candidateId && mode) {
            $('#report-content').html('<div class="text-center my-4"><span class="spinner-border spinner-border-sm"></span> Loading...</div>');
            $exportBtn.hide();
            let url = mode === 'barangay' ? "{{ route('poll.reports.candidate_ranking_by_brgy', [], false) }}" : "{{ route('poll.reports.candidate_ranking_by_cluster', [], false) }}";
            url += `?election_id=${electionId}&position=${encodeURIComponent(position)}&candidate_id=${candidateId}`;
            $.getJSON(url, function(data) {
                renderTableReport(data, mode);
            }).fail(function() {
                $('#report-content').html('<div class="alert alert-danger">Failed to load data.</div>');
            });
        } else {
            $('#report-content').html('<div class="alert alert-warning">Please select all filters.</div>');
            $exportBtn.hide();
        }
    });

    $exportBtn.on('click', function() {
        if(!lastTableData) return;
        let csv = '';
        let headers = lastTableData.headers;
        let rows = lastTableData.rows;
        csv += '\uFEFF';
        csv += headers.map(x => '"'+String(x).replace(/"/g,'""')+'"').join(',') + '\n';
        rows.forEach(function(row) {
            csv += row.map(x => '"'+String(x).replace(/"/g,'""')+'"').join(',') + '\n';
        });
        let blob = new Blob([csv], {type: 'text/csv'});
        let url = window.URL.createObjectURL(blob);
        let a = document.createElement('a');
        a.href = url;
        a.download = 'candidate_ranking_report_' + (new Date().toISOString().slice(0,10)) + '.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    });

    function renderTableReport(data, mode) {
        if(!data || !data.length) {
            $('#report-content').html('<div class="alert alert-info">No data available.</div>');
            lastTableData = null;
            $exportBtn.hide();
            return;
        }
        let headers = [];
        if(mode === 'barangay') {
            headers = ['Barangay', 'Rank', 'Total Votes'];
        } else {
            headers = ['Cluster #', 'Barangay', 'Rank', 'Total Votes'];
        }
        let rows = [];
        data.forEach(function(row) {
            if(mode === 'barangay') {
                rows.push([
                    row.area_name,
                    row.rank,
                    row.total_votes
                ]);
            } else {
                rows.push([
                    row.area_name,
                    row.barangay || '',
                    row.rank,
                    row.total_votes
                ]);
            }
        });
        let table = `<div class='table-responsive' style='overflow-x:auto;max-width:100vw;'>`;
        table += `<table class='table table-bordered table-striped text-center table-sm align-middle mb-0' style='min-width:700px;'>`;
        table += `<thead><tr class='bg-olive'>`;
        headers.forEach(h => table += `<th>${h}</th>`);
        table += `</tr></thead><tbody>`;
        rows.forEach(row => {
            table += '<tr>' + row.map(x => `<td>${x}</td>`).join('') + '</tr>';
        });
        table += '</tbody></table></div>';
        $('#report-content').html(table);
        lastTableData = { headers, rows };
        $exportBtn.show();
    }
});
</script>
@endsection
