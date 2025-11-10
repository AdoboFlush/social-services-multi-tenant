<form method="post" class="validate" style="width:100%;" autocomplete="off" action="{{ route('poll.elections.candidates.update', $poll_election_candidate->id, [], false) }}" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Position') }}</label>
                <select class="form-control" name="position" id="position" required>
                    <option value="">{{ _lang('Select Position') }}</option>
                    @foreach(\App\PollElectionCandidate::POSITIONS as $position)
                        <option value="{{ $position }}" {{ $poll_election_candidate->position == $position ? 'selected' : '' }}>
                            {{ $position }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Party-list') }}</label>
                <select class="form-control" name="party_list" id="party_list">
                    <option value="">{{ _lang('Select Party-list') }}</option>
                    @foreach(\App\PollElectionCandidate::PARTYLISTS as $partylist)
                        <option value="{{ $partylist }}" {{ $poll_election_candidate->party_list == $partylist ? 'selected' : '' }}>
                            {{ $partylist }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Local Party') }}</label>
                <select class="form-control" name="party" id="party" required>
                    <option value="">{{ _lang('Select Local Party') }}</option>
                    @foreach(\App\PollElectionCandidate::PARTIES as $party)
                        <option value="{{ $party }}" {{ $poll_election_candidate->party == $party ? 'selected' : '' }}>
                            {{ $party }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('National Party') }}</label>
                <select class="form-control" name="national_party" id="national_party" required>
                    <option value="">{{ _lang('Select National Party') }}</option>
                    @foreach(\App\PollElectionCandidate::NATIONAL_PARTIES as $national_party)
                        <option value="{{ $national_party }}" {{ $poll_election_candidate->national_party == $national_party ? 'selected' : '' }}>
                            {{ $national_party }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-save">{{ _lang('Save') }}</button>
                <button type="reset" class="btn btn-danger">{{ _lang('Reset') }}</button>
            </div>
        </div>
    </div>
</form>
