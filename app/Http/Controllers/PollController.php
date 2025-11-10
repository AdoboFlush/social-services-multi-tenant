<?php

namespace App\Http\Controllers;

use App\PollActivityLog;
use App\PollElection;
use App\PollCandidate;
use App\PollElectionCandidate;
use App\PollElectionWatcher;
use App\PollWatcher;
use App\PollEntry;
use App\Services\Poll\PollFacade;
use App\Services\Tag\TagFacade;
use Illuminate\Http\Request;
use App\User;
use App\VoterTagDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PollController extends Controller
{

    private $poll_service;
    private $tag_service;

    public function __construct(PollFacade $poll_service, TagFacade $tag_service)
    {
        $this->poll_service = $poll_service;
        $this->tag_service = $tag_service;
    }

    // Election

    public function electionIndex(Request $request)
    {
        if($request->ajax()){
            $election = $this->poll_service::getElections($request);
            return response()->json([
                'data'=> $election['data'],
                'recordsTotal' => $election['count'],
                'recordsFiltered' => $election['count'],
                'start' => $request->start,
                'length' => $request->length,
                'draw' => $request->draw,
            ]);
        }else{
            return view('backend.poll.election.list');
        }
    }

    public function electionCandidatesList(Request $request, PollElection $poll_election)
    {
        $election = $this->poll_service::getElectionCandidatesWithVotes($request, $poll_election);
        $election['data'] = collect($election['data'])->map(function($row) {
            $row['party_list'] = $row['party_list'] ?? null;
            return $row;
        })->toArray();
        return response()->json([
            'data' => $election['data'],
            'recordsTotal' => $election['count'],
            'recordsFiltered' => $election['count'],
            'start' => $request->start,
            'length' => $request->length,
            'draw' => $request->draw,
        ]);
    }

    public function electionWatchersList(Request $request, PollElection $poll_election)
    {
        $election = $this->poll_service::getElectionWatchers($request, $poll_election);
        return response()->json([
            'data'=> $election['data'],
            'recordsTotal' => $election['count'],
            'recordsFiltered' => $election['count'],
            'start' => $request->start,
            'length' => $request->length,
            'draw' => $request->draw,
        ]);
    }

    public function createElection(Request $request)
    {
        return view('backend.poll.election.modal.create');
    }

    public function storeElection(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'election_date' => 'required|date',
            'type' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        PollElection::where("is_active",  1)->update(["is_active" => false]); // disable the existing active election first.

        PollElection::create(array_merge($validated, ['is_active' => true, 'status' => PollElection::STATUS_PENDING]));

        return redirect('poll/elections')->with('success', 'Election created successfully.');
    }

    public function editElection(Request $request, PollElection $poll_election)
    {
        return view('backend.poll.election.modal.edit', compact('poll_election'));
    }

    public function updateElection(Request $request, PollElection $poll_election)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'election_date' => 'required|date',
            'type' => 'required|string|max:255',
            'remarks' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        
        if($validated['is_active'] == 1) {
            PollElection::where('id', '!=', $poll_election->id)->update(['is_active' => false]);
        } else {
            PollElection::where('id', '!=', $poll_election->id)->update(['is_active' => true]);
        }

        $poll_election->update($validated);

        return redirect('poll/elections')->with('success', 'Election updated successfully.');
    }

    public function deleteElection(Request $request, PollElection $poll_election)
    {
        $poll_election->delete();

        return redirect()->route('election.index')->with('success', 'Election deleted successfully.');
    }

    public function showElectionDetails(Request $request, PollElection $poll_election)
    {
        return view('backend.poll.election.election_details', compact('poll_election'));
    }

    public function getElectionCandidates(Request $request, PollElection $poll_election)
    {
        $candidates = $poll_election->pollElectionCandidates()->with('pollCandidate')->get();
        return response()->json($candidates);
    }

    public function getFilteredElectionCandidates(Request $request, PollElection $poll_election)
    {
        if ($request->has('q')) {
            $query = $poll_election->pollElectionCandidates()->with('pollCandidate');
            $query->whereHas('pollCandidate', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%');
            });
            $candidates = $query->paginate(10);
            return response()->json([
                'data' => $candidates->map(fn ($candidate) => [
                    'id' => $candidate->id,
                    'name' => $candidate->pollCandidate->name,
                ]),
                'total_count' => $candidates->total(),
            ]);
        }
        return response()->json(['data' => [], 'total_count' => 0]);
    }

    public function addCandidateToElection(Request $request, PollElection $poll_election)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:poll_candidates,id',
            'position' => 'required|string|max:255',
            'party' => 'required|string|max:255',
            'party_list' => 'nullable|string|max:255',
            'national_party' => 'nullable|string|max:255',
        ]);

        // Check if the candidate already exists in the election
        $exists = \App\PollElectionCandidate::where('poll_election_id', $poll_election->id)
            ->where('poll_candidate_id', $validated['candidate_id'])
            ->exists();

        if ($exists) {
            return response()->json(['status' => 0, 'message' => 'Candidate already exists in this election.'], 422);
        }

        $poll_election_candidate = new \App\PollElectionCandidate([
            'poll_election_id' => $poll_election->id,
            'poll_candidate_id' => $validated['candidate_id'],
            'position' => $validated['position'],
            'party' => $validated['party'],
            'national_party' => $validated['national_party'],
            'party_list' => $validated['party_list'] ?? null,
        ]);

        $poll_election_candidate->save();

        return response()->json(['status' => 1, 'message' => 'Candidate added successfully.']);
    }

    public function editElectionCandidate(Request $request, PollElectionCandidate $poll_election_candidate)
    {
        return view('backend.poll.election.modal.edit_election_candidate', compact('poll_election_candidate'));
    }

    public function showElectionCandidateDetails(PollElectionCandidate $poll_election_candidate)
    {
        $poll_election_candidate = $poll_election_candidate->load(['pollCandidate', 'pollElection', 'pollEntries.pollElectionWatcher.pollWatcher']);

        $total_votes = $poll_election_candidate->pollEntries->sum('votes'); // Define total votes

        // Group watchers by barangay and calculate total registered voters per barangay
        $registered_voters_per_brgy = $poll_election_candidate->pollEntries
            ->pluck('pollElectionWatcher.pollWatcher')
            ->groupBy('brgy')
            ->map(fn ($watchers) => $watchers->unique('id')->sum('no_of_registered_voters'));

        $votes_per_brgy = $poll_election_candidate->pollEntries
            ->sortBy(fn($entry) => $entry->pollElectionWatcher->pollWatcher->id)
            ->groupBy('pollElectionWatcher.pollWatcher.brgy')
            ->map(function ($entries, $brgy) use ($total_votes, $registered_voters_per_brgy) {
                $votes = $entries->sum('votes');
                $total_registered_voters = $registered_voters_per_brgy[$brgy] ?? 0;
                $vote_percentage = $total_registered_voters > 0 ? round(($votes / $total_registered_voters) * 100, 2) : 0;
                return [
                    'votes' => $votes,
                    'vote_percentage' => $vote_percentage,
                    'total_registered_voters' => $total_registered_voters,
                ];
            });

        // Calculate completion percentage
        $poll_election = $poll_election_candidate->pollElection;
        $total_watchers = $poll_election->pollElectionWatchers->pluck('poll_watcher_id')->unique()->count();
        $watchers_with_entries = $poll_election_candidate
            ->pollEntries
            ->where('status', 1)
            ->pluck('pollElectionWatcher.poll_watcher_id')->unique()->count();
        $completion_percentage = $total_watchers > 0 ? round(($watchers_with_entries / $total_watchers) * 100, 2) : 0;

        // --- RANK CALCULATION ---
        $all_candidates = $poll_election->pollElectionCandidates()
            ->where('position', $poll_election_candidate->position)
            ->with(['pollEntries.pollElectionWatcher.pollWatcher'])
            ->get();
        $candidate_votes = $all_candidates->mapWithKeys(function($c) {
            return [$c->id => $c->pollEntries->sum('votes')];
        });
        $sorted = collect($candidate_votes)->sortByDesc(function($votes, $cid) { return $votes; });
        $rank = 1;
        $candidate_rank = null;
        foreach ($sorted as $cid => $votes) {
            if ($cid == $poll_election_candidate->id) {
                $candidate_rank = $rank;
                break;
            }
            $rank++;
        }
        // --- END RANK CALCULATION ---

        // --- RANK PER BARANGAY ---
        $barangay_ranks = [];
        $barangays = $poll_election->pollElectionWatchers->pluck('pollWatcher.brgy')->unique()->filter()->values();
        foreach ($barangays as $brgy) {
            $votes_in_brgy = $all_candidates->mapWithKeys(function($c) use ($brgy) {
                $votes = $c->pollEntries->filter(function($entry) use ($brgy) {
                    return $entry->pollElectionWatcher && $entry->pollElectionWatcher->pollWatcher && $entry->pollElectionWatcher->pollWatcher->brgy == $brgy;
                })->sum('votes');
                return [$c->id => $votes];
            });
            $sorted_brgy = collect($votes_in_brgy)->sortByDesc(function($votes, $cid) { return $votes; });
            $brgy_rank = 1;
            foreach ($sorted_brgy as $cid => $votes) {
                if ($cid == $poll_election_candidate->id) {
                    $barangay_ranks[$brgy] = $brgy_rank;
                    break;
                }
                $brgy_rank++;
            }
        }
        // --- END RANK PER BARANGAY ---

        // --- RANK PER CLUSTER ---
        $cluster_ranks = [];
        $cluster_rows = [];
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
            }
        }
        usort($cluster_rows, function($a, $b) {
            return intval($a['cluster']) <=> intval($b['cluster']);
        });
        // For each cluster, calculate rank
        $all_clusters = collect($cluster_rows)->pluck('cluster')->unique()->sort()->values();
        foreach ($all_clusters as $cluster_num) {
            $votes_in_cluster = $all_candidates->mapWithKeys(function($c) use ($cluster_num) {
                $votes = 0;
                foreach ($c->pollEntries as $entry) {
                    if (!empty($entry->clustered_precinct_votes)) {
                        $json = json_decode($entry->clustered_precinct_votes, true);
                        if (is_array($json) && isset($json[$cluster_num])) {
                            $votes += intval($json[$cluster_num]);
                        }
                    }
                }
                return [$c->id => $votes];
            });
            $sorted_cluster = collect($votes_in_cluster)->sortByDesc(function($votes, $cid) { return $votes; });
            $cluster_rank = 1;
            foreach ($sorted_cluster as $cid => $votes) {
                if ($cid == $poll_election_candidate->id) {
                    $cluster_ranks[$cluster_num] = $cluster_rank;
                    break;
                }
                $cluster_rank++;
            }
        }
        // --- END RANK PER CLUSTER ---

        return view('backend.poll.election.modal.show_election_candidate_details', compact(
            'poll_election_candidate',
            'votes_per_brgy',
            'total_votes',
            'completion_percentage',
            'candidate_rank',
            'barangay_ranks',
            'cluster_ranks',
            'cluster_rows'
        ));
    }

    public function updateElectionCandidate(Request $request, PollElectionCandidate $poll_election_candidate)
    {
        $validated = $request->validate([
            'position' => 'required|string|max:255',
            'party' => 'required|string|max:255',
            'party_list' => 'nullable|string|max:255',
            'national_party' => 'nullable|string|max:255',
        ]);

        $poll_election_candidate->update([
            'position' => $validated['position'],   
            'party' => $validated['party'],
            'national_party' => $validated['national_party'],
            'party_list' => $validated['party_list'] ?? null,
        ]);

        return back()->withSuccess('Candidate updated successfully.');
    }
    
    public function searchWatchers(Request $request)
    {
        if ($request->has('q')) {
            $query = PollWatcher::with(['user'])
            ->whereHas('user', function ($q) use ($request) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $request->q . '%']);
            });
            $watchers = $query->paginate(10);
            return response()->json([
                'data' => collect($watchers->items())->map(fn ($watcher) => [
                    'id' => $watcher->id,
                    'full_name' => $watcher->user->full_name,
                    'precinct' => $watcher->precinct,
                    'brgy' => $watcher->brgy,
                ]),
                'total_count' => $watchers->total(),
            ]);
        }
        return response()->json(['data' => [], 'total_count' => 0]);
    }

    public function searchCandidates(Request $request)
    {
        $query = \App\PollCandidate::query();
        if ($request->has('q')) {
            $query->where('name', 'LIKE', '%' . $request->q . '%');
        }
        // If election_id and position are present, filter by those as well
        if ($request->filled('election_id') && $request->filled('position')) {
            $query->whereHas('pollElectionCandidates', function($q) use ($request) {
                $q->where('poll_election_id', $request->election_id)
                  ->where('position', $request->position);
            });
        }
        $candidates = $query->paginate(10);
        return response()->json([
            'data' => collect($candidates->items())->map(function ($candidate) {
                return [
                    'id' => $candidate->id,
                    'name' => $candidate->name,
                    'image' => $candidate->image,
                ];
            }),
            'total_count' => $candidates->total(),
        ]);
    }

    public function getFilteredElectionWatchers(Request $request, PollElection $poll_election)
    {
        if ($request->has('q')) {
            $query = $poll_election->pollElectionWatchers()->with('pollWatcher.user');
            $query->whereHas('pollWatcher.user', function ($q) use ($request) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $request->q . '%']);
            });
            $watchers = $query->paginate(10);
            return response()->json([
                'data' => $watchers->map(fn ($watcher) => [
                    'id' => $watcher->id,
                    'full_name' => $watcher->pollWatcher->user->full_name,
                ]),
                'total_count' => $watchers->total(),
            ]);
        }
        return response()->json(['data' => [], 'total_count' => 0]);
    }

    public function addWatcherToElection(Request $request, PollElection $poll_election)
    {
        $validated = $request->validate([
            'poll_watcher_id' => 'required|exists:poll_watchers,id',
        ]);

        // Check if the watcher already exists in the election
        $exists = \App\PollElectionWatcher::where('poll_election_id', $poll_election->id)
            ->where('poll_watcher_id', $validated['poll_watcher_id'])
            ->exists();

        if ($exists) {
            return response()->json(['status' => 0, 'message' => 'Watcher already exists in this election.'], 422);
        }

        $poll_election_watcher = new \App\PollElectionWatcher([
            'poll_election_id' => $poll_election->id,
            'poll_watcher_id' => $validated['poll_watcher_id'],
        ]);

        $poll_election_watcher->save();

        return response()->json(['status' => 1, 'message' => 'Watcher added successfully.']);
    }

    public function searchUsers(Request $request)
    {
        if ($request->has('q')) {
            $query = User::query();
            $query->where('user_type', User::WATCHER)
                ->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $request->q . '%']);
            $users = $query->paginate(10);
            return response()->json([
                'data' => $users->items(),
                'total_count' => $users->total(),
            ]);
        }
        return response()->json(['data' => [], 'total_count' => 0]);
    }

    // Candidate

    public function candidateIndex(Request $request)
    {
        if($request->ajax()){
            $candidate = $this->poll_service::getCandidates($request);
            return response()->json([
                'data'=> $candidate['data'],
                'recordsTotal' => $candidate['count'],
                'recordsFiltered' => $candidate['count'],
                'start' => $request->start,
                'length' => $request->length,
                'draw' => $request->draw,
            ]);
        }else{
            return view('backend.poll.candidates.list');
        }
    }

    public function createCandidate(Request $request)
    { 
        return view('backend.poll.candidates.modal.create');
    }

    public function storeCandidate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|file',
            'remarks' => 'nullable|string',
        ]);

        $model = new PollCandidate;
        $model->name = $request->name;
        $model->remarks = $request->remarks;
        
        if ($request->hasFile('image')){
            $image = $request->file('image');
            $file_name = "candidate_".time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('uploads'), $file_name);
            $model->image = $file_name;
         }

        $model->save();

        return back()->with('success', 'Candidate created successfully.');
    }

    public function editCandidate(Request $request, PollCandidate $poll_candidate)
    {
        return view('backend.poll.candidates.modal.edit', compact('poll_candidate'));
    }

    public function updateCandidate(Request $request, PollCandidate $poll_candidate)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|file',
            'remarks' => 'nullable|string',
        ]);

        $poll_candidate->name = $request->name;
        $poll_candidate->remarks = $request->remarks;
        
        if ($request->hasFile('image')){
            $image = $request->file('image');
            $file_name = "candidate_".time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('uploads'), $file_name);
            $poll_candidate->image = $file_name;
         }

        $poll_candidate->save();

        return back()->with('success', 'Candidate updated successfully.');
    }

    public function deleteCandidate(Request $request, PollCandidate $poll_candidate)
    {
        $poll_candidate->delete();

        return redirect()->route('candidate.index')->with('success', 'Candidate deleted successfully.');
    }

    // Watchers

    public function watcherIndex(Request $request)
    {
        if($request->ajax()){
            $candidate = $this->poll_service::getWatchers($request);
            return response()->json([
                'data'=> $candidate['data'],
                'recordsTotal' => $candidate['count'],
                'recordsFiltered' => $candidate['count'],
                'start' => $request->start,
                'length' => $request->length,
                'draw' => $request->draw,
            ]);
        }else{
            return view('backend.poll.watcher.list');
        }
    }

    public function createWatcher(Request $request)
    {
        $brgys = collect(config('area_barangays'))
            ->flatten()
            ->reject(fn($brgy) => $brgy === '*')
            ->sort()
            ->values()
            ->toArray();

        return view('backend.poll.watcher.modal.create', compact('brgys'));
    }

    public function storeWatcher(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'brgy' => 'nullable|string|max:255',
            'precinct' => 'nullable|string|max:255',
            'poll_place' => 'nullable|string|max:255',
            'clustered_precincts' => 'required|string|max:255',
            'no_of_registered_voters' => 'nullable|string|max:255',
        ]);

        $area_barangays = config('area_barangays');
        $area = collect($area_barangays)->search(fn($barangays) => in_array($validated['brgy'], $barangays));
        $validated['area'] = $area !== false ? $area : null;

        PollWatcher::create($validated);

        return back()->with('success', 'Watcher created successfully.');
    }

    public function editWatcher(Request $request, PollWatcher $poll_watcher)
    {
        $brgys = collect(config('area_barangays'))
            ->flatten()
            ->reject(fn($brgy) => $brgy === '*')
            ->sort()
            ->values()
            ->toArray();

        return view('backend.poll.watcher.modal.edit', compact('poll_watcher', 'brgys'));
    }

    public function updateWatcher(Request $request, PollWatcher $poll_watcher)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'brgy' => 'nullable|string|max:255',
            'precinct' => 'nullable|string|max:255',
            'poll_place' => 'nullable|string|max:255',
            'clustered_precincts' => 'nullable|string|max:255',
            'no_of_registered_voters' => 'nullable|string|max:255',
        ]);

        $area_barangays = config('area_barangays');
        $area = collect($area_barangays)->search(fn($barangays) => in_array($validated['brgy'], $barangays));
        $validated['area'] = $area !== false ? $area : null;

        $poll_watcher->update($validated);

        return back()->with('success', 'Watcher updated successfully.');
    }

    public function deleteWatcher(Request $request, PollWatcher $poll_watcher)
    {
        $poll_watcher->delete();

        return back()->with('success', 'Watcher deleted successfully.');
    }

    // Entries

    public function entriesIndex(Request $request)
    {
        if($request->ajax()){
            $entries = $this->poll_service::getEntries($request);
            return response()->json([
                'data'=> collect($entries['data'])->map(fn ($d) => [
                    "candidate_name" => $d->pollElectionCandidate->pollCandidate->name,
                    "watcher_name" => $d->pollElectionWatcher->pollWatcher->user->full_name,
                    "precinct" => $d->pollElectionWatcher->pollWatcher->precinct,
                    "brgy" => $d->pollElectionWatcher->pollWatcher->brgy,
                    "area" => $d->pollElectionWatcher->pollWatcher->area,
                    "watcher_name" => $d->pollElectionWatcher->pollWatcher->user->full_name,
                    "election_name" => $d->pollElection->name,
                    "votes" => $d->votes,
                    "id" => $d->id,
                    "remarks" => $d->remarks,
                    "created_at" => Carbon::parse($d->created_at)->toDateTimeString(),
                    "updated_at" => Carbon::parse($d->updated_at)->toDateTimeString(),
                ])->toArray(),
                'recordsTotal' => $entries['count'],
                'recordsFiltered' => $entries['count'],
                'start' => $request->start,
                'length' => $request->length,
                'draw' => $request->draw,
            ]);
        }else{
            return view('backend.poll.entry.list');
        }
    }

    public function createEntries(Request $request)
    {
        return view('backend.poll.entry.modal.create');
    }

    public function storeEntries(Request $request)
    {
        $validated = $request->validate([
            'poll_election_candidate_id' => 'required|exists:poll_election_candidates,id',
            'poll_election_watcher_id' => 'required|exists:poll_election_watchers,id',
            'poll_election_id' => 'required|exists:poll_elections,id',
            'votes' => 'required|integer|min:0',
            'remarks' => 'nullable|string',
        ]);

        PollEntry::create($validated);

        return back()->with('success', 'Entry created successfully.');
    }

    public function editEntries(Request $request, PollEntry $poll_entry)
    {
        return view('backend.poll.entry.modal.edit', compact('poll_entry'));
    }

    public function updateEntries(Request $request, PollEntry $poll_entry)
    {
        $validated = $request->validate([
            'poll_election_candidate_id' => 'required|exists:poll_election_candidates,id',
            'poll_election_watcher_id' => 'required|exists:poll_election_watchers,id',
            'poll_election_id' => 'required|exists:poll_elections,id',
            'votes' => 'required|integer|min:0',
            'remarks' => 'nullable|string',
        ]);

        $poll_entry->update($validated);

        return back()->with('success', 'Entry updated successfully.');
    }

    public function deleteEntries(Request $request, PollEntry $poll_entry)
    {
        $poll_entry->delete();

        return back()->with('success', 'Entry deleted successfully.');
    }

    public function bulkDeleteEntries(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:poll_entries,id',
        ]);

        PollEntry::whereIn('id', $validated['ids'])->delete();

        return response()->json(['status' => 1, 'message' => 'Selected entries deleted successfully.']);
    }

    public function showWatcherCandidates(Request $request, PollElection $poll_election, PollWatcher $poll_watcher)
    {
        if(strpos($poll_watcher->clustered_precincts, '-') !== false) {
            $clustered_frag = explode("-", $poll_watcher->clustered_precincts);
            $clustered_precinct_arr = range(intval($clustered_frag[0]), intval($clustered_frag[1]));
        } else {
            $clustered_precinct_arr = [intval($poll_watcher->clustered_precincts)];
        }
        return view("backend.poll.election.modal.show_watcher_candidates", compact("clustered_precinct_arr", "poll_watcher", "poll_election"));
    }

    public function getWatcherCandidates(Request $request, PollElection $poll_election, PollWatcher $poll_watcher)
    {
        $election = $this->poll_service::getElectionCandidatesWithWatcherVotes($request, $poll_election, $poll_watcher);

        return response()->json([
            'data' => $election['data'],
            'recordsTotal' => $election['count'],
            'recordsFiltered' => $election['count'],
            'start' => $request->start,
            'length' => $request->length,
            'draw' => $request->draw,
        ]);
    }

    public function exportWatcherCandidatesCsv(Request $request, PollElection $poll_election, PollWatcher $poll_watcher)
    {
        // Get all data (no pagination)
        $data = app(\App\Services\Poll\PollService::class)->getElectionCandidatesWithWatcherVotesAll($request, $poll_election, $poll_watcher);
        $rows = $data['data'];
        if (empty($rows)) {
            return response('No data to export', 404);
        }
        // Build CSV headers
        $headers = ['#', 'Details'];
        // Get all unique clusters for this watcher
        $clustered_precinct_arr = [];
        if(strpos($poll_watcher->clustered_precincts, '-') !== false) {
            $frag = explode('-', $poll_watcher->clustered_precincts);
            $clustered_precinct_arr = range(intval($frag[0]), intval($frag[1]));
        } else {
            $clustered_precinct_arr = [intval($poll_watcher->clustered_precincts)];
        }
        foreach ($clustered_precinct_arr as $clustered_precinct_number) {
            $headers[] = 'Cluster ' . $clustered_precinct_number;
        }
        $headers[] = 'Total Votes';
        $headers[] = '# of Cluster Entries';
        // Prepare CSV content
        $csv = "\xEF\xBB\xBF"; // UTF-8 BOM for Excel
        $csv .= collect($headers)->map(function($h){ return '"'.str_replace('"','""',$h).'"'; })->implode(',') . "\n";
        foreach ($rows as $i => $row) {
            $line = [];
            $line[] = $i+1;
            $line[] = $row['name'] . ' (' . $row['position'] . ' - ' . $row['party'] . ')';
            $cvotes = $row['clustered_votes'] ? json_decode($row['clustered_votes'], true) : [];
            foreach ($clustered_precinct_arr as $clustered_precinct_number) {
                $line[] = isset($cvotes[$clustered_precinct_number]) ? $cvotes[$clustered_precinct_number] : 0;
            }
            $line[] = $row['total_votes_by_watcher'] ?? 0;
            $line[] = ($row['clustered_vote_entry_count'] ?? 0) . ' / ' . count($clustered_precinct_arr);
            $csv .= collect($line)->map(function($v){ return '"'.str_replace('"','""',$v).'"'; })->implode(',') . "\n";
        }
        $filename = 'watcher_candidates_' . $poll_election->id . '_' . $poll_watcher->id . '_' . date('Ymd_His') . '.csv';
        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function watcherDashboardIndex(Request $request)
    {
        $user = auth()->user();
        $active_election = PollElection::where('is_active', 1)
            ->whereHas('pollElectionWatchers.pollWatcher', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->first();
        $watcher = PollWatcher::where('user_id', $user->id)->first();
        if(strpos($watcher->clustered_precincts, '-') !== false) {
            $clustered_frag = explode("-", $watcher->clustered_precincts);
            $clustered_precinct_arr = range(intval($clustered_frag[0]), intval($clustered_frag[1]));
        } else {
            $clustered_precinct_arr = [intval($watcher->clustered_precincts)];
        }

        return view('guest.poll.dashboard', compact('active_election', 'watcher', 'clustered_precinct_arr'));
    }

    public function getWatcherStats(Request $request)
    {
        $user = auth()->user();
        $active_election = PollElection::where('is_active', 1)
            ->whereHas('pollElectionWatchers.pollWatcher', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->first();
        $watcher = PollWatcher::where('user_id', $user->id)->first();

        if (!$active_election) {
            return response()->json(['status' => 0, 'message' => 'No active election found.'], 404);
        }

        $total_completed_entry_count = $active_election->pollElectionWatchers
            ->where('poll_watcher_id', $watcher->id)
            ->first()
            ->pollEntries()
            ->where('status', 1)
            ->count();

        $total_candidates = $active_election->pollElectionCandidates->count();

        $completion_percentage = $total_candidates > 0 ? round(($total_completed_entry_count / $total_candidates) * 100, 2) : 0;

        return response()->json([
            'status' => 1,
            'message' => 'Watcher stats retrieved successfully.',
            'data' => [
                'total_entry_count' => $total_completed_entry_count,
                'total_candidates' => $total_candidates,
                'completion_percentage' => $completion_percentage,
            ]
        ]);
    }

    public function watcherCandidatesList(Request $request, PollElection $poll_election)
    {
        $user = auth()->user();
        $watcher = PollWatcher::where('user_id', $user->id)->first();

        $election = $this->poll_service::getElectionCandidatesWithWatcherVotes($request, $poll_election, $watcher);

        return response()->json([
            'data' => $election['data'],
            'recordsTotal' => $election['count'],
            'recordsFiltered' => $election['count'],
            'start' => $request->start,
            'length' => $request->length,
            'draw' => $request->draw,
        ]);
    }

    public function upsertVotes(Request $request, PollElection $poll_election, PollElectionCandidate $poll_election_candidate)
    {
        $validated = $request->validate([
            'field' => 'required|string',
            'votes' => 'required|numeric',
            'clustered_precinct_number' => 'required|numeric',
            'remarks' => 'nullable|string',
        ]);

        $user = auth()->user();
        $watcher = PollWatcher::where('user_id', $user->id)->first();

        if (!$watcher) {
            return response()->json(['status' => 0, 'message' => 'Watcher not found.'], 404);
        }

        if($watcher->no_of_registered_voters > 0 && $request->votes > $watcher->no_of_registered_voters) {
            return response()->json(['status' => 0, 'message' => 'Your votes count exceed the number of registered voters.'], 403);
        }

        $poll_election_watcher = $poll_election->pollElectionWatchers()
            ->where('poll_watcher_id', $watcher->id)
            ->first();

        if (!$poll_election_watcher) {
            return response()->json(['status' => 0, 'message' => 'Watcher is not assigned to this election.'], 403);
        }

        $old_entry = PollEntry::where([
            'poll_election_candidate_id' => $poll_election_candidate->id,
            'poll_election_watcher_id' => $poll_election_watcher->id,
            'poll_election_id' => $poll_election->id,
        ])->first();

        $total_votes = 0;
        $clustered_precinct_votes = !empty($old_entry->clustered_precinct_votes) 
            ? json_decode($old_entry->clustered_precinct_votes, true) : [];
        $clustered_precinct_votes[$request->clustered_precinct_number] = $request->votes;
        foreach($clustered_precinct_votes as $key => $votes) {
            $total_votes += intval($votes);
        }

        $new_entry = PollEntry::updateOrCreate([
            'poll_election_candidate_id' => $poll_election_candidate->id,
            'poll_election_watcher_id' => $poll_election_watcher->id,
            'poll_election_id' => $poll_election->id,
        ], [
            'votes' => $total_votes,
            'clustered_precinct_votes' => json_encode($clustered_precinct_votes),
            'remarks' => $request->input('remarks', null),
            'status' => count($clustered_precinct_votes) >= $watcher->clustered_precinct_count ? 1 : 0,
        ]);

        // Log the activity
        PollActivityLog::create([
            'action' => $old_entry ? 'update' : 'create',
            'description' => $old_entry 
                ? 'Updated votes from ' . $old_entry->votes . ' to ' . $new_entry->votes 
                : 'Created new entry with votes: ' . $new_entry->votes,
            'poll_watcher_id' => $watcher->id,
            'poll_entry_id' => $new_entry->id,
            'poll_election_id' => $poll_election->id,
            'ip_address' => $request->ip(),
        ]);

        return response()->json(['status' => 1, 'message' => 'Votes added successfully.']);
    }

    public function getElectionLeaderboard(Request $request, PollElection $poll_election)
    {
        $position = $request->input('position', null);
        $leaderboard = $this->poll_service::getElectionCandidateLeaderboard($poll_election, $position);
        return response()->json($leaderboard);
    }

    public function overviewIndex(Request $request)
    {
        $activeElection = PollElection::where('is_active', 1)->first();
        $position = $request->input('position', 'Congressman');

        if (!$activeElection) {
            return view('backend.poll.overview', [
                'activeElection' => null,
                'pollVotesData' => [],
                'positionRaceData' => [],
                'totalVotes' => 0,
                'totalRegisteredVoters' => 0,
            ]);
        }

        // Poll Votes Per Barangay
        $pollVotesData = PollWatcher::select('brgy', DB::raw('COALESCE(SUM(poll_entries.votes), 0) as total_votes'), DB::raw('SUM(no_of_registered_voters) as no_of_registered_voters'))
            ->leftJoin('poll_election_watchers', 'poll_watchers.id', '=', 'poll_election_watchers.poll_watcher_id')
            ->leftJoin('poll_entries', function ($join) use ($activeElection, $position) {
                $join->on('poll_election_watchers.id', '=', 'poll_entries.poll_election_watcher_id')
                    ->leftJoin('poll_election_candidates', 'poll_entries.poll_election_candidate_id', '=', 'poll_election_candidates.id')
                    ->where('poll_election_watchers.poll_election_id', $activeElection->id)
                    ->where('poll_election_candidates.position', $position);
            })
            ->groupBy('brgy')
            ->get()
            ->map(function ($item) {
                return [
                    'brgy' => $item->brgy,
                    'total_votes' => intval($item->total_votes),
                    'no_of_registered_voters' => intval($item->no_of_registered_voters),
                ];
            });

        // Election Race Per Position
        $positionOrder = ['Congressman', 'Councilor', 'Mayor', 'Vice Mayor'];
        $positionRaceData = $activeElection->pollElectionCandidates()
            ->with(['pollCandidate:id,name,image', 'pollEntries'])
            ->get()
            ->groupBy('position')
            ->sortBy(function ($_, $key) use ($positionOrder) {
                return array_search($key, $positionOrder) !== false ? array_search($key, $positionOrder) : PHP_INT_MAX;
            })
            ->map(function ($candidates) use ($activeElection) {
                return $candidates->map(function ($candidate) use ($activeElection) {
                    $totalUniqueWatchers = $activeElection->pollElectionWatchers->pluck('poll_watcher_id')->unique()->count();
                    $uniqueWatchersWithEntries = $candidate->pollEntries
                        ->where('status', 1)
                        ->pluck('pollElectionWatcher.poll_watcher_id')
                        ->unique()
                        ->count();
                    $completionPercentage = $totalUniqueWatchers > 0 ? round(($uniqueWatchersWithEntries / $totalUniqueWatchers) * 100, 2) : 0;

                    return [
                        'id' => $candidate->id,
                        'name' => $candidate->pollCandidate->name ?? 'N/A',
                        'image' => $candidate->pollCandidate->image ? asset('uploads/' . $candidate->pollCandidate->image) : asset('images/avatar-classic.png'),
                        'votes' => $candidate->pollEntries->sum('votes'),
                        'completion_percentage' => $completionPercentage,
                        'party' => $candidate->party,
                        'party_list' => $candidate->party_list,
                        'national_party' => $candidate->national_party,
                    ];
                })->sortByDesc('votes')->take(10)->values();
            });

        $totalVotes = $pollVotesData->sum('total_votes');
        $totalRegisteredVoters = VoterTagDetail::count();
        
        return view('backend.poll.overview', [
            'activeElection' => $activeElection,
            'pollVotesData' => $pollVotesData,
            'positionRaceData' => $positionRaceData,
            'totalVotes' => $totalVotes,
            'totalRegisteredVoters' => $totalRegisteredVoters,
        ]);
    }

    public function refreshOverviewData(Request $request)
    {
        $activeElection = PollElection::where('is_active', 1)->first();
        $position = $request->input('position', 'Congressman');

        if (!$activeElection) {
            return response()->json([
                'pollVotesData' => [],
                'positionRaceData' => [],
            ]);
        }

        // Poll Votes Per Barangay
        $pollVotesData = PollWatcher::select('brgy', DB::raw('COALESCE(SUM(poll_entries.votes), 0) as total_votes'), DB::raw('SUM(no_of_registered_voters) as no_of_registered_voters'))
            ->leftJoin('poll_election_watchers', 'poll_watchers.id', '=', 'poll_election_watchers.poll_watcher_id')
            ->leftJoin('poll_entries', function ($join) use ($activeElection, $position) {
                $join->on('poll_election_watchers.id', '=', 'poll_entries.poll_election_watcher_id')
                    ->leftJoin('poll_election_candidates', 'poll_entries.poll_election_candidate_id', '=', 'poll_election_candidates.id')
                    ->where('poll_election_watchers.poll_election_id', $activeElection->id)
                    ->where('poll_election_candidates.position', $position);
            })
            ->groupBy('brgy')
            ->get()
            ->map(function ($item) {
                return [
                    'brgy' => $item->brgy,
                    'total_votes' => intval($item->total_votes),
                    'no_of_registered_voters' => intval($item->no_of_registered_voters),
                ];
            });

        // Election Race Per Position
        $positionOrder = ['Congressman', 'Councilor', 'Mayor', 'Vice Mayor'];
        $positionRaceData = $activeElection->pollElectionCandidates()
            ->with(['pollCandidate:id,name,image', 'pollEntries'])
            ->get()
            ->groupBy('position')
            ->sortBy(function ($_, $key) use ($positionOrder) {
                return array_search($key, $positionOrder) !== false ? array_search($key, $positionOrder) : PHP_INT_MAX;
            })
            ->map(function ($candidates) use ($activeElection) {
                return $candidates->map(function ($candidate) use ($activeElection) {
                    $totalUniqueWatchers = $activeElection->pollElectionWatchers->pluck('poll_watcher_id')->unique()->count();
                    $uniqueWatchersWithEntries = $candidate->pollEntries
                        ->where('status', 1)
                        ->pluck('pollElectionWatcher.poll_watcher_id')
                        ->unique()
                        ->count();
                    $completionPercentage = $totalUniqueWatchers > 0 ? round(($uniqueWatchersWithEntries / $totalUniqueWatchers) * 100, 2) : 0;

                    return [
                        'id' => $candidate->id,
                        'name' => $candidate->pollCandidate->name ?? 'N/A',
                        'image' => $candidate->pollCandidate->image ? asset('uploads/' . $candidate->pollCandidate->image) : asset('images/avatar-classic.png'),
                        'votes' => $candidate->pollEntries->sum('votes'),
                        'completion_percentage' => $completionPercentage,
                        'party' => $candidate->party,
                        'party_list' => $candidate->party_list,
                        'national_party' => $candidate->national_party,
                    ];
                })->sortByDesc('votes')->take(10)->values();
            });

        $totalVotes = $pollVotesData->sum('total_votes');
        $totalRegisteredVoters = VoterTagDetail::count();

        return response()->json([
            'pollVotesData' => $pollVotesData,
            'positionRaceData' => $positionRaceData,
            'totalVotes' => $totalVotes,
            'totalRegisteredVoters' => $totalRegisteredVoters,
        ]);
    }

    public function showWatcherActivityLog(Request $request)
    {
        return view("guest.poll.modal.entry_history");
    }

    public function getWatcherActivityLogs(Request $request)
    {
        $user = auth()->user();
        $watcher = PollWatcher::where('user_id', $user->id)->first();

        if (!$watcher) {
            return response()->json(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
        }

        $active_election = $watcher->getActiveElection();

        $logs = PollActivityLog::with(["pollEntry.pollElectionCandidate.pollCandidate"])
            ->where('poll_watcher_id', $watcher->id)
            ->where('poll_election_id', $active_election->id)
            ->orderBy('created_at', 'DESC');

        $total_count = $logs->count();
        $logs = $logs->offset($request->start)
            ->limit($request->length)
            ->get()
            ->map(fn ($log) => [
                "id" => $log->id,
                "action" => $log->action,
                "description" => $log->description,
                "candidate" => $log->pollEntry->pollElectionCandidate->pollCandidate->name ?? 'N/A',
                "created_at" => $log->created_at->format('Y-m-d H:i:s'),
            ]);

        return response()->json([
            'data' => $logs,
            'recordsTotal' => $total_count,
            'recordsFiltered' => $total_count,
            'start' => $request->start,
            'length' => $request->length,
            'draw' => $request->draw,
        ]);
    }

    public function showWatcherActivityLogFromAdmin(Request $request, PollElection $poll_election, PollWatcher $poll_watcher)
    {
        $poll_watcher = $poll_watcher->load("user");
        return view("backend.poll.election.modal.watcher_entry_history", compact('poll_election', 'poll_watcher'));
    }

    public function getWatcherActivityLogFromAdmin(Request $request, PollElection $poll_election, PollWatcher $poll_watcher)
    {
        if (!$poll_watcher) {
            return response()->json(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
        }

        $logs = PollActivityLog::with(["pollEntry.pollElectionCandidate.pollCandidate"])
            ->where('poll_watcher_id', $poll_watcher->id)
            ->where('poll_election_id', $poll_election->id)
            ->orderBy('created_at', 'DESC');

        $total_count = $logs->count();
        $logs = $logs->offset($request->start)
            ->limit($request->length)
            ->get()
            ->map(fn ($log) => [
                "id" => $log->id,
                "action" => $log->action,
                "description" => $log->description,
                "candidate" => $log->pollEntry->pollElectionCandidate->pollCandidate->name ?? 'N/A',
                "created_at" => $log->created_at->format('Y-m-d H:i:s'),
            ]);

        return response()->json([
            'data' => $logs,
            'recordsTotal' => $total_count,
            'recordsFiltered' => $total_count,
            'start' => $request->start,
            'length' => $request->length,
            'draw' => $request->draw,
        ]);
    }

    public function exportVotesBreakdown(PollElectionCandidate $poll_election_candidate)
    {
        $poll_election_candidate = $poll_election_candidate->load(['pollEntries.pollElectionWatcher.pollWatcher', 'pollElection']);

        // Get all candidates for this election/position
        $all_candidates = $poll_election_candidate->pollElection->pollElectionCandidates()
            ->where('position', $poll_election_candidate->position)
            ->with(['pollEntries.pollElectionWatcher.pollWatcher'])
            ->get();

        // Calculate votes per barangay for this candidate
        $votes_per_brgy = $poll_election_candidate->pollEntries
            ->sortBy(fn($entry) => $entry->pollElectionWatcher->pollWatcher->id)
            ->groupBy('pollElectionWatcher.pollWatcher.brgy')
            ->map(function ($entries, $brgy) {
                $votes = $entries->sum('votes');
                $total_registered_voters = $entries->first()->pollElectionWatcher->pollWatcher->no_of_registered_voters ?? 0;
                $vote_percentage = $total_registered_voters > 0 ? round(($votes / $total_registered_voters) * 100, 2) : 0;
                return [
                    'barangay' => $brgy,
                    'votes' => $votes,
                    'vote_percentage' => $vote_percentage,
                    'total_registered_voters' => $total_registered_voters,
                ];
            });

        // Calculate rank per barangay
        $barangay_ranks = [];
        $barangays = $votes_per_brgy->keys();
        foreach ($barangays as $brgy) {
            $votes_in_brgy = $all_candidates->mapWithKeys(function($c) use ($brgy) {
                $votes = $c->pollEntries->filter(function($entry) use ($brgy) {
                    return $entry->pollElectionWatcher && $entry->pollElectionWatcher->pollWatcher && $entry->pollElectionWatcher->pollWatcher->brgy == $brgy;
                })->sum('votes');
                return [$c->id => $votes];
            });
            $sorted_brgy = collect($votes_in_brgy)->sortByDesc(function($votes, $cid) { return $votes; });
            $brgy_rank = 1;
            foreach ($sorted_brgy as $cid => $votes) {
                if ($cid == $poll_election_candidate->id) {
                    $barangay_ranks[$brgy] = $brgy_rank;
                    break;
                }
                $brgy_rank++;
            }
        }

        $csv_data = [
            ['Barangay', 'Votes', 'Vote Percentage', '# of Registered Voters', 'Rank']
        ];

        foreach ($votes_per_brgy as $brgy => $data) {
            $csv_data[] = [
                $brgy,
                $data['votes'],
                $data['vote_percentage'] . '%',
                $data['total_registered_voters'],
                $barangay_ranks[$brgy] ?? '-',
            ];
        }

        $filename = 'votes_breakdown_' . $poll_election_candidate->id . '.csv';
        $handle = fopen($filename, 'w');
        foreach ($csv_data as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return response()->download($filename)->deleteFileAfterSend(true);
    }

    public function exportVotesBreakdownCluster(PollElectionCandidate $poll_election_candidate)
    {
        $poll_election_candidate = $poll_election_candidate->load(['pollEntries.pollElectionWatcher.pollWatcher', 'pollElection']);

        // Get all candidates for this election/position
        $all_candidates = $poll_election_candidate->pollElection->pollElectionCandidates()
            ->where('position', $poll_election_candidate->position)
            ->with(['pollEntries.pollElectionWatcher.pollWatcher'])
            ->get();

        // Prepare cluster breakdown data, order by cluster
        $cluster_rows = [];
        foreach ($poll_election_candidate->pollEntries as $entry) {
            $watcher = $entry->pollElectionWatcher->pollWatcher ?? null;
            $brgy = $watcher ? $watcher->brgy : '';
            $watcher_name = $watcher && $watcher->user ? $watcher->user->full_name : '';
            $clustered_votes = !empty($entry->clustered_precinct_votes) ? json_decode($entry->clustered_precinct_votes, true) : [];
            foreach ($clustered_votes as $cluster => $votes) {
                $votes_num = is_numeric($votes) ? (int)$votes : 0;
                $cluster_rows[] = [
                    'cluster' => $cluster,
                    'watcher' => $watcher_name,
                    'brgy' => $brgy,
                    'votes' => $votes_num,
                ];
            }
        }
        usort($cluster_rows, function($a, $b) {
            return intval($a['cluster']) <=> intval($b['cluster']);
        });

        // Calculate rank per cluster
        $cluster_ranks = [];
        $all_clusters = collect($cluster_rows)->pluck('cluster')->unique()->sort()->values();
        foreach ($all_clusters as $cluster_num) {
            $votes_in_cluster = $all_candidates->mapWithKeys(function($c) use ($cluster_num) {
                $votes = 0;
                foreach ($c->pollEntries as $entry) {
                    if (!empty($entry->clustered_precinct_votes)) {
                        $json = json_decode($entry->clustered_precinct_votes, true);
                        if (is_array($json) && isset($json[$cluster_num])) {
                            $votes += intval($json[$cluster_num]);
                        }
                    }
                }
                return [$c->id => $votes];
            });
            $sorted_cluster = collect($votes_in_cluster)->sortByDesc(function($votes, $cid) { return $votes; });
            $cluster_rank = 1;
            foreach ($sorted_cluster as $cid => $votes) {
                if ($cid == $poll_election_candidate->id) {
                    $cluster_ranks[$cluster_num] = $cluster_rank;
                    break;
                }
                $cluster_rank++;
            }
        }

        $csv_data = [
            ['Cluster', 'Watcher', 'Barangay', 'Votes', 'Rank']
        ];

        foreach ($cluster_rows as $row) {
            $csv_data[] = [
                $row['cluster'],
                $row['watcher'],
                $row['brgy'],
                $row['votes'],
                $cluster_ranks[$row['cluster']] ?? '-',
            ];
        }

        $filename = 'votes_breakdown_cluster_' . $poll_election_candidate->id . '.csv';
        $handle = fopen($filename, 'w');
        foreach ($csv_data as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return response()->download($filename)->deleteFileAfterSend(true);
    }

    // Candidates Per Barangay Data Endpoint
    public function candidatesPerBrgyData(Request $request)
    {
        $election_id = $request->get('election_id');
        $position = $request->get('position');
        if (!$election_id || !$position) {
            return response()->json([]);
        }
        $election = PollElection::find($election_id);
        if (!$election) {
            return response()->json([]);
        }
        $barangays = PollWatcher::whereHas('pollElectionWatchers', function($q) use ($election_id) {
            $q->where('poll_election_id', $election_id);
        })->pluck('brgy')->unique()->values();
        $candidates = PollElectionCandidate::where('poll_election_id', $election_id)
            ->where('position', $position)
            ->with(['pollCandidate'])
            ->get();
        $candidate_totals = [];
        foreach ($candidates as $candidate) {
            $total_votes = 0;
            $entries = PollEntry::where('poll_election_candidate_id', $candidate->id)->get();
            foreach ($entries as $entry) {
                $total_votes += intval($entry->votes);
            }
            $candidate_totals[$candidate->id] = $total_votes;
        }
        $sorted_candidates = $candidates->sortByDesc(function($c) use ($candidate_totals) {
            return $candidate_totals[$c->id] ?? 0;
        })->values();
        // Color palette (colorblind-friendly)
        $palette = [
            '#4e79a7', '#f28e2b', '#e15759', '#76b7b2', '#59a14f', '#edc949',
            '#af7aa1', '#ff9da7', '#9c755f', '#bab0ab', '#86bcb6', '#b07aa1'
        ];
        $master_candidates = $sorted_candidates->values()->map(function($c, $idx) use ($palette) {
            $color = $palette[$idx % count($palette)];
            // Special color for ATAYDE
            if (stripos($c->pollCandidate->name ?? '', 'ATAYDE') !== false) {
                $color = '#3d9970';
            }
            return [
                'id' => $c->id,
                'name' => $c->pollCandidate->name ?? '',
                'party' => $c->party ?? '',
                'party_list' => $c->party_list ?? '',
                'color' => $color
            ];
        })->toArray();
        $result = [];
        foreach ($barangays as $brgy) {
            $candidate_votes = [];
            foreach ($sorted_candidates as $idx => $candidate) {
                $votes = \App\PollEntry::where('poll_election_candidate_id', $candidate->id)
                    ->whereHas('pollElectionWatcher.pollWatcher', function($q) use ($brgy) {
                        $q->where('brgy', $brgy);
                    })->sum('votes');
                $color = $master_candidates[$idx]['color'];
                $candidate_votes[] = [
                    'id' => $candidate->id,
                    'name' => $candidate->pollCandidate->name ?? '',
                    'party' => $candidate->party ?? '',
                    'party_list' => $candidate->party_list ?? '',
                    'votes' => $votes,
                    'color' => $color
                ];
            }
            $result[] = [
                'id' => 'brgy_' . $brgy,
                'name' => $brgy,
                'candidates' => $candidate_votes
            ];
        }
        return response()->json(['master_candidates' => $master_candidates, 'data' => $result]);
    }

    // Candidates Per Cluster Data Endpoint
    public function candidatesPerClusterData(Request $request)
    {
        $election_id = $request->get('election_id');
        $position = $request->get('position');
        if (!$election_id || !$position) {
            return response()->json([]);
        }
        $election = PollElection::find($election_id);
        if (!$election) {
            return response()->json([]);
        }
        $watchers = PollWatcher::whereHas('pollElectionWatchers', function($q) use ($election_id) {
            $q->where('poll_election_id', $election_id);
        })->get();
        $cluster_map = [];
        foreach ($watchers as $watcher) {
            if ($watcher->clustered_precincts) {
                if (strpos($watcher->clustered_precincts, '-') !== false) {
                    $frag = explode('-', $watcher->clustered_precincts);
                    foreach (range(intval($frag[0]), intval($frag[1])) as $cnum) {
                        $cluster_map[$cnum] = $watcher->brgy;
                    }
                } else {
                    $cluster_map[intval($watcher->clustered_precincts)] = $watcher->brgy;
                }
            }
        }
        ksort($cluster_map, SORT_NUMERIC);
        $candidates = PollElectionCandidate::where('poll_election_id', $election_id)
            ->where('position', $position)
            ->with(['pollCandidate'])
            ->get();
        $candidate_totals = [];
        foreach ($candidates as $candidate) {
            $total_votes = 0;
            $entries = PollEntry::where('poll_election_candidate_id', $candidate->id)->get();
            foreach ($entries as $entry) {
                if (!empty($entry->clustered_precinct_votes)) {
                    $json = json_decode($entry->clustered_precinct_votes, true);
                    if (is_array($json)) {
                        foreach ($json as $votes) {
                            $total_votes += intval($votes);
                        }
                    }
                }
            }
            $candidate_totals[$candidate->id] = $total_votes;
        }
        $sorted_candidates = $candidates->sortByDesc(function($c) use ($candidate_totals) {
            return $candidate_totals[$c->id] ?? 0;
        })->values();
        $palette = [
            '#4e79a7', '#f28e2b', '#e15759', '#76b7b2', '#59a14f', '#edc949',
            '#af7aa1', '#ff9da7', '#9c755f', '#bab0ab', '#86bcb6', '#b07aa1'
        ];
        $master_candidates = $sorted_candidates->values()->map(function($c, $idx) use ($palette) {
            $color = $palette[$idx % count($palette)];
            if (stripos($c->pollCandidate->name ?? '', 'ATAYDE') !== false) {
                $color = '#3d9970';
            }
            return [
                'id' => $c->id,
                'name' => $c->pollCandidate->name ?? '',
                'party' => $c->party ?? '',
                'party_list' => $c->party_list ?? '',
                'color' => $color
            ];
        })->toArray();
        $result = [];
        foreach ($cluster_map as $cluster_num => $brgy) {
            $candidate_votes = [];
            foreach ($sorted_candidates as $idx => $candidate) {
                $votes = 0;
                $entries = PollEntry::where('poll_election_candidate_id', $candidate->id)->get();
                foreach ($entries as $entry) {
                    if (!empty($entry->clustered_precinct_votes)) {
                        $json = json_decode($entry->clustered_precinct_votes, true);
                        if (is_array($json) && isset($json[$cluster_num])) {
                            $votes += intval($json[$cluster_num]);
                        }
                    }
                }
                $color = $master_candidates[$idx]['color'];
                $candidate_votes[] = [
                    'id' => $candidate->id,
                    'name' => $candidate->pollCandidate->name ?? '',
                    'party' => $candidate->party ?? '',
                    'party_list' => $candidate->party_list ?? '',
                    'votes' => $votes,
                    'color' => $color
                ];
            }
            $result[] = [
                'id' => 'cluster_' . $cluster_num,
                'name' => $cluster_num,
                'brgy' => $brgy,
                'candidates' => $candidate_votes
            ];
        }
        return response()->json(['master_candidates' => $master_candidates, 'data' => $result]);
    }

    // Candidates Per Cluster Group Data Endpoint (for graph mode)
    public function candidatesPerClusterGroupData(Request $request)
    {
        $election_id = $request->get('election_id');
        $position = $request->get('position');
        if (!$election_id || !$position) {
            return response()->json([]);
        }
        $election = \App\PollElection::find($election_id);
        if (!$election) {
            return response()->json([]);
        }
        $watchers = \App\PollWatcher::whereHas('pollElectionWatchers', function($q) use ($election_id) {
            $q->where('poll_election_id', $election_id);
        })->get();
        $group_map = [];
        foreach ($watchers as $watcher) {
            if ($watcher->clustered_precincts) {
                $group_map[$watcher->clustered_precincts] = $watcher->brgy;
            }
        }
        ksort($group_map, SORT_NATURAL);
        $candidates = \App\PollElectionCandidate::where('poll_election_id', $election_id)
            ->where('position', $position)
            ->with(['pollCandidate'])
            ->get();
        $candidate_totals = [];
        foreach ($candidates as $candidate) {
            $total_votes = 0;
            $entries = \App\PollEntry::where('poll_election_candidate_id', $candidate->id)->get();
            foreach ($entries as $entry) {
                if (!empty($entry->clustered_precinct_votes)) {
                    $json = json_decode($entry->clustered_precinct_votes, true);
                    if (is_array($json)) {
                        foreach ($json as $votes) {
                            $total_votes += intval($votes);
                        }
                    }
                }
            }
            $candidate_totals[$candidate->id] = $total_votes;
        }
        $sorted_candidates = $candidates->sortByDesc(function($c) use ($candidate_totals) {
            return $candidate_totals[$c->id] ?? 0;
        })->values();
        $palette = [
            '#4e79a7', '#f28e2b', '#e15759', '#76b7b2', '#59a14f', '#edc949',
            '#af7aa1', '#ff9da7', '#9c755f', '#bab0ab', '#86bcb6', '#b07aa1'
        ];
        $master_candidates = $sorted_candidates->values()->map(function($c, $idx) use ($palette) {
            $color = $palette[$idx % count($palette)];
            if (stripos($c->pollCandidate->name ?? '', 'ATAYDE') !== false) {
                $color = '#3d9970';
            }
            return [
                'id' => $c->id,
                'name' => $c->pollCandidate->name ?? '',
                'party' => $c->party ?? '',
                'party_list' => $c->party_list ?? '',
                'color' => $color
            ];
        })->toArray();
        $result = [];
        foreach ($group_map as $cluster_group => $brgy) {
            $candidate_votes = [];
            foreach ($sorted_candidates as $idx => $candidate) {
                $votes = 0;
                $entries = \App\PollEntry::where('poll_election_candidate_id', $candidate->id)->get();
                foreach ($entries as $entry) {
                    if (!empty($entry->clustered_precinct_votes)) {
                        $json = json_decode($entry->clustered_precinct_votes, true);
                        if (is_array($json) && isset($json[$cluster_group])) {
                            $votes += intval($json[$cluster_group]);
                        }
                    }
                }
                $color = $master_candidates[$idx]['color'];
                $candidate_votes[] = [
                    'id' => $candidate->id,
                    'name' => $candidate->pollCandidate->name ?? '',
                    'party' => $candidate->party ?? '',
                    'party_list' => $candidate->party_list ?? '',
                    'votes' => $votes,
                    'color' => $color
                ];
            }
            $result[] = [
                'id' => 'group_' . $cluster_group,
                'name' => $cluster_group,
                'brgy' => $brgy,
                'candidates' => $candidate_votes
            ];
        }
        return response()->json(['master_candidates' => $master_candidates, 'data' => $result]);
    }

    // Candidate Comparison Report
    public function candidateComparisonData(Request $request)
    {
        $election_id = $request->get('election_id');
        $position = $request->get('position');
        $candidate1_id = $request->get('candidate1_id');
        $candidate2_id = $request->get('candidate2_id');
        if (!$election_id || !$position || !$candidate1_id || !$candidate2_id || $candidate1_id == $candidate2_id) {
            return response()->json(['error' => 'Invalid parameters.'], 422);
        }

        // Get all candidates for ranking and percentage
        $candidates = \App\PollElectionCandidate::where('poll_election_id', $election_id)
            ->where('position', $position)
            ->with(['pollCandidate', 'pollEntries'])
            ->get();

        // Calculate votes and sort for ranking
        $candidates = $candidates->map(function($c) {
            $c->votes = $c->pollEntries->sum('votes');
            return $c;
        })->sortByDesc('votes')->values();

        // Assign rank
        foreach ($candidates as $i => $c) {
            $c->rank = $i + 1;
        }
        $total_votes = $candidates->sum('votes');

        // Find the two selected candidates and build their data in the order requested
        $selected = collect([$candidate1_id, $candidate2_id])->map(function($cid) use ($candidates, $total_votes) {
            $c = $candidates->first(fn($c) => $c->poll_candidate_id == $cid);
            if (!$c) return null;
            return [
                'id' => $c->id,
                'name' => $c->pollCandidate->name ?? '',
                'image' => $c->pollCandidate->image ? asset('uploads/'.$c->pollCandidate->image) : asset('images/avatar-classic.png'),
                'party' => $c->party ?? '',
                'party_list' => $c->party_list ?? '',
                'votes' => $c->votes,
                'vote_percentage' => $total_votes > 0 ? round(($c->votes / $total_votes) * 100, 2) : 0,
                'rank' => $c->rank,
            ];
        })->filter()->values();

        if ($selected->count() < 2) {
            return response()->json(['error' => 'Candidates not found.'], 404);
        }
        $candidateData = $selected;

        // Section 2: Votes per Barangay
        $brgys = \App\PollWatcher::whereHas('pollElectionWatchers', function($q) use ($election_id) {
            $q->where('poll_election_id', $election_id);
        })->pluck('brgy')->unique()->values();
        $votes_per_brgy = [];
        foreach ($brgys as $brgy) {
            $c1_votes = \App\PollEntry::whereHas('pollElectionCandidate.pollCandidate', fn($q) => $q->where("id", $candidate1_id))
                ->whereHas('pollElectionWatcher.pollWatcher', function($q) use ($brgy) {
                    $q->where('brgy', $brgy);
                })->sum('votes');
            $c2_votes = \App\PollEntry::whereHas('pollElectionCandidate.pollCandidate', fn($q) => $q->where("id", $candidate2_id))
                ->whereHas('pollElectionWatcher.pollWatcher', function($q) use ($brgy) {
                    $q->where('brgy', $brgy);
                })->sum('votes');
            $votes_per_brgy[] = [
                'brgy' => $brgy,
                'c1_votes' => $c1_votes,
                'c2_votes' => $c2_votes,
            ];
        }

        // Section 3: Votes per Cluster (parse clustered_precinct_votes JSON, order by cluster # asc)
        $clusters = [];
        // Get all entries for both candidates
        $entries1 = \App\PollEntry::whereHas('pollElectionCandidate.pollCandidate', fn($q) => $q->where("id", $candidate1_id))->get();
        $entries2 = \App\PollEntry::whereHas('pollElectionCandidate.pollCandidate', fn($q) => $q->where("id", $candidate2_id))->get();
        $cluster_votes = [];
        // Parse candidate 1 entries
        foreach ($entries1 as $entry) {
            if (!empty($entry->clustered_precinct_votes)) {
                $json = json_decode($entry->clustered_precinct_votes, true);
                if (is_array($json)) {
                    foreach ($json as $cluster => $votes) {
                        $cluster_votes[$cluster]['c1_votes'] = ($cluster_votes[$cluster]['c1_votes'] ?? 0) + intval($votes);
                    }
                }
            }
        }
        // Parse candidate 2 entries
        foreach ($entries2 as $entry) {
            if (!empty($entry->clustered_precinct_votes)) {
                $json = json_decode($entry->clustered_precinct_votes, true);
                if (is_array($json)) {
                    foreach ($json as $cluster => $votes) {
                        $cluster_votes[$cluster]['c2_votes'] = ($cluster_votes[$cluster]['c2_votes'] ?? 0) + intval($votes);
                    }
                }
            }
        }
        // Build ordered result
        $votes_per_cluster = [];
        $all_clusters = array_keys($cluster_votes);
        sort($all_clusters, SORT_NUMERIC);
        foreach ($all_clusters as $cluster) {
            $votes_per_cluster[] = [
                'cluster' => $cluster,
                'c1_votes' => $cluster_votes[$cluster]['c1_votes'] ?? 0,
                'c2_votes' => $cluster_votes[$cluster]['c2_votes'] ?? 0,
            ];
        }

        return response()->json([
            'candidates' => $candidateData,
            'votes_per_brgy' => $votes_per_brgy,
            'votes_per_cluster' => $votes_per_cluster,
        ]);
    }

    // Candidate Ranking Report: By Barangay
    public function candidateRankingByBrgy(Request $request)
    {
        $election_id = $request->get('election_id');
        $position = $request->get('position');
        $candidate_id = $request->get('candidate_id');
        if (!$election_id || !$position || !$candidate_id) {
            return response()->json([]);
        }
        $election = \App\PollElection::find($election_id);
        if (!$election) {
            return response()->json([]);
        }
        $barangays = \App\PollWatcher::whereHas('pollElectionWatchers', function($q) use ($election_id) {
            $q->where('poll_election_id', $election_id);
        })->pluck('brgy')->unique()->values();
        $candidates = \App\PollElectionCandidate::where('poll_election_id', $election_id)
            ->where('position', $position)
            ->with(['pollCandidate'])
            ->get();
        // Calculate total votes per candidate for ranking
        $candidate_totals = [];
        foreach ($candidates as $candidate) {
            $total_votes = \App\PollEntry::where('poll_election_candidate_id', $candidate->id)->sum('votes');
            $candidate_totals[$candidate->id] = $total_votes;
        }
        // Sort and assign rank
        $sorted = collect($candidate_totals)->sortByDesc(function($votes, $cid) { return $votes; });
        $ranks = [];
        $rank = 1;
        foreach ($sorted as $cid => $votes) {
            $ranks[$cid] = $rank++;
        }
        // For each barangay, get the selected candidate's votes and rank
        $result = [];
        foreach ($barangays as $brgy) {
            // Get all candidates' votes in this barangay
            $votes_in_brgy = [];
            foreach ($candidates as $candidate) {
                $votes = \App\PollEntry::where('poll_election_candidate_id', $candidate->id)
                    ->whereHas('pollElectionWatcher.pollWatcher', function($q) use ($brgy) {
                        $q->where('brgy', $brgy);
                    })->sum('votes');
                $votes_in_brgy[$candidate->id] = $votes;
            }
            // Sort for rank in this barangay
            $sorted_brgy = collect($votes_in_brgy)->sortByDesc(function($votes, $cid) { return $votes; });
            $brgy_ranks = [];
            $brgy_rank = 1;
            foreach ($sorted_brgy as $cid => $votes) {
                $brgy_ranks[$cid] = $brgy_rank++;
            }
            // Only include the selected candidate
            $selected = $candidates->firstWhere('poll_candidate_id', $candidate_id);
            if ($selected) {
                $result[] = [
                    'candidate_name' => $selected->pollCandidate->name ?? '',
                    'area_name' => $brgy,
                    'rank' => $brgy_ranks[$selected->id] ?? null,
                    'total_votes' => $votes_in_brgy[$selected->id] ?? 0,
                ];
            }
        }
        return response()->json($result);
    }

    // Candidate Ranking Report: By Cluster
    public function candidateRankingByCluster(Request $request)
    {
        $election_id = $request->get('election_id');
        $position = $request->get('position');
        $candidate_id = $request->get('candidate_id');
        if (!$election_id || !$position || !$candidate_id) {
            return response()->json([]);
        }
        $election = \App\PollElection::find($election_id);
        if (!$election) {
            return response()->json([]);
        }
        // Get all clusters from all watchers in this election, and map cluster to barangay
        $watchers = \App\PollWatcher::whereHas('pollElectionWatchers', function($q) use ($election_id) {
            $q->where('poll_election_id', $election_id);
        })->get();
        $cluster_barangay_map = [];
        $clusters = collect();
        foreach ($watchers as $watcher) {
            if ($watcher->clustered_precincts) {
                $parts = explode('-', $watcher->clustered_precincts);
                if (count($parts) == 2) {
                    for ($i = intval($parts[0]); $i <= intval($parts[1]); $i++) {
                        $clusters->push($i);
                        $cluster_barangay_map[$i] = $watcher->brgy;
                    }
                } else {
                    $clusters->push(intval($watcher->clustered_precincts));
                    $cluster_barangay_map[intval($watcher->clustered_precincts)] = $watcher->brgy;
                }
            }
        }
        $clusters = $clusters->unique()->sort()->values();
        $candidates = \App\PollElectionCandidate::where('poll_election_id', $election_id)
            ->where('position', $position)
            ->with(['pollCandidate'])
            ->get();
        // Calculate total votes per candidate for ranking
        $candidate_totals = [];
        foreach ($candidates as $candidate) {
            $total_votes = 0;
            $entries = \App\PollEntry::where('poll_election_candidate_id', $candidate->id)->get();
            foreach ($entries as $entry) {
                if (!empty($entry->clustered_precinct_votes)) {
                    $json = json_decode($entry->clustered_precinct_votes, true);
                    if (is_array($json)) {
                        foreach ($json as $votes) {
                            $total_votes += intval($votes);
                        }
                    }
                }
            }
            $candidate_totals[$candidate->id] = $total_votes;
        }
        // Sort and assign rank
        $sorted = collect($candidate_totals)->sortByDesc(function($votes, $cid) { return $votes; });
        $ranks = [];
        $rank = 1;
        foreach ($sorted as $cid => $votes) {
            $ranks[$cid] = $rank++;
        }
        // For each cluster, get the selected candidate's votes and rank, and barangay
        $result = [];
        foreach ($clusters as $cluster_num) {
            // Get all candidates' votes in this cluster
            $votes_in_cluster = [];
            foreach ($candidates as $candidate) {
                $votes = 0;
                $entries = \App\PollEntry::where('poll_election_candidate_id', $candidate->id)->get();
                foreach ($entries as $entry) {
                    if (!empty($entry->clustered_precinct_votes)) {
                        $json = json_decode($entry->clustered_precinct_votes, true);
                        if (is_array($json) && isset($json[$cluster_num])) {
                            $votes += intval($json[$cluster_num]);
                        }
                    }
                }
                $votes_in_cluster[$candidate->id] = $votes;
            }
            // Sort for rank in this cluster
            $sorted_cluster = collect($votes_in_cluster)->sortByDesc(function($votes, $cid) { return $votes; });
            $cluster_ranks = [];
            $cluster_rank = 1;
            foreach ($sorted_cluster as $cid => $votes) {
                $cluster_ranks[$cid] = $cluster_rank++;
            }
            // Only include the selected candidate
            $selected = $candidates->firstWhere('poll_candidate_id', $candidate_id);
            if ($selected) {
                $result[] = [
                    'area_name' => $cluster_num,
                    'barangay' => $cluster_barangay_map[$cluster_num] ?? '',
                    'rank' => $cluster_ranks[$selected->id] ?? null,
                    'total_votes' => $votes_in_cluster[$selected->id] ?? 0,
                ];
            }
        }
        return response()->json($result);
    }

    // Candidate Ranking Report View
    public function candidateRankingReport()
    {
        return view('backend.poll.reports.candidate_ranking');
    }

}
