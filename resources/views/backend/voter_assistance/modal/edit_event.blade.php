<form method="post" class="ajax-submit" autocomplete="off" action="{{ route('voter_assistance.events.update', ['assistance_event' => $assistance_event->id], false) }}" enctype="multipart/form-data">

    {{ csrf_field() }}
    <input name="_method" type="hidden" value="POST">

    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label">{{ _lang('Event Name') }}</label>
            <input type="text" class="form-control" name="name" value="{{ $assistance_event->name }}" required>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label">{{ _lang('Description') }}</label>
            <textarea class="form-control" name="description">{{ $assistance_event->description }}</textarea>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label">{{ _lang('Assistance Type') }}</label>
            <input type="text" class="form-control" name="assistance_type" value="{{ $assistance_event->assistance_type }}" required>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label">{{ _lang('Start Date') }}</label>
            <input type="datetime-local" class="form-control" name="starts_at" value="{{ $assistance_event->starts_at }}">
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label">{{ _lang('End Date') }}</label>
            <input type="datetime-local" class="form-control" name="ends_at" value="{{ $assistance_event->ends_at }}">
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label">{{ _lang('Amount') }}</label>
            <input type="number" class="form-control" name="amount" value="{{ $assistance_event->amount }}" required>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label">{{ _lang('Is Active') }}</label>
            <select class="form-control" name="is_active" required>
                <option value="1" {{ $assistance_event->is_active ? 'selected' : '' }}>{{ _lang('Yes') }}</option>
                <option value="0" {{ !$assistance_event->is_active ? 'selected' : '' }}>{{ _lang('No') }}</option>
            </select>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label">{{ _lang('Custom Condition Props (Format: {field_name}={search_value}) (separated by semicolon ; if multiple.)') }}</label>
            <br><i>Supported Fields : alliance; alliance_1; affiliation; affiliation_subgroup; affiliation_1; sectoral; religion; organization; contact_number; is_deceased; civil_status; remarks; party_list; party_list_1;</i>
            <input type="text" class="form-control" name="custom_condition_props" value="{{ $assistance_event->custom_condition_props }}">
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <button type="submit" class="btn btn-primary">{{ _lang('Update') }}</button>
        </div>
    </div>

</form>
