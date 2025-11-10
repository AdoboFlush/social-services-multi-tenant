<?php

namespace App\Services\Poll;

use App\PollCandidate;
use App\PollElection;
use App\PollElectionCandidate;
use App\PollElectionWatcher;
use App\PollEntry;
use App\PollWatcher;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PollService extends BaseService
{

    public function getElections(Request $request)
    {
        $model = new PollElection();
        $model = $this->filterElections($model, $request);
        $total_count = $model->count();
        $model = $this->buildModelQueryDataTable($model, $request);
        return [
            "data" => $model->get(),
            "count" => $total_count,
        ];
    }

    public function filterElections(PollElection $model, Request $request)
    {
        if($request->has('filter')) {
            $model = $model->when($request->filter['name'], function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->filter['name'] . '%');
            })->when($request->filter['election_date'], function ($query) use ($request) {
                return $query->where('election_date', Carbon::parse($request->filter['election_date'])
                ->toDateString());
            })->when($request->filter['type'], function ($query) use ($request) {
                return $query->where('type', 'like', '%' . $request->filter['type'] . '%');
            });
        }
        return $model;
    }

    public function getCandidates(Request $request)
    {
        $model = new PollCandidate;
        $model = $this->filterCandidates($model, $request);
        $total_count = $model->count();
        $model = $this->buildModelQueryDataTable($model, $request);
        return [
            "data" => $model->get(),
            "count" => $total_count,
        ];
    }

    public function filterCandidates(PollCandidate $model, Request $request)
    {
        $model = $model->when($request->election_id, function ($query) use ($request) {
            return $query->where('poll_election_id', $request->election_id);
        });

        if($request->has('filter')) {
            $model = $model->when($request->filter['name'], function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->filter['name'] . '%');
            })->when($request->filter['party'], function ($query) use ($request) {
                return $query->where('party', 'like', '%' . $request->filter['party'] . '%');
            })->when($request->filter['position'], function ($query) use ($request) {
                return $query->where('position', 'like', $request->filter['position'] . '%');
            })->when($request->filter['national_party'], function ($query) use ($request) {
                return $query->where('national_party', 'like', $request->filter['national_party'] . '%');
            });
        }
        return $model;
    }

    public function getElectionCandidates(Request $request, PollElection $poll_election)
    {
        $model = $poll_election->pollElectionCandidates()
            ->with('pollCandidate:id,name,image') // Include name and image fields
            ->when($request->has('filter'), function ($query) use ($request) {
                $query->when(isset($request->filter["position"]), function ($q) use ($request) {
                    return $q->where('position', 'like', $request->filter['position'] . '%');
                })->when(isset($request->filter["party"]), function ($q) use ($request) {
                    return $q->where('party', 'like', '%' . $request->filter['party'] . '%');
                })->when(isset($request->filter["national_party"]), function ($q) use ($request) {
                    return $q->where('national_party', 'like', '%' . $request->filter['national_party'] . '%');
                });
            })
            ->get()
            ->map(function ($electionCandidate) {
                return [
                    'id' => $electionCandidate->id,
                    'position' => $electionCandidate->position,
                    'party' => $electionCandidate->party,
                    'party_list' => $electionCandidate->party_list,
                    'national_party' => $electionCandidate->national_party,
                    'name' => $electionCandidate->pollCandidate->name ?? 'N/A',
                    'image' => $electionCandidate->pollCandidate->image ?? null,
                ];
            });

        return [
            "data" => $model,
            "count" => $model->count(),
        ];
    }

    public function getElectionCandidatesWithVotes(Request $request, PollElection $poll_election)
    {
        $model = $poll_election->pollElectionCandidates()
            ->with(['pollCandidate:id,name,image', 'pollEntries', 'pollElection.pollElectionWatchers.pollWatcher'])
            ->when($request->has('filter'), function ($query) use ($request) {
                $query->when(isset($request->filter["position"]), function ($q) use ($request) {
                    return $q->where('position', 'like', $request->filter['position'] . '%');
                })->when(isset($request->filter["party"]), function ($q) use ($request) {
                    return $q->where('party', 'like', '%' . $request->filter['party'] . '%');
                })->when(isset($request->filter["national_party"]), function ($q) use ($request) {
                    return $q->where('national_party', 'like', '%' . $request->filter['national_party'] . '%');
                })->when(isset($request->filter["party_list"]), function ($q) use ($request) {
                    return $q->where('party_list', 'like', '%' . $request->filter['party_list'] . '%');
                });
            });
        $total_count = $model->count();
        $model = $this->buildModelQueryDataTable($model, $request);

        return [
            "data" => $model->get()
                ->map(function ($electionCandidate) use ($poll_election) {
                    $totalUniqueWatchers = $poll_election->pollElectionWatchers->pluck('poll_watcher_id')->unique()->count();
                    $uniqueWatchersWithEntries = $electionCandidate->pollEntries
                        ->where('status', 1)
                        ->pluck('pollElectionWatcher.poll_watcher_id')
                        ->unique()
                        ->count();
                    $completionPercentage = $totalUniqueWatchers > 0 ? round(($uniqueWatchersWithEntries / $totalUniqueWatchers) * 100, 2) : 0;

                    return [
                        'id' => $electionCandidate->id,
                        'position' => $electionCandidate->position,
                        'party' => $electionCandidate->party,
                        'party_list' => $electionCandidate->party_list,
                        'national_party' => $electionCandidate->national_party,
                        'name' => $electionCandidate->pollCandidate->name ?? 'N/A',
                        'image' => !empty($electionCandidate->pollCandidate->image) 
                            ? '/uploads/'.$electionCandidate->pollCandidate->image 
                            : asset('images/avatar-classic.png'),
                        'total_votes' => $electionCandidate->pollEntries->sum('votes'),
                        'completion_percentage' => $completionPercentage,
                    ];
                }),
            "count" => $total_count,
        ];
    }

    public function getElectionCandidatesWithWatcherVotes(Request $request, PollElection $poll_election, PollWatcher $watcher)
    {
        $model = $poll_election->pollElectionCandidates()
            ->with(['pollCandidate:id,name,image', 'pollEntries' => function ($query) use ($watcher) {
                $query->whereHas('pollElectionWatcher', function ($q) use ($watcher) {
                    $q->where('poll_watcher_id', $watcher->id);
                });
            }])
            ->when($request->has('filter'), function ($query) use ($request) {
                $query->when(isset($request->filter["position"]), function ($q) use ($request) {
                    return $q->where('position', 'like', $request->filter['position'] . '%');
                })->when(isset($request->filter["party"]), function ($q) use ($request) {
                    return $q->where('party', 'like', '%' . $request->filter['party'] . '%');
                })->when(isset($request->filter["national_party"]), function ($q) use ($request) {
                    return $q->where('national_party', 'like', '%' . $request->filter['national_party'] . '%');
                })->when(isset($request->filter["party_list"]), function ($q) use ($request) {
                    return $q->where('party_list', 'like', '%' . $request->filter['party_list'] . '%');
                })->when(isset($request->filter["has_votes"]) && $request->filter["has_votes"] !== 'all', function ($q) use ($request) {
                    if ($request->filter["has_votes"] == '1') {
                        return $q->has('pollEntries');
                    } else {
                        return $q->doesntHave('pollEntries');
                    }
                });
            });
        
        $total_count = $model->count();
        $model = $this->buildModelQueryDataTable($model, $request);

        return [
            "data" => $model->get()
                ->map(function ($electionCandidate) {
                    $entryCountByWatcher = $electionCandidate->pollEntries->count();
                    $totalVotesByWatcher = $electionCandidate->pollEntries->sum('votes');
                    $pollEntry = $electionCandidate->pollEntries->first();
                    $clustered_votes = $pollEntry->clustered_precinct_votes ?? null;
                    return [
                        'id' => $electionCandidate->id,
                        'position' => $electionCandidate->position,
                        'national_party' => $electionCandidate->national_party,
                        'party' => $electionCandidate->party,
                        'party_list' => $electionCandidate->party_list, // Add party_list
                        'name' => $electionCandidate->pollCandidate->name ?? 'N/A',
                        'image' => !empty($electionCandidate->pollCandidate->image) 
                            ? '/uploads/'.$electionCandidate->pollCandidate->image 
                            : asset('images/avatar-classic.png'),
                        'total_votes_by_watcher' => $totalVotesByWatcher,
                        'entry_count_by_watcher' => $entryCountByWatcher,
                        'clustered_votes' => $clustered_votes,
                        'clustered_vote_entry_count' => $pollEntry->clustered_vote_entry_count ?? 0,
                        'status' => $pollEntry->status ?? 0,
                    ];
                }),
            "count" => $total_count,
        ];
    }

    public function getElectionWatchers(Request $request, PollElection $poll_election)
    {
        $model = $poll_election->pollElectionWatchers()
        ->with(['pollWatcher', 'pollWatcher.user', 'pollEntries']);

        if($request->has('filter')) {
            $request_arr = $request->all();
            foreach ($request_arr['filter'] as $field => $value) {
                $model = $model->when(in_array($field, ['precinct', 'brgy', 'area' ,'poll_place', 'clustered_precincts']), 
                    fn($q) => $q->whereHas('pollWatcher', fn($q) => $q->where($field, 'LIKE', "%{$value}%"))
                )->when($field == "name", 
                    fn($q) => $q->whereHas('pollWatcher.user', fn($q) => $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $value . '%']))
                );
            }
        }

        $total_count = $model->count();
        $model = $this->buildModelQueryDataTable($model, $request);
        $total_candidates = $poll_election->pollElectionCandidates()->count();

        $model = $model->get()
            ->map(function ($data) use ($total_candidates) {

                $unique_entries = $data->pollEntries()
                    ->where('status', 1)
                    ->pluck('poll_election_candidate_id')
                    ->unique()
                    ->count();

                $completionPercentage = $total_candidates > 0 ? round(($unique_entries / $total_candidates) * 100, 2) : 0;
                
                return [
                    "watcher_name" => $data->pollWatcher->user->full_name,
                    "precinct" => $data->pollWatcher->precinct,
                    "brgy" => $data->pollWatcher->brgy,
                    "id" => $data->id,
                    "area" => $data->pollWatcher->area,
                    "poll_place" => $data->pollWatcher->poll_place,
                    "clustered_precincts" => $data->pollWatcher->clustered_precincts,
                    "no_of_registered_voters" => $data->pollWatcher->no_of_registered_voters,
                    "completion_percentage" => $completionPercentage,
                    "no_of_unique_entries" => $unique_entries,
                    "watcher_id" => $data->pollWatcher->id,
                ];
            });
        
        return [
            "data" => $model,
            "count" => $total_count,
        ];
    }

    public function getWatchers(Request $request)
    {
        $model = PollWatcher::with('user');
        $model = $this->filterWatchers($model, $request);
        $total_count = $model->count();
        $model = $this->buildModelQueryDataTable($model, $request);
        return [
            "data" => $model->get(),
            "count" => $total_count,
        ];
    }

    public function filterWatchers(Builder $model, Request $request)
    {
        if($request->has('filter')) {
            $model = $model->when($request->filter['brgy'], function ($query) use ($request) {
                return $query->where('brgy', 'like', '%' . $request->filter['brgy'] . '%');
            })->when($request->filter['precinct'], function ($query) use ($request) {
                return $query->where('precinct', 'like', '%' . $request->filter['precinct'] . '%');
            });
        }
        return $model;
    }

    public function getEntries(Request $request)
    {
        $model = PollEntry::with([
            'pollElectionCandidate', 
            'pollElectionWatcher.pollWatcher.user', 
            'pollElection']);
        $model = $this->filterEntries($model, $request);
        $total_count = $model->count();
        $model = $this->buildModelQueryDataTable($model, $request);
        return [
            "data" => $model->get(),
            "count" => $total_count,
        ];
    }

    public function getWatcherEntries(Request $request, PollElectionWatcher $poll_election_watcher)
    {
        $poll_election_watcher->pollElection()->pollEntries()
            ->with([
                'pollElectionCandidate', 
                'pollElectionWatcher.pollWatcher.user', 
                'pollElection'])
            ->get()
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'votes' => $entry->votes,
                    'remarks' => $entry->remarks,
                    'poll_election_candidate_id' => $entry->pollElectionCandidate->id,
                    'poll_election_watcher_id' => $entry->pollElectionWatcher->id,
                    'poll_election_id' => $entry->pollElection->id,
                ];
            });
    }

    public function filterEntries(Builder $model, Request $request)
    {
        if($request->has('filter')) {
            // $model = $model->when($request->filter['brgy'], function ($query) use ($request) {
            //     return $query->where('brgy', 'like', '%' . $request->filter['brgy'] . '%');
            // })->when($request->filter['precinct'], function ($query) use ($request) {
            //     return $query->where('precinct', 'like', '%' . $request->filter['precinct'] . '%');
            // });
        }
        return $model;
    }

    public function deleteWatcherVotes(PollElectionCandidate $poll_election_candidate, PollElectionWatcher $poll_election_watcher)
    {
        try {
            return PollEntry::where('poll_election_candidate_id', $poll_election_candidate->id)
                ->where('poll_election_watcher_id', $poll_election_watcher->id)
                ->delete();
        } catch (Exception $e) {
            Log::error('Failed to delete votes: ' . $e->getMessage());
            return false;
        }
    }

    public function getElectionCandidateLeaderboard(PollElection $poll_election, $position = null)
    {
        $query = $poll_election->pollElectionCandidates()
            ->with(['pollCandidate:id,name,image', 'pollEntries', 'pollElection.pollElectionWatchers.pollWatcher']);

        if ($position) {
            $query->where('position', $position);
        }

        $leaderboard = $query->get()
            ->map(function ($candidate) use ($poll_election) {
                $totalUniqueWatchers = $poll_election->pollElectionWatchers->pluck('poll_watcher_id')->unique()->count();
                $uniqueWatchersWithEntries = $candidate->pollEntries
                    ->where('status', 1)
                    ->pluck('pollElectionWatcher.poll_watcher_id')->unique()->count();

                $completionPercentage = $totalUniqueWatchers > 0 ? round(($uniqueWatchersWithEntries / $totalUniqueWatchers) * 100, 2) : 0;

                return [
                    'id' => $candidate->id,
                    'name' => $candidate->pollCandidate->name ?? 'N/A',
                    'image' => $candidate->pollCandidate->image ? asset('uploads/' . $candidate->pollCandidate->image) : asset('images/avatar-classic.png'),
                    'votes' => $candidate->pollEntries->sum('votes'),
                    'party' => $candidate->party,
                    'party_list' => $candidate->party_list,
                    'national_party' => $candidate->national_party,
                    'position' => $candidate->position,
                    'completion_percentage' => $completionPercentage,
                ];
            })
            ->sortByDesc('votes')
            ->take(10) // Limit to 10 rows
            ->values();

        return $leaderboard->toArray();
    }

    // Get all watcher candidates (not paginated) for export
    public function getElectionCandidatesWithWatcherVotesAll(Request $request, PollElection $poll_election, PollWatcher $watcher)
    {
        $model = $poll_election->pollElectionCandidates()
            ->with(['pollCandidate:id,name,image', 'pollEntries' => function ($query) use ($watcher) {
                $query->whereHas('pollElectionWatcher', function ($q) use ($watcher) {
                    $q->where('poll_watcher_id', $watcher->id);
                });
            }]);
        if($request->has('filter')) {
            if(isset($request->filter['position'])) {
                $model = $model->where('position', 'like', $request->filter['position'] . '%');
            }
            if(isset($request->filter['party'])) {
                $model = $model->where('party', 'like', '%' . $request->filter['party'] . '%');
            }
            if(isset($request->filter['national_party'])) {
                $model = $model->where('national_party', 'like', '%' . $request->filter['national_party'] . '%');
            }
        }
        $rows = $model->get()->map(function ($candidate) use ($watcher) {
            $clustered_votes = [];
            $total_votes = 0;
            $clustered_vote_entry_count = 0;
            foreach ($candidate->pollEntries as $entry) {
                if ($entry->clustered_precinct_votes) {
                    $json = json_decode($entry->clustered_precinct_votes, true);
                    if (is_array($json)) {
                        foreach ($json as $cluster => $votes) {
                            $clustered_votes[$cluster] = ($clustered_votes[$cluster] ?? 0) + intval($votes);
                            $total_votes += intval($votes);
                        }
                        $clustered_vote_entry_count++;
                    }
                }
            }
            return [
                'name' => $candidate->pollCandidate->name ?? '',
                'position' => $candidate->position ?? '',
                'party' => $candidate->party ?? '',
                'national_party' => $candidate->national_party ?? '',
                'clustered_votes' => json_encode($clustered_votes),
                'total_votes_by_watcher' => $total_votes,
                'clustered_vote_entry_count' => $clustered_vote_entry_count,
            ];
        })->toArray();
        return ['data' => $rows];
    }

}