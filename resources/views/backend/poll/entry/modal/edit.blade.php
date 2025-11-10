<form method="post" class="validate" style="width:100%;" autocomplete="off" action="{{ route('poll.entries.update', $poll_entry->id, false) }}">
    {{ csrf_field() }}

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Election</label>
                <select class="form-control election-search-ajax" name="poll_election_id" required>
                    <option value="{{ $poll_entry->poll_election_id }}">{{ $poll_entry->pollElection->name }}</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Watcher</label>
                <select class="form-control watcher-search-ajax" name="poll_watcher_id" required>
                    <option value="{{ $poll_entry->poll_election_watcher_id }}">{{ $poll_entry->pollWatcher->user->full_name }}</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Candidate</label>
                <select class="form-control candidate-search-ajax" name="poll_candidate_id" required>
                    <option value="{{ $poll_entry->poll_election_candidate_id }}">{{ $poll_entry->pollCandidate->name }}</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Votes</label>
                <input type="number" class="form-control" name="votes" value="{{ $poll_entry->votes }}" min="0" required>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">Remarks</label>
                <textarea class="form-control" name="remarks">{{ $poll_entry->remarks }}</textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-save">Save</button>
                <button type="reset" class="btn btn-danger">Reset</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(function() {
        $(".election-search-ajax").select2({
            width: "100%",
            dropdownParent: $('#main_modal'),
            ajax: {
                url: "{{ route('poll.elections.index', [], false) }}",
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => ({
                    results: data.data.map(election => ({
                        id: election.id,
                        text: election.name
                    }))
                }),
                cache: true
            },
            placeholder: 'Search Elections',
            minimumInputLength: 2,
        }).on('change', function() {
            const electionId = $(this).val();
            if (electionId) {
                $(".watcher-search-ajax").prop("disabled", false).val(null).trigger("change");
                $(".candidate-search-ajax").prop("disabled", false).val(null).trigger("change");
            } else {
                $(".watcher-search-ajax, .candidate-search-ajax").prop("disabled", true).val(null).trigger("change");
            }
        });

        $(".watcher-search-ajax").select2({
            width: "100%",
            dropdownParent: $('#main_modal'),
            ajax: {
                url: () => "{{ route('poll.elections.filtered.watchers', ':id') }}".replace(':id', $(".election-search-ajax").val()),
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => ({
                    results: data.data.map(watcher => ({
                        id: watcher.id,
                        text: watcher.full_name
                    }))
                }),
                cache: true
            },
            placeholder: 'Search Watchers',
            minimumInputLength: 2,
        });

        $(".candidate-search-ajax").select2({
            width: "100%",
            dropdownParent: $('#main_modal'),
            ajax: {
                url: () => "{{ route('poll.elections.filtered.candidates', ':id') }}".replace(':id', $(".election-search-ajax").val()),
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => ({
                    results: data.data.map(candidate => ({
                        id: candidate.id,
                        text: candidate.name
                    }))
                }),
                cache: true
            },
            placeholder: 'Search Candidates',
            minimumInputLength: 2,
        });
    });
</script>
