<form method="post" class="validate" style="width:100%;" autocomplete="off" action="{{ route('poll.elections.update', $poll_election->id, false) }}" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Name') }}</label>
                <input type="text" class="form-control" name="name" id="name" value="{{ $poll_election->name }}" required>
                <span class="err-message">{{ _lang('Name is required.') }}</span>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Election Date') }}</label>
                <input type="date" class="form-control" name="election_date" id="election_date" value="{{ $poll_election->election_date }}" required>
                <span class="err-message">{{ _lang('Election Date is required.') }}</span>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Type') }}</label>
                <input type="text" class="form-control" name="type" id="type" value="{{ $poll_election->type }}" required>
                <span class="err-message">{{ _lang('Type is required.') }}</span>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Remarks') }}</label>
                <textarea class="form-control" name="remarks" id="remarks">{{ $poll_election->remarks }}</textarea>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Is Active') }}</label>
                <select class="form-control" name="is_active" id="is_active" required>
                    <option value="1" {{ $poll_election->is_active ? 'selected' : '' }}>{{ _lang('Active') }}</option>
                    <option value="0" {{ !$poll_election->is_active ? 'selected' : '' }}>{{ _lang('In-active') }}</option>
                </select>
                <span class="err-message">{{ _lang('Status is required.') }}</span>
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
