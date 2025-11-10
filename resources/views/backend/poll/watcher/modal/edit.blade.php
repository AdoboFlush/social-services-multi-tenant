<form method="post" class="validate" style="width:100%;" autocomplete="off" action="{{ route('poll.watchers.update', $poll_watcher->id, false) }}" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('User') }}</label>
                <select class="form-control user-search-ajax" name="user_id" id="user_id" required>
                    <option value="{{ $poll_watcher->user_id }}">{{ $poll_watcher->user->first_name . ' ' . $poll_watcher->user->last_name }}</option>
                </select>
                <span class="err-message">{{ _lang('User is required.') }}</span>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Barangay') }}</label>
                <select class="form-control select2" name="brgy" required>
                    <option value="">All</option>
                    @foreach($brgys as $brgy)
                    <option value="{{$brgy}}" {{ $poll_watcher->brgy === $brgy ? "selected" : "" }}>{{$brgy}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Barangay is required.') }}</span>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Precinct') }}</label>
                <input type="text" class="form-control" name="precinct" id="precinct" value="{{ $poll_watcher->precinct }}">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Poll Place') }}</label>
                <input type="text" class="form-control" name="poll_place" value="{{ $poll_watcher->poll_place }}">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Clustered Precincts') }}</label>
                <p><i>Note: Add dash "-" if its a precinct range. Example 1-10</i></p>
                <input type="text" class="form-control" name="clustered_precincts" value="{{ $poll_watcher->clustered_precincts }}">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('No. of Registered Voters') }}</label>
                <input type="number" class="form-control" name="no_of_registered_voters" value="{{ $poll_watcher->no_of_registered_voters }}">
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

<script>
    $(function() {
        $(".user-search-ajax").select2({
            width: "100%",
            dropdownParent: $('#main_modal'), // Ensure dropdown is within the modal
            ajax: {
                url: "{{ route('poll.watchers.user.search', [], false) }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data.map(user => ({
                            id: user.id,
                            text: user.first_name + ' ' + user.last_name
                        })),
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            placeholder: 'Search Users',
            minimumInputLength: 2,
        });
    });
</script>
