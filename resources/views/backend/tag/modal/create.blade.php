<form method="post" class="validate" style="width:100%;" autocomplete="off" action="{{ url('tag/store') }}" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="row">

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Name') }}</label>
                <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" required>
                <span class="err-message">{{ _lang('Name is required.') }}</span>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label" for="type">{{ _lang('Type') }}</label>
                <select class="form-control select2" name="type">
                    <option value="">Please select Type</option>
                    <option value="alliance">Alliance</option>
                    <option value="alliance_1">Sub Alliance</option>
                    <option value="affiliation">Affiliation</option>
                    <option value="sectoral">Sectoral</option>
                    <option value="brgy">Barangay</option>
                    <option value="religion">Religion</option>
                    <option value="purpose">Purpose</option>
                    <option value="civil_status">Civil Status</option>
                    <option value="beneficiaries">Beneficiaries</option>
                    <option value="organization">Organization</option>
                    <option value="party_list">Party List</option>
                    <option value="position">Position</option>
                </select>
                <span class="err-message">{{ _lang('Type is required') }}</span>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label" for="type">{{ _lang('Parent Tag') }}</label>
                <select class="form-control select2" name="parent_id">
                    <option value="">Please select Parent Tag (If Applicable)</option>
                    @foreach($tags as $parent)
                    <option value="{{$parent->id}}">{{$parent->name}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Parent Tag is required') }}</span>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Custom Field') }}</label>
                <input type="text" class="form-control" name="custom_field" id="custom_field" value="{{ old('custom_field') }}">
                <span class="err-message">{{ _lang('Custom Field is required.') }}</span>
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
            