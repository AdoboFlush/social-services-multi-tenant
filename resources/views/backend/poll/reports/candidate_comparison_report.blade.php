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
        <label>Candidate 1</label>
        <select id="candidate1-select" class="form-control candidate-search-select2" disabled></select>
    </div>
    <div class="col-md-3">
        <label>Candidate 2</label>
        <select id="candidate2-select" class="form-control candidate-search-select2" disabled></select>
    </div>
</div>
<div id="report-content"></div>
@endsection

@section('js-script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(function() {
    function enableCandidateSelects(enable) {
        $('#candidate1-select, #candidate2-select').prop('disabled', !enable).val(null).trigger('change');
    }
    $('#election-select, #position-select').on('change', function() {
        let electionId = $('#election-select').val();
        let position = $('#position-select').val();
        enableCandidateSelects(false);
        $('#report-content').html('');
        if(electionId && position) {
            enableCandidateSelects(true);
        }
    });
    function getCandidateAjaxConfig(selector) {
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
                        results: data.data.map(function(candidate) {
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
    $('#candidate1-select').select2(getCandidateAjaxConfig('#candidate1-select'));
    $('#candidate2-select').select2(getCandidateAjaxConfig('#candidate2-select'));
    $('#candidate1-select, #candidate2-select').on('change', function() {
        let electionId = $('#election-select').val();
        let position = $('#position-select').val();
        let candidate1 = $('#candidate1-select').val();
        let candidate2 = $('#candidate2-select').val();
        if(electionId && position && candidate1 && candidate2 && candidate1 !== candidate2) {
            $('#report-content').html('<div class="text-center my-4"><span class="spinner-border spinner-border-sm"></span> Loading...</div>');
            let url = "{{ route('poll.reports.candidate_comparison_data', [], false) }}";
            url += `?election_id=${electionId}&position=${encodeURIComponent(position)}&candidate1_id=${candidate1}&candidate2_id=${candidate2}`;
            $.getJSON(url)
                .done(function(data) {
                    renderReport(data);
                })
                .fail(function() {
                    $('#report-content').html('<div class="alert alert-danger">Failed to fetch data. Please try again.</div>');
                });
        } else {
            $('#report-content').html('<div class="alert alert-warning">Please select two different candidates.</div>');
        }
    });
    function renderReport(data) {
        if(!data || !data.candidates) {
            $('#report-content').html('<div class="alert alert-info">No data available.</div>');
            return;
        }
        let c1 = data.candidates[0], c2 = data.candidates[1];
        // Side-by-side visually appealing summary
        let summary = `
        <div class='card card-olive mb-4'>
            <div class='card-header'><b>Candidate Comparison Summary</b></div>
            <div class='card-body'>
                <div class='row align-items-center'>
                    <div class='col-md-5 text-center border-right'>
                        <img src='${c1.image}' class='img-thumbnail mb-2' style='max-width:110px;'><br>
                        <h4 class='mb-1'>${c1.name}</h4>
                        <div class='text-muted mb-2'>${c1.party}${c1.party_list ? ' / ' + c1.party_list : ''}</div>
                        <div class='mb-2'><span class='badge badge-primary p-2'>Rank: <b>${c1.rank ?? '-'}</b></span></div>
                        <div class='mb-2'><span class='badge badge-info p-2'>Votes: <b>${c1.votes.toLocaleString()}</b></span></div>
                        <div class='mb-2'><span class='badge badge-success p-2'>Vote %: <b>${c1.vote_percentage}%</b></span></div>
                    </div>
                    <div class='col-md-2 text-center d-flex flex-column align-items-center justify-content-center'>
                        <div class='display-4 font-weight-bold my-2'>VS</div>
                        <div class='my-2'><span class='badge badge-warning p-2'>Diff: <b>${Math.abs(c1.votes-c2.votes).toLocaleString()}</b></span></div>
                    </div>
                    <div class='col-md-5 text-center border-left'>
                        <img src='${c2.image}' class='img-thumbnail mb-2' style='max-width:110px;'><br>
                        <h4 class='mb-1'>${c2.name}</h4>
                        <div class='text-muted mb-2'>${c2.party}${c2.party_list ? ' / ' + c2.party_list : ''}</div>
                        <div class='mb-2'><span class='badge badge-primary p-2'>Rank: <b>${c2.rank ?? '-'}</b></span></div>
                        <div class='mb-2'><span class='badge badge-info p-2'>Votes: <b>${c2.votes.toLocaleString()}</b></span></div>
                        <div class='mb-2'><span class='badge badge-success p-2'>Vote %: <b>${c2.vote_percentage}%</b></span></div>
                    </div>
                </div>
            </div>
        </div>`;
        // Side-by-side barangay table with dynamic win/lose highlight and difference
        let brgyTable = `<div class='card card-olive mb-4'><div class='card-header'><b>Votes Per Barangay</b></div><div class='card-body'><div class='table-responsive'><table class='table table-bordered table-striped text-center align-middle' style='table-layout:fixed;width:100%'><thead class='thead-light'><tr><th class='text-center' style='width:35%'>${c1.name}</th><th class='text-center' style='width:30%'>Barangay</th><th class='text-center' style='width:35%'>${c2.name}</th></tr></thead><tbody>`;
        data.votes_per_brgy.forEach(function(row) {
            let c1Win = parseInt(row.c1_votes) > parseInt(row.c2_votes);
            let c2Win = parseInt(row.c2_votes) > parseInt(row.c1_votes);
            let c1Class = c1Win ? 'text-success' : (c2Win ? 'text-danger' : '');
            let c2Class = c2Win ? 'text-success' : (c1Win ? 'text-danger' : '');
            let diff = Math.abs(c1Win ? row.c1_votes - row.c2_votes : row.c2_votes - row.c1_votes);
            let diffText = `<small>Diff: ${diff}</small>`;

            brgyTable += `<tr><td class='text-center align-middle'><span class='font-weight-bold ${c1Class}'>${row.c1_votes}</span></td><td class='text-center align-middle'>${row.brgy}<br>${diffText}</td><td class='text-center align-middle'><span class='font-weight-bold ${c2Class}'>${row.c2_votes}</span></td></tr>`;
        });
        brgyTable += '</tbody></table></div></div></div>';
        // Side-by-side cluster table with dynamic win/lose highlight and difference
        let clusterTable = `<div class='card card-olive mb-4'><div class='card-header'><b>Votes Per Cluster</b></div><div class='card-body'><div class='table-responsive'><table class='table table-bordered table-striped text-center align-middle' style='table-layout:fixed;width:100%'><thead class='thead-light'><tr><th class='text-center' style='width:35%'>${c1.name}</th><th class='text-center' style='width:30%'>Cluster #</th><th class='text-center' style='width:35%'>${c2.name}</th></tr></thead><tbody>`;
        data.votes_per_cluster.forEach(function(row) {
            let c1Win = parseInt(row.c1_votes) > parseInt(row.c2_votes);
            let c2Win = parseInt(row.c2_votes) > parseInt(row.c1_votes);
            let c1Class = c1Win ? 'text-success' : (c2Win ? 'text-danger' : '');
            let c2Class = c2Win ? 'text-success' : (c1Win ? 'text-danger' : '');
            let diff = Math.abs(c1Win ? row.c1_votes - row.c2_votes : row.c2_votes - row.c1_votes);
            let diffText = `<small>Diff: ${diff}</small>`;

            clusterTable += `<tr><td class='text-center align-middle'><span class='font-weight-bold ${c1Class}'>${row.c1_votes}</span></td><td class='text-center align-middle'>${row.cluster}<br>${diffText}</td><td class='text-center align-middle'><span class='font-weight-bold ${c2Class}'>${row.c2_votes}</span></td></tr>`;
        });
        clusterTable += '</tbody></table></div></div></div>';
        $('#report-content').html(summary + brgyTable + clusterTable);
    }
});
</script>
@endsection
