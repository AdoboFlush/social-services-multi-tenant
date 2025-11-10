<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">{{ _lang('Position') }}</label>
                <select class="form-control filter_candidate" name="position" id="position">
                    <option value="">All</option>
                    @foreach(\App\PollElectionCandidate::POSITIONS as $position)
                    <option value="{{ $position }}">{{ $position }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">{{ _lang('Local Party') }}</label>
                <select class="form-control filter_candidate" name="party" id="party">
                    <option value="">All</option>
                    @foreach(\App\PollElectionCandidate::PARTIES as $party)
                    <option value="{{ $party }}">{{ $party }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">{{ _lang('National Party') }}</label>
                <select class="form-control filter_candidate" name="national_party" id="national_party">
                    <option value="">All</option>
                    @foreach(\App\PollElectionCandidate::NATIONAL_PARTIES as $national_party)
                        <option value="{{ $national_party }}">
                            {{ $national_party }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-1">
            <div class="form-group">
                <label class="control-label">--</label>
                <button class="btn btn-secondary btn-sm form-control" id="btn-reset" type="button"><i class="fa fa-undo mr-2"></i>Refresh</button>
            </div>
        </div>

        <div class="col-md-1">
            <div class="form-group">
                <label class="control-label">&nbsp;</label>
                <button class="btn btn-success btn-sm form-control" id="btn-download-csv" type="button"><i class="fa fa-download mr-2"></i>Export</button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table id="watcher-candidate-ajax-table" class="table table-sm table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th style="min-width:400px;">Details</th>
                            <th>Has Entry</th>
                            @foreach($clustered_precinct_arr as $clustered_precinct_number)
                            <th style="min-width:100px;">Cluster {{$clustered_precinct_number}}</th>
                            @endforeach
                            <th style="min-width:100px;">Total Votes</th>
                            <th style="min-width:100px;"># of Cluster Entries</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>#</th>
                            <th>Details</th>
                            <th>Has Entry</th>
                            @foreach($clustered_precinct_arr as $clustered_precinct_number)
                            <th>Cluster {{$clustered_precinct_number}}</th>
                            @endforeach
                            <th>Total Votes</th>
                            <th># of Cluster Entries</th>
                        </tr>
                    </tfoot>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {

        $('#btn-reset').on('click', function(e) {
            $('.filter_candidate').val("");
            $('#watcher-candidate-ajax-table').DataTable().ajax.url('{{route("poll.election.watcher_candidates.get", [$poll_election->id, $poll_watcher->id], false)}}').load();
        });

        $('#position, #party, #national_party').on('change', function() {
            let position = $('#position').val();
            let party = $('#party').val();
            let national_party = $('#national_party').val();
            let search_data_arr = [];

            if (position) {
                search_data_arr.push({ key: "position", value: position });
            }
            if (party) {
                search_data_arr.push({ key: "party", value: party });
            }
            if (national_party) {
                search_data_arr.push({ key: "national_party", value: national_party });
            }
            let search_query_params = search_data_arr.map((data) => "filter[" + data.key + "]=" + data.value).join("&");
            if (search_query_params) {
                $('#watcher-candidate-ajax-table').DataTable().ajax.url('{{route("poll.election.watcher_candidates.get", [$poll_election->id, $poll_watcher->id], false)}}?' + search_query_params).load();
            } else {
                $('#watcher-candidate-ajax-table').DataTable().ajax.url('{{route("poll.election.watcher_candidates.get", [$poll_election->id, $poll_watcher->id], false)}}').load();
            }
        });

        $('#btn-download-csv').on('click', function(e) {
            let position = $('#position').val();
            let party = $('#party').val();
            let national_party = $('#national_party').val();
            let params = [];
            if (position) params.push('filter[position]=' + encodeURIComponent(position));
            if (party) params.push('filter[party]=' + encodeURIComponent(party));
            if (national_party) params.push('filter[national_party]=' + encodeURIComponent(national_party));
            let url = "{{ route('poll.election.watcher_candidates.export_csv', [$poll_election->id, $poll_watcher->id], false) }}";
            if (params.length > 0) {
                url += '?' + params.join('&');
            }
            window.location.href = url;
        });

        $("#watcher-candidate-ajax-table").DataTable({
            'responsive': true, // Enable responsive behavior
            'autoWidth': false, // Disable automatic column width calculation
            'scrollX': true, // Enable horizontal scrolling
            'fixedColumns': {
                leftColumns: 1 // Fix the "Details" column
            },
            'lengthChange': false,
            'searching': false, // Disable search functionality
            'ordering': false, // Disable ordering functionality
            'buttons': ["pageLength"],
            'lengthMenu': [
                [10, 25, 50],
                ['10 rows', '25 rows', '50 rows']
            ],
            'processing': true,
            'serverSide': true,
            'serverMethod': 'get',
            "rowCallback": function(row, data, index) {
                if (data.status) {
                    $('td', row).css({
                        'background-color': '#28a745',
                        'color': "white"
                    });
                }
            },
            'ajax': {
                'url': '{{route("poll.election.watcher_candidates.get", [$poll_election->id, $poll_watcher->id], false)}}',
            },
            'columns': [{
                    data: 'id',
                    visible: false,
                    responsivePriority: 5,
                    render: function(data, type, row) {
                        return data;
                    }
                },
                {
                    data: null,
                    responsivePriority: 1,
                    render: function(data, type, row) {
                        return `
                            <div style="display: flex; align-items: center;">
                                <img src="${row.image}" style="width:40px;height:40px;margin-right:10px;border-radius:20%;"/>
                                <div>
                                    <strong>${row.name}</strong><br>
                                    <small>${row.position} ${row.party ? ' - ' + row.party : ''} ${row.national_party ? ' - ' + row.national_party : ''} ${row.partylist ? ' - ' + row.partylist : ''}</small>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    data: null,
                    visible: false,
                    responsivePriority: 2,
                    render: function(data, type, row) {
                        return row.entry_count_by_watcher > 0 ?
                            `<span class="badge badge-success">YES</span>` :
                            `<span class="badge badge-danger">NO</span>`;
                    }
                },
                @foreach($clustered_precinct_arr as $clustered_precinct_number) {
                    data: null,
                    responsivePriority: 3,
                    render: function(data, type, row) {
                        let cvotes = row.clustered_votes ? JSON.parse(row.clustered_votes) : {};
                        let current_votes = cvotes['{{$clustered_precinct_number}}'] ?? 0;
                        return `<span class="clustered-votes">${current_votes.toLocaleString()}</span>`;
                    }
                },
                @endforeach {
                    data: null,
                    responsivePriority: 3,
                    render: function(data, type, row) {
                        return `<span class="total-votes">${row.total_votes_by_watcher.toLocaleString()}</span>`;
                    }
                },
                {
                    data: null,
                    responsivePriority: 3,
                    render: function(data, type, row) {
                        return `<span class="clustered-entry-count">
                            ${row.clustered_vote_entry_count.toLocaleString()} / {{count($clustered_precinct_arr)}}
                        </span>`;
                    }
                },
            ]
        });
    });
</script>