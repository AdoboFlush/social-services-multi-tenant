@extends('layouts.poll_watcher')

@section('content')

<div class="container-fluid mb-1 mt-1">
    <div class="card card-olive" style="width: 100%; height: auto;">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-poll mr-2"></i>{{ _lang('Poll Watcher Dashboard') }}</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    @if($active_election)

                    <div class="row">
                        <div class="col-md-6">
                            <ul>
                                <li><strong>{{ _lang('Name') }}:</strong> {{ $active_election->name }}</li>
                                <li><strong>{{ _lang('Assigned Poll Place') }}:</strong> {{ $watcher->poll_place }}</li>
                                <li><strong>{{ _lang('Established Precincts') }}:</strong> {{ $watcher->precinct }}</li>
                                <li><strong>{{ _lang('Assigned Clustered Precincts') }}:</strong> {{ $watcher->clustered_precincts }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul>
                                <li><strong>{{ _lang('Assigned Barangay') }}:</strong> {{ $watcher->brgy }}</li>
                                <li><strong>{{ _lang('Election Date') }}:</strong> {{ $active_election->election_date }}</li>
                                <li><strong>{{ _lang('Type') }}:</strong> {{ $active_election->type }}</li>
                                <li><strong>{{ _lang('Remarks') }}:</strong> {{ $active_election->remarks }}</li>
                            </ul>
                        </div>
                    </div>

                    @else
                    <div class="alert alert-danger">
                        {{ _lang('No active election found.') }}
                    </div>
                    @endif
                </div>
            </div>

            @if($active_election)

            <div class="card card-olive mt-5" style="width: 100%; height: auto;">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa fa-user mr-2"></i> {{ _lang('Candidates') }}</h3>
                    <span class="float-right">
                        <a href="#" class="btn btn-primary btn-sm ajax-modal" data-title="Activity Logs" data-href="{{ route('poll.guest.watcher.activity_logs.show', [], false) }}" data-fullscreen="true">Show Activity</a>
                    </span>
                </div>
                <div class="card-body">
                    <div class="container-fluid mb-1 mt-1">
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
                                    <label class="control-label">{{ _lang('Party') }}</label>
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
                                    <button class="btn btn-secondary btn-sm form-control" id="btn-reset" type="button"><i class="fa fa-undo mr-2"></i>Refresh</button>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Completion %</label>
                                    <div class="progress" style="height: 20px; position: relative;">
                                        <div class="progress-bar" role="progressbar" style="width: 0%; position: relative;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                        <span class="progress-text" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: black; font-weight: bold; white-space: nowrap;">
                                            0%
                                        </span>
                                    </div>
                                    <p class="completion-details mt-2" style="font-weight: bold; text-align: center;">You completed 0 out of 0 candidates</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                @if($active_election)
                                <div class="table-responsive">
                                    <table id="candidate-ajax-table" class="table table-sm table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th style="min-width:200px;">Details</th>
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
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
        </div>
    </div>
</div>


@endsection

@section('js-script')

<script>
    $(function() {

        @if($active_election)

        // Customize DataTable loader with a spinning loader circle
        $.fn.dataTable.ext.classes.sProcessing = 'dataTables_processing';

        // Fetch and display the completion percentage and details on page load
        refreshCompletionPercentage();

        function refreshCompletionPercentage() {
            $.ajax({
                url: "{{ route('poll.guest.watcher.stats', [], false) }}",
                type: "get",
                success: function(data) {
                    if (data.status === 1) {
                        let completionPercentage = data.data.completion_percentage;
                        let totalEntryCount = data.data.total_entry_count;
                        let totalCandidates = data.data.total_candidates;

                        // Update progress bar
                        $('.progress-bar').css('width', completionPercentage + '%').attr('aria-valuenow', completionPercentage);
                        $('.progress-text').text(completionPercentage + '%');

                        // Update completion details
                        $('.completion-details').text(`You completed ${totalEntryCount} out of ${totalCandidates} candidates`);
                    }
                },
                error: function() {
                    console.error("Failed to fetch completion percentage.");
                }
            });
        }

        $('#candidate-ajax-table thead tr:eq(1) th').each(function() {
            var title = $(this).text();
            if (title != 'Actions') {
                $(this).html('<input type="text" placeholder="Search ' + title + '" class="column_search" />');
            } else {
                $(this).html('');
            }
        });

        var table = $("#candidate-ajax-table").DataTable({
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
            'language': {
                'processing': `
                    <div style="display: flex; justify-content: center; align-items: center; height: 100%;">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                `
            },
            'initComplete': function(settings, json) {
                table.buttons().container().appendTo('#candidate-ajax-table_wrapper .col-md-6:eq(0)');
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
            "rowCallback": function(row, data, index) {
                if (data.status) {
                    $('td', row).css({
                        'background-color': '#28a745',
                        'color': "white"
                    });
                }
            },
            'ajax': {
                'url': '{{route("poll.guest.election.candidates.get", $active_election->id, false)}}',
            },
            'columns': [
                {
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
                                    <small>
                                        ${row.position ? row.position : ''}
                                        ${row.party ? ' - ' + row.party : ''}
                                        ${row.national_party ? ' - ' + row.national_party : ''}
                                        ${row.party_list ? ' - ' + row.party_list : ''}
                                    </small>
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
                @foreach($clustered_precinct_arr as $clustered_precinct_number)
                {
                    data: null,
                    responsivePriority: 3,
                    render: function(data, type, row) {
                        let cvotes = row.clustered_votes ? JSON.parse(row.clustered_votes) : {};
                        let current_votes = cvotes[{{$clustered_precinct_number}}] ?? 0;
                        return `<button class="btn btn-sm btn-warning btn-field-update mr-2" 
                            data-field="clustered_votes"
                            data-value="${current_votes}" 
                            data-id="${row.id}"  
                            data-name="${row.name}" 
                            data-position="${row.position}" 
                            data-party="${row.party}" 
                            data-image="${row.image}"
                            data-clustered_precinct_number='{{$clustered_precinct_number}}'>
                            <i class="nav-icon fa fa-edit"></i>
                        </button><span class="clustered-votes">${current_votes.toLocaleString()}</span>`;
                    }
                },
                @endforeach
                {
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

        $(document).on("click", '.btn-field-update', function() {
            let field = $(this).data('field');
            let value = $(this).data('value');
            let candidate_id = $(this).data('id');
            let clustered_precinct_number = $(this).data('clustered_precinct_number');
            // Hide all other buttons and show only the clicked one
            $('.btn-field-update').not(this).show();
            $('.clustered-votes').show(); // Show all total votes
            $(this).hide();
            $(this).siblings('.clustered-votes').hide(); // Hide the total votes for the clicked button

            $('.field_updater').remove();

            let votes_field_html = `<div class="field_updater mt-2">
                <input type="number" class="form-control mb-2" name="field_${field}" id="field_${field}" value="${value}" />
                <div class="input-group-append" id="process_field_update" style="cursor:pointer;" data-field="${field}" data-id="${candidate_id}" data-clustered_precinct_number="${clustered_precinct_number}">
                    <div class="input-group-text"><i class="fa fa-paper-plane mr-2"></i> IPADALA</div>
                </div>
            </div>`;

            $(this).parent('td').append(votes_field_html);

            // Explicitly focus on the input after it is appended
            $(`#field_${field}`).focus();
        });

        $(document).on("click", '#process_field_update', function() {

            let field_name = $(this).data('field');
            let selected_id = $(this).data('id');
            let field_value = $('#field_' + field_name).val();
            let clustered_precinct_number = $(this).data('clustered_precinct_number');

            let ajax_url = "{{route('poll.guest.election.candidates.upsert_votes', [$active_election->id, ':selected_id'], false)}}".replace(':selected_id', selected_id);

            $.ajax({
                url: ajax_url,
                type: "post",
                data: {
                    'field' : field_name,
                    'clustered_precinct_number' : clustered_precinct_number,
                    'votes': field_value,
                    '_token': '{{csrf_token()}}'
                },
                success: function(data, textStatus, jqXHR) {
                    if (data.status == 1) {
                        swal("Votes have been updated", {
                            icon: "success",
                        });
                        $('.field_updater').remove();
                        $('.btn-field-update').show(); // Show all buttons after processing
                        $('.clustered-votes').show(); // Show all total votes after processing
                        refreshCompletionPercentage(); // Refresh completion percentage
                    } else {
                        swal("Update failed. Error: " + data.message, {
                            icon: "error",
                        });
                    }
                    $("#candidate-ajax-table").DataTable().ajax.reload(null, false);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    swal("Update failed!", {
                        icon: "error",
                    });
                }
            });
        });

        $(document).on("keypress", '#field_clustered_votes', function(e) {
            if (e.which === 13) { // Check if 'Enter' key is pressed
                $('#process_field_update').trigger('click'); // Trigger the click event on the update button
            }
        });

        // Apply the search
        $('#candidate-ajax-table thead').on('keyup', ".column_search", function() {
            table
                .column($(this).parent().index())
                .search(this.value)
                .draw();
        });

        $('#position, #party, #party_list, #national_party, #has_votes').on('change', function() {
            let position = $('#position').val();
            let party = $('#party').val();
            let party_list = $('#party_list').val();
            let national_party = $('#national_party').val();
            let hasVotes = $('#has_votes').val();
            let search_data_arr = [];

            if (position) {
                search_data_arr.push({ key: "position", value: position });
            }
            if (party) {
                search_data_arr.push({ key: "party", value: party });
            }
            if (party_list) {
                search_data_arr.push({ key: "party_list", value: party_list });
            }
            if (national_party) {
                search_data_arr.push({ key: "national_party", value: national_party });
            }
            if (hasVotes && hasVotes !== 'all') {
                search_data_arr.push({ key: "has_votes", value: hasVotes });
            }

            let search_query_params = search_data_arr.map((data) => "filter[" + data.key + "]=" + data.value).join("&");
            if (search_query_params) {
                $('#candidate-ajax-table').DataTable().ajax.url('{{route("poll.guest.election.candidates.get", $active_election->id, false)}}?' + search_query_params).load();
            } else {
                $('#candidate-ajax-table').DataTable().ajax.url('{{route("poll.guest.election.candidates.get", $active_election->id, false)}}').load();
            }
        });

        $('#btn-reset').on('click', function(e) {
            // Reset filter fields
            $('#position').val("").trigger('change'); // Ensure select2 dropdown resets visually
            $('#party').val("").trigger('change');   // Ensure select2 dropdown resets visually
            $('#party_list').val("").trigger('change'); // Ensure select2 dropdown resets visually

            // Reload DataTable with default URL
            $('#candidate-ajax-table').DataTable().ajax.url('{{route("poll.guest.election.candidates.get", $active_election->id, false)}}').load();
        });

        @endif

    });
</script>

@endsection