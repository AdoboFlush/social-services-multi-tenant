<form method="post" class="validate" style="width:100%;" autocomplete="off" action="{{ url('tag/update') }}" enctype="multipart/form-data">
    {{ csrf_field() }}

    <input type="hidden" name="tag_id" value="{{ $tag->id }}">
    <div class="row">

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Name') }}</label>
                <input type="text" class="form-control" name="name" id="name" value="{{ $tag->name }}" required>
                <span class="err-message">{{ _lang('Name is required.') }}</span>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label" for="type">{{ _lang('Type') }}</label>
                <select class="form-control select2" name="type">
                    <option value="">Please select Type</option>
                    <option value="affiliation" {{ $tag->type == "affiliation" ? 'selected="selected"' : ''}} >Affiliation</option>
                    <option value="alliance" {{ $tag->type == "alliance" ? 'selected="selected"' : ''}} >Alliance</option>
                    <option value="alliance_1" {{ $tag->type == "alliance_1" ? 'selected="selected"' : ''}}>Sub Alliance</option>
                    <option value="brgy" {{ $tag->type == "brgy" ? 'selected="selected"' : ''}} >Barangay</option>
                    <option value="religion" {{ $tag->type == "religion" ? 'selected="selected"' : ''}} >Religion</option>
                    <option value="purpose" {{ $tag->type == "purpose" ? 'selected="selected"' : ''}}>Purpose</option>
                    <option value="civil_status" {{ $tag->type == "civil_status" ? 'selected="selected"' : ''}}>Civil Status</option>
                    <option value="beneficiaries" {{ $tag->type == "beneficiaries" ? 'selected="selected"' : ''}}>Beneficiaries</option>
                    <option value="sectoral" {{ $tag->type == "sectoral" ? 'selected="selected"' : ''}} >Sectoral</option>
                    <option value="organization" {{ $tag->type == "organization" ? 'selected="selected"' : ''}}>Organization</option>
                    <option value="party_list" {{ $tag->type == "party_list" ? 'selected="selected"' : ''}}>Party List</option>
                    <option value="position" {{ $tag->type == "position" ? 'selected="selected"' : ''}}>Position</option>
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
                    <option value="{{$parent->id}}" {{ $parent->id == $tag->parent_id ? 'selected="selected"' : ''}}>{{$parent->name}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Parent Tag is required') }}</span>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Custom Field') }}</label>
                <input type="text" class="form-control" name="custom_field" id="custom_field" value="{{ $tag->custom_field }}">
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
            