<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="{{ $poll_election_candidate->pollCandidate->image ? "/uploads/{$poll_election_candidate->pollCandidate->image}" : "/images/avatar-classic.png" }}" 
                             alt="{{ $poll_election_candidate->pollCandidate->name }}" 
                             class="rounded-circle" 
                             style="width: 100px; height: 100px;" />
                    </div>
                    <h5 class="text-center font-weight-bold">{{ $poll_election_candidate->pollCandidate->name }}</h5>
                    <p class="text-center text-muted">
                        {{ $poll_election_candidate->position }} <br/>
                        {{ $poll_election_candidate->party }}
                        {{ $poll_election_candidate->national_party ? ' (' . $poll_election_candidate->national_party . ')' : '' }}
                        {{ $poll_election_candidate->party_list ? ' - ' . $poll_election_candidate->party_list : '' }}
                    </p>
                    <p class="text-center mt-2 mb-1">
                        <span class="badge badge-primary p-2">{{ _lang('Rank') }}: <b>{{ $candidate_rank ?? '-' }}</b></span>
                    </p>
                    <hr>
                    <p class="text-center font-weight-bold">{{ _lang('Completion %') }}</p>
                    <div class="progress" style="height: 20px; position: relative;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ $completion_percentage }}%; position: relative;" 
                             aria-valuenow="{{ $completion_percentage }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                        <span class="progress-text" 
                              style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: black; font-weight: bold; white-space: nowrap;">
                            {{ $completion_percentage }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div>
                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs mb-2" id="breakdownTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="brgy-tab" data-toggle="tab" href="#brgy-breakdown" role="tab" aria-controls="brgy-breakdown" aria-selected="true">
                            {{ _lang('Votes Breakdown Per Barangay') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="cluster-tab" data-toggle="tab" href="#cluster-breakdown" role="tab" aria-controls="cluster-breakdown" aria-selected="false">
                            {{ _lang('Votes Breakdown Per Cluster') }}
                        </a>
                    </li>
                </ul>
                <div class="tab-content" id="breakdownTabsContent" style="max-height: 700px; overflow-y: auto; width: 100%;">
                    <!-- Barangay Breakdown Tab -->
                    <div class="tab-pane fade show active" id="brgy-breakdown" role="tabpanel" aria-labelledby="brgy-tab">
                        <table class="table table-bordered table-striped table-sm mb-0">
                            <thead>
                                <tr>
                                    <td colspan="5" class="bg-olive py-2 text-center font-weight-bold">
                                        {{ $poll_election_candidate->pollElection->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-center font-weight-bold position-relative" style="vertical-align: middle;">
                                        {{ _lang('Votes Breakdown Per Barangay') }}
                                        <a href="{{ route('poll.elections.candidates.export', $poll_election_candidate->id, false) }}" 
                                           class="btn btn-success btn-sm position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%);">
                                            <i class="fa fa-download mr-2"></i>{{ _lang('Export') }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ _lang('Barangay') }}</th>
                                    <th>{{ _lang('Votes') }}</th>
                                    <th>{{ _lang('# of Registered Voters') }}</th>
                                    <th>%</th>
                                    <th>{{ _lang('Rank') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($votes_per_brgy as $brgy => $data)
                                <tr>
                                    <td>{{ $brgy }}</td>
                                    <td>{{ number_format($data['votes']) }}</td>
                                    <td>{{ number_format($data['total_registered_voters']) }}</td>
                                    <td>{{ $data['vote_percentage'] }}%</td>
                                    <td>{{ $barangay_ranks[$brgy] ?? '-' }}</td>
                                </tr>
                                @endforeach
                                <tr class="font-weight-bold">
                                    <td>{{ _lang('Total Votes') }}</td>
                                    <td>{{ number_format($total_votes) }}</td>
                                    <td>{{ number_format($votes_per_brgy->sum('total_registered_voters')) }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Cluster Breakdown Tab -->
                    <div class="tab-pane fade" id="cluster-breakdown" role="tabpanel" aria-labelledby="cluster-tab">
                        <table class="table table-bordered table-striped table-sm mb-0">
                            <thead>
                                <tr>
                                    <td colspan="5" class="bg-olive py-2 text-center font-weight-bold">
                                        {{ $poll_election_candidate->pollElection->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-center font-weight-bold position-relative" style="vertical-align: middle;">
                                        {{ _lang('Votes Breakdown Per Cluster') }}
                                        <a href="{{ route('poll.elections.candidates.export_cluster', $poll_election_candidate->id, false) }}" 
                                           class="btn btn-success btn-sm position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%);">
                                            <i class="fa fa-download mr-2"></i>{{ _lang('Export') }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ _lang('Cluster') }}</th>
                                    <th>{{ _lang('Watcher') }}</th>
                                    <th>{{ _lang('Barangay') }}</th>
                                    <th>{{ _lang('Votes') }}</th>
                                    <th>{{ _lang('Rank') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // Prepare cluster breakdown data, remove registered_voters, order by cluster
                                    $cluster_rows = [];
                                    $total_cluster_votes = 0;
                                    foreach ($poll_election_candidate->pollEntries as $entry) {
                                        $watcher = $entry->pollElectionWatcher->pollWatcher ?? null;
                                        $brgy = $watcher ? $watcher->brgy : '';
                                        $watcher_name = $watcher && $watcher->user ? $watcher->user->full_name : '';
                                        $clustered_votes = !empty($entry->clustered_precinct_votes) ? json_decode($entry->clustered_precinct_votes, true) : [];
                                        foreach ($clustered_votes as $cluster => $votes) {
                                            $votes_num = is_numeric($votes) ? (int)$votes : 0;
                                            $cluster_rows[] = [
                                                'cluster' => $cluster,
                                                'votes' => $votes_num,
                                                'watcher' => $watcher_name,
                                                'brgy' => $brgy,
                                            ];
                                            $total_cluster_votes += $votes_num;
                                        }
                                    }
                                    usort($cluster_rows, function($a, $b) {
                                        return intval($a['cluster']) <=> intval($b['cluster']);
                                    });
                                @endphp
                                @forelse($cluster_rows as $row)
                                <tr>
                                    <td>{{ $row['cluster'] }}</td>
                                    <td>{{ $row['watcher'] }}</td>
                                    <td>{{ $row['brgy'] }}</td>
                                    <td>{{ number_format($row['votes']) }}</td>
                                    <td>{{ $cluster_ranks[$row['cluster']] ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">{{ _lang('No data available.') }}</td>
                                </tr>
                                @endforelse
                                <tr class="font-weight-bold">
                                    <td colspan="3" class="text-right">{{ _lang('Total Votes') }}</td>
                                    <td>{{ number_format($total_cluster_votes) }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>