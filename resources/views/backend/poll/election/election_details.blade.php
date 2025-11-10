@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ $poll_election->name }} ({{ \Carbon\Carbon::parse($poll_election->election_date)->format('F j, Y') }})</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('poll.elections.index', [], false)}}">Elections</a></li>
                    <li class="breadcrumb-item active">{{ $poll_election->name }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- /.content-header -->
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Election Details</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card card-olive">
                                <div class="card-header">
                                    <h3 class="card-title">{{ _lang('Election Details') }}</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-sm">
                                        <tbody>
                                            <tr>
                                                <th>{{ _lang('Event Name') }}</th>
                                                <td>{{ $poll_election->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ _lang('Remarks') }}</th>
                                                <td>{{ $poll_election->remark ?? _lang('N/A') }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ _lang('Type') }}</th>
                                                <td>{{ $poll_election->type }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ _lang('Election Date') }}</th>
                                                <td>{{ \Carbon\Carbon::parse($poll_election->election_date)->format('F j, Y g:i A') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card card-olive">
                                <div class="card-header">
                                    <h3 class="card-title">{{ _lang('Poll Leaderboard') }}</h3>
                                    <div class="float-right">
                                        <select id="leaderboard-position-filter" class="form-control form-control-sm d-inline-block" style="width: auto;">
                                            <option value="">{{ _lang('All Positions') }}</option>
                                            @foreach(\App\PollElectionCandidate::POSITIONS as $position)
                                                <option value="{{ $position }}">{{ $position }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-primary btn-sm" id="refresh-leaderboard">
                                            <i class="fa fa-sync"></i> {{ _lang('Refresh') }}
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="leaderboard-loader" class="text-center" style="display: none;">
                                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                                        <p>{{ _lang('Refreshing leaderboard...') }}</p>
                                    </div>
                                    <div id="leaderboard-content" class="row" style="max-height: 400px; overflow-y: auto;">
                                        <p class="text-center w-100">{{ _lang('Loading leaderboard...') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="row">
                        <div class="col-md-12">
                            <div class="card card-olive">
                                <div class="card-header">
                                    <h3 class="card-title">{{ _lang('Watcher Progression Per Barangay') }}</h3>
                                </div>
                                <div class="card-body">
                                    
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-olive">
                                <div class="card-header">
                                    <h3 class="card-title">{{ _lang('Candidates') }}</h3>
                                    <span class="float-right">
                                        <button class="btn btn-primary btn-sm" id="btn-candidate-lookup">Add Candidate</button>
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="control-label">{{ _lang('Position') }}</label>
                                                <select class="form-control select2 filter_candidate" name="position" id="position">
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
                                                <select class="form-control select2 filter_candidate" name="party" id="party">
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
                                                <select class="form-control select2 filter_candidate" name="national_party" id="national_party">
                                                    <option value="">All</option>
                                                    @foreach(\App\PollElectionCandidate::NATIONAL_PARTIES as $party)
                                                    <option value="{{ $party }}">{{ $party }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="control-label">{{ _lang('Party-list') }}</label>
                                                <select class="form-control select2 filter_candidate" name="party_list" id="party_list">
                                                    <option value="">All</option>
                                                    @foreach(\App\PollElectionCandidate::PARTYLISTS as $partylist)
                                                    <option value="{{ $partylist }}">{{ $partylist }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label class="control-label">--</label>
                                                <button class="btn btn-primary btn-sm form-control" id="btn-search" type="button"><i class="fa fa-search mr-2"></i>Search</button>
                                            </div>
                                        </div>

                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label class="control-label">--</label>
                                                <button class="btn btn-secondary btn-sm form-control" id="btn-reset" type="button"><i class="fa fa-undo mr-2"></i>Reset</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="ajax-table" class="table table-sm table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Actions</th>
                                                    <th>Name</th>
                                                    <th>Position</th>
                                                    <th>Local Party</th>
                                                    <th>National Party</th>
                                                    <th>Party-list</th>
                                                    <th>Total Votes</th>
                                                    <th>Completion %</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>Actions</th>
                                                    <th>Name</th>
                                                    <th>Position</th>
                                                    <th>Local Party</th>
                                                    <th>National Party</th>
                                                    <th>Party-list</th>
                                                    <th>Total Votes</th>
                                                    <th>Completion %</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-olive">
                                <div class="card-header">
                                    <h3 class="card-title">{{ _lang('Watchers') }}</h3>
                                    <span class="float-right">
                                        <button class="btn btn-primary btn-sm" id="btn-watcher-lookup">Add Watcher</button>
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="control-label">{{ _lang('Search Field') }}</label>
                                                <select class="form-control select2" name="watcher_search_field" id="watcher_search_field">
                                                    <option value="">Select search field</option>
                                                    <option value="name">Watcher Name</option>
                                                    <option value="brgy">Barangay</option>
                                                    <option value="precinct">Precinct</option>
                                                    <option value="area">Area</option>
                                                    <option value="poll_place">Poll Place</option>
                                                    <option value="clustered_precincts">Clustered Precincts</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="control-label">{{ _lang('Search Value') }}</label>
                                                <input type="text" class="form-control" name="watcher_search_value" id="watcher_search_value" placeholder="Search Value">
                                            </div>
                                        </div>

                                        <div class="col-md-2"></div>

                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label class="control-label">--</label>
                                                <button class="btn btn-primary btn-sm form-control" id="btn-watcher-search" type="button"><i class="fa fa-search mr-2"></i>Search</button>
                                            </div>
                                        </div>

                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label class="control-label">--</label>
                                                <button class="btn btn-secondary btn-sm form-control" id="btn-watcher-reset" type="button"><i class="fa fa-undo mr-2"></i>Reset</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="watcher-table" class="table table-sm table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Actions</th>
                                                    <th>Name</th>
                                                    <th>Barangay</th>
                                                    <th>Precinct</th>
                                                    <th>Area</th>
                                                    <th>Poll Place</th>
                                                    <th>Clustered Precincts</th>
                                                    <th>No of Registered Voters</th>
                                                    <th>No of Entries</th>
                                                    <th>Completion %</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>Actions</th>
                                                    <th>Name</th>
                                                    <th>Barangay</th>
                                                    <th>Precinct</th>
                                                    <th>Area</th>
                                                    <th>Poll Place</th>
                                                    <th>Clustered Precincts</th>
                                                    <th>No of Registered Voters</th>
                                                    <th>No of Entries</th>
                                                    <th>Completion %</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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

<div id="candidate_search_modal" class="modal animated bounceInDown" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="max-width:600px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Election Candidate</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row mb-3">
                        <div class="col-md-12 mb-2">
                            <label class="control-label">{{ _lang('Candidate') }}:</label>
                            <select class="form-control candidate-search-ajax" id="candidate_list">
                                <option value="">Please select a candidate</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="control-label">{{ _lang('Position') }}:</label>
                            <select class="form-control" id="candidate_position">
                                <option value="">Select Position</option>
                                @foreach (\App\PollElectionCandidate::POSITIONS as $position)
                                    <option value="{{ $position }}">{{ $position }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="control-label">{{ _lang('Local Party') }}:</label>
                            <select class="form-control" id="candidate_party">
                                <option value="">{{ _lang('Select Party') }}</option>
                                @foreach (\App\PollElectionCandidate::PARTIES as $party)
                                    <option value="{{ $party }}">{{ $party }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="control-label">{{ _lang('National Party') }}:</label>
                            <select class="form-control" id="candidate_national_party">
                                <option value="">{{ _lang('Select National Party') }}</option>
                                @foreach (\App\PollElectionCandidate::NATIONAL_PARTIES as $national_party)
                                    <option value="{{ $national_party }}">{{ $national_party }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="control-label">{{ _lang('Party-list') }}:</label>
                            <select class="form-control" id="candidate_party_list">
                                <option value="">{{ _lang('Select Party-list') }}</option>
                                @foreach (\App\PollElectionCandidate::PARTYLISTS as $partylist)
                                    <option value="{{ $partylist }}">{{ $partylist }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4 candidate-info" style="overflow-y:auto;max-height:500px;display:none;">
                        <div class="col-md-12">
                            <div class="alert alert-info">Candidate Information</div>
                            <table class="table table-bordered">
                                <tr><td><strong>{{ _lang('Name') }}</strong></td><td id="candidate_name"></td></tr>
                                <tr><td><strong>{{ _lang('Position') }}</strong></td><td id="candidate_position_display"></td></tr>
                                <tr><td><strong>{{ _lang('Party') }}</strong></td><td id="candidate_party_display"></td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-add-candidate-to-election" class="btn btn-success float-right" disabled="true">{{ _lang('Add Candidate') }}</button>
            </div>
        </div>
    </div>
</div>

<div id="watcher_search_modal" class="modal animated bounceInDown" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="max-width:600px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Search Watchers</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="control-label">{{ _lang('Watcher') }}:</label>
                            <select class="form-control watcher-search-ajax" id="watcher_list">
                                <option value="">Please select a watcher</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-add-watcher-to-election" class="btn btn-success float-right" disabled="true">{{ _lang('Add Watcher') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-script')
<script>
    $(document).ready(function() {
        $('.nav-tabs .nav-link').on('click', function(e) {
            let search_type = $(this).data('tag-type');
            $('#ajax-table').DataTable().ajax.url("?search_type=" + search_type).load();
        });

        var table = $("#ajax-table").DataTable({
            'orderCellsTop': true,
            'fixedHeader': true,
            'responsive': true,
            "lengthChange": false,
            "autoWidth": false,
            "searching": false,
            'buttons': ["copy", "csv", "excel", "pdf", "print", "pageLength"],
            'lengthMenu': [
                [10, 25, 50],
                ['10 rows', '25 rows', '50 rows']
            ],
            'processing': true,
            'serverSide': true,
            'serverMethod': 'get',
            'initComplete': function(settings, json) {
                table.buttons().container().appendTo('#ajax-table_wrapper .col-md-6:eq(0)');
                this.api()
                    .columns()
                    .every(function() {
                        var that = this;
                        $('input', this.footer()).on('keyup change clear', function() {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        });
                    });
            },
            'ajax': {
                'url': '{{route("poll.elections.candidates.list", $poll_election->id, false)}}',
                "dataSrc": function(json) {
                    for (var i = 0, ien = json.data.length; i < ien; i++) {
                    }
                    return json.data;
                }
            },
            'columns': [
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return '<input type="checkbox" data-id="' + row.id + '" class="row-checkbox ml-2 mr-3 mt-2"><a class="btn btn-primary btn-sm ml-1 ajax-modal" href="#" data-title="Edit" data-href="{{ url("/poll/elections/candidates/edit/") }}/'+ row.id +'">Edit</a>';
                    }
                },
                {
                    data: 'name',
                    render: function(data, type, row) {
                        let nameHtml = '<a class="ajax-modal" style="color:black;" href="#" data-title="Candidate Details" data-href="{{ url("/poll/elections/candidates/show/") }}/'+ row.id +'">';
                        nameHtml += '<img src="'+ row.image + '" style="width:40px;height:40px;margin-right:5px;"/><strong>' + row.name + '</strong>';
                        return nameHtml + "</a>";
                    }, 
                    orderable: false, 
                    searchable: false 
                },
                { data: 'position', orderable: false, searchable: false },
                { data: 'party', orderable: false, searchable: false },
                { data: 'national_party', orderable: false, searchable: false },
                { data: 'party_list', orderable: false, searchable: false },
                { 
                    data: 'total_votes', 
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return data.toLocaleString();
                    }
                },
                {
                    data: 'completion_percentage',
                    render: function(data, type, row) {
                        return `
                            <div class="progress" style="height: 20px; position: relative;">
                                <div class="progress-bar" role="progressbar" style="width: ${data}%; position: relative;" aria-valuenow="${data}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                                <span class="progress-text" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: black; font-weight: bold; white-space: nowrap;">
                                    ${data}%
                                </span>
                            </div>
                        `;
                    }, 
                    orderable: false, 
                    searchable: false 
                },
            ]
        });

        // Apply the search
        $('#ajax-table thead').on('keyup', ".column_search", function() {
            table
                .column($(this).parent().index())
                .search(this.value)
                .draw();
        });

        $('#btn-search').on('click', function(e) {
            let position = $('#position').val();
            let party = $('#party').val();
            let party_list = $('#party_list').val();
            let national_party = $('#national_party').val();
            let search_data_arr = [];

            if (position) {
                search_data_arr.push({
                    key: "position",
                    value: position
                });
            }
            if (party) {
                search_data_arr.push({
                    key: "party",
                    value: party
                });
            }
            if (party_list) {
                search_data_arr.push({
                    key: "party_list",
                    value: party_list
                });
            }
            if (national_party) {
                search_data_arr.push({
                    key: "national_party",
                    value: national_party
                });
            }

            let search_query_params = search_data_arr.map((data) => "filter[" + data.key + "]=" + data.value).join("&");
            if (search_query_params) {
                $('#ajax-table').DataTable().ajax.url('{{route("poll.elections.candidates.list", $poll_election->id, false)}}?' + search_query_params).load();
            } else {
                $('#ajax-table').DataTable().ajax.url('{{route("poll.elections.candidates.list", $poll_election->id, false)}}').load();
            }
        });

        $('#btn-reset').on('click', function(e) {
            // Reset filter fields
            $('#position').val("").trigger('change'); // Ensure select2 dropdown resets visually
            $('#party').val("").trigger('change');   // Ensure select2 dropdown resets visually
            $('#party_list').val("").trigger('change'); // Reset party_list
            $('#national_party').val("").trigger('change'); // Reset party_list

            // Reload DataTable with default URL
            $('#ajax-table').DataTable().ajax.url('{{route("poll.elections.candidates.list", $poll_election->id, false)}}').load();
        });       

        $('#candidate_search_modal .close').on('click', function(e) {
            $('#candidate_search_modal').hide();
        });

        $('#btn-candidate-lookup').on('click', function(e) {
            e.preventDefault();
            $('#candidate_search_modal').show();
        });

        $(".candidate-search-ajax").select2({
            width: "100%",
            templateResult: formatCandidateOption,
            templateSelection: formatCandidateSelection,
            ajax: {
                url: "{{ route('poll.candidates.search', [], false) }}",
                dataType: 'json',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data.map(candidate => ({
                            id: candidate.id,
                            text: candidate.name,
                            image: candidate.image ? '/uploads/' + candidate.image : '/images/avatar-classic.png'
                        })),
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            placeholder: 'Search Candidates',
            minimumInputLength: 2,
        }).on('select2:select', function(e) {
            // Enable the "Add Candidate" button when a candidate is selected
            $('#btn-add-candidate-to-election').prop('disabled', false);
        });

        function formatCandidateOption(candidate) {
            if (!candidate.id) {
                return candidate.text;
            }
            var $candidate = $(
                '<div class="d-flex align-items-center">' +
                '<img src="' + candidate.image + '" class="rounded-circle mr-2" style="width:30px;height:30px;" />' +
                '<span>' + candidate.text + '</span>' +
                '</div>'
            );
            return $candidate;
        }

        function formatCandidateSelection(candidate) {
            if (!candidate.id) {
                return candidate.text;
            }
            return candidate.text;
        }

        $("#add_candidate").on('click', function(e) {
            $('.candidate-info').show();
            let candidate_id = $("#candidate_list").find('option:selected').val();
            let position = $("#candidate_position").val();
            let party = $("#candidate_party").val();

            if (!position || !party) {
                alert('Please select both position and party.');
                return;
            }

            $.ajax("{{ route('poll.candidates.show', '', false) }}/" + candidate_id, {
                type: 'GET',
                success: function(data) {
                    $('#btn-add-candidate-to-election').prop('disabled', false);
                    $('#candidate_name').html(data.name);
                    $('#candidate_position_display').html(position);
                    $('#candidate_party_display').html(party);
                },
                error: function() {
                    alert('Candidate not found');
                    $('#btn-add-candidate-to-election').prop('disabled', true);
                }
            });
        });

        $("#btn-add-candidate-to-election").on('click', function(e) {
            let candidate_id = $("#candidate_list").find('option:selected').val();
            let position = $("#candidate_position").val();
            let party = $("#candidate_party").val();
            let party_list = $("#candidate_party_list").val();
            let national_party = $("#candidate_national_party").val();

            if (!candidate_id || !position || !party) {
                alert('Please select a candidate, position, and party.');
                return;
            }

            $(this).prop('disabled', true);
            $.ajax("{{ route('poll.elections.candidates.store', $poll_election->id, false) }}", {
                type: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'candidate_id': candidate_id,
                    'position': position,
                    'party': party,
                    'party_list': party_list,
                    'national_party': national_party,
                },
                success: function(data) {
                    if (data.status === 1) {
                        swal("Candidate has been added to the election!", { icon: "success" });
                        $("#ajax-table").DataTable().ajax.reload();
                        $('#candidate_search_modal').hide();
                        $(this).prop('disabled', false);
                    } else {
                        swal(data.message, { icon: "error" });
                    }
                },
                error: function(data) {
                    if(data.responseJSON.message) {
                        swal(data.responseJSON.message, { icon: "error" });
                    } else {
                        swal("Candidate not added!", { icon: "error" });
                    }
                }
            });
        });

        var watcherTable = $("#watcher-table").DataTable({
            'processing': true,
            'serverSide': true,
            'orderable': false,
            'searching': false,
            'ajax': '{{route("poll.elections.watchers.list", $poll_election->id, false)}}',
            'columns': [
                {
                    data: 'watcher_id',
                    render: function(data, type, row) {
                        let entry_url = "{{route('poll.election.watcher_candidates.show', [$poll_election->id, ':watcher_id'], false)}}".replace(':watcher_id', data);

                        let log_url = "{{route('poll.election.watcher.activity_logs.show', [$poll_election->id, ':watcher_id'], false)}}".replace(':watcher_id', data);
                        
                        return `<input type="checkbox" class="row-checkbox ml-2 mr-3 mt-2">
                        <a class="btn btn-warning btn-sm ml-1 ajax-modal" href="#" data-title="Show Entries" data-href="${entry_url}">Show Entries</a>

                        <a class="btn btn-primary btn-sm ml-1 ajax-modal" href="#" data-title="Watcher Logs" data-href="${log_url}">Logs</a>
                        `;
                    }, 
                    orderable: false, 
                    searchable: false 
                },
                { data: 'watcher_name', orderable: false }, 
                { data: 'brgy', orderable: false },
                { data: 'precinct', orderable: false },
                { data: 'area', orderable: false },
                { data: 'poll_place', orderable: false },
                { data: 'clustered_precincts', orderable: false },
                { 
                    data: 'no_of_registered_voters', 
                    orderable: false,
                    render: function(data, type, row) {
                        return data.toLocaleString();
                    }
                },
                { data: 'no_of_unique_entries', orderable: false },
                {
                    data: 'completion_percentage',
                    render: function(data, type, row) {
                        return `
                            <div class="progress" style="height: 20px; position: relative;">
                                <div class="progress-bar" role="progressbar" style="width: ${data}%; position: relative;" aria-valuenow="${data}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                                <span class="progress-text" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: black; font-weight: bold; white-space: nowrap;">
                                    ${data}%
                                </span>
                            </div>
                        `;
                    }, 
                    orderable: false, 
                    searchable: false 
                },
            ]
        });

        $('#btn-watcher-search').on('click', function(e) {
            let search_field = $('#watcher_search_field').val();
            let search_value = $('#watcher_search_value').val();
            let search_data_arr = [];

            if (search_field && search_value) {
                search_data_arr.push({
                    key: search_field,
                    value: search_value
                });
            }   
            let search_query_params = search_data_arr.map((data) => "filter[" + data.key + "]=" + data.value).join("&");
            if (search_query_params) {
                $('#watcher-table').DataTable().ajax.url('{{route("poll.elections.watchers.list", $poll_election->id, false)}}' + "?" + search_query_params).load();
            } else {
                $('#watcher-table').DataTable().ajax.url('{{route("poll.elections.watchers.list", $poll_election->id, false)}}').load();
            }
        });

        $('#btn-watcher-reset').on('click', function(e) {
            $('#watcher_search_field').val("").trigger('change');;
            $('#watcher_search_value').val("").trigger('change');;
            $('#watcher-table').DataTable().ajax.url('{{route("poll.elections.watchers.list", $poll_election->id, false)}}').load();
        });

        $('#watcher_search_modal .close').on('click', function(e) {
            $('#watcher_search_modal').modal('hide'); // Ensure the modal is properly hidden
        });

        $('#btn-watcher-lookup').on('click', function(e) {
            e.preventDefault();
            $('#watcher_search_modal').modal('show'); // Use Bootstrap's modal show method
        });

        $(".watcher-search-ajax").select2({
            width: "100%",
            dropdownParent: $('#watcher_search_modal'), // Attach dropdown to the modal
            ajax: {
                url: "{{ route('poll.watchers.search', [], false) }}",
                dataType: 'json',
                delay: 2000, // Debounce for 2 seconds
                data: function(params) {
                    return { 
                        q: params.term || '', // Ensure the 'q' parameter is included
                        page: params.page || 1 
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data?.data.map(watcher => ({
                            id: watcher.id,
                            text: `${watcher?.full_name} (${watcher?.brgy} - ${watcher?.precinct})`,
                        })),
                        pagination: { more: (params.page * 30) < data.total }
                    };
                },
                cache: true
            },
            placeholder: 'Search Watchers',
            minimumInputLength: 2,
        }).on('select2:select', function(e) {
            $('#btn-add-watcher-to-election').prop('disabled', false);
        });

        $("#btn-add-watcher-to-election").on('click', function(e) {
            let watcher_id = $("#watcher_list").find('option:selected').val();
            if (!watcher_id) {
                alert('Please select a watcher.');
                return;
            }
            $(this).prop('disabled', true);
            $.ajax("{{ route('poll.elections.watchers.store', $poll_election->id, false) }}", {
                type: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'poll_watcher_id': watcher_id,
                },
                success: function(data) {
                    if (data.status === 1) {
                        swal("Watcher has been added to the election!", { icon: "success" });
                        watcherTable.ajax.reload();
                        $('#watcher_search_modal').modal('hide');
                        $(this).prop('disabled', false);
                    } else {
                        swal(data.message, { icon: "error" });
                    }
                },
                error: function() {
                    if(data.responseJSON.message) {
                        swal(data.responseJSON.message, { icon: "error" });
                    } else {
                        swal("Watcher not added!", { icon: "error" });
                    }
                }
            });
        });

        function loadLeaderboard(position = '') {
            $('#leaderboard-loader').show();
            $('#leaderboard-content').hide();

            $.ajax({
                url: '{{ route("poll.elections.leaderboard", $poll_election->id, false) }}',
                method: 'GET',
                data: { position: position },
                success: function(data) {
                    let content = '<div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Rank</th><th>Candidate</th><th>Votes</th><th>Completion %</th></tr></thead><tbody>';
                    if (data.length > 0) {
                        data.forEach((item, index) => {
                            const placing = index === 0 ? '1st' : index === 1 ? '2nd' : index === 2 ? '3rd' : `${index + 1}th`;
                            content += `
                                <tr>
                                    <td><strong>${placing}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="${item.image}" alt="${item.name}" class="mr-2 rounded-circle" style="width: 50px; height: 50px;">
                                            <div>
                                                <a class="ajax-modal" style="color:black;" href="#" data-title="Candidate Details" data-href="{{ url("/poll/elections/candidates/show/") }}/${item.id}">
                                                    <strong>${item.name}</strong>
                                                </a><br>
                                                <small>${item.position} - ${item.party} ${item.national_party ? ' - ' + item.national_party : ''} ${item.party_list ? ' - ' + item.party_list : ''}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>${item.votes.toLocaleString()}</td>
                                    <td>
                                        <div class="progress" style="height: 20px; position: relative;">
                                            <div class="progress-bar" role="progressbar" style="width: ${item.completion_percentage}%; position: relative;" aria-valuenow="${item.completion_percentage}" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                            <span class="progress-text" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: black; font-weight: bold; white-space: nowrap;">
                                                ${item.completion_percentage}%
                                            </span>
                                        </div>
                                    </td>
                                </tr>`;
                        });
                    } else {
                        content += '<tr><td colspan="4" class="text-center">{{ _lang("No data available.") }}</td></tr>';
                    }
                    content += '</tbody></table></div>';
                    $('#leaderboard-content').html(content).show();
                    $('#leaderboard-loader').hide();
                },
                error: function() {
                    $('#leaderboard-content').html('<p class="text-center text-danger w-100">{{ _lang("Failed to load leaderboard.") }}</p>').show();
                    $('#leaderboard-loader').hide();
                }
            });
        }

        $('#refresh-leaderboard').on('click', function() {
            const position = $('#leaderboard-position-filter').val();
            loadLeaderboard(position);
        });

        $('#leaderboard-position-filter').on('change', function() {
            const position = $(this).val();
            loadLeaderboard(position);
        });

        // Initial load
        loadLeaderboard();
    });
</script>
@endsection