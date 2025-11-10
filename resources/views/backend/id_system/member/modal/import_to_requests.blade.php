<form action="{{url('id_system/members/import_to_request')}}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning">Create requests with these parameters:</div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Filter Field') }}</label>
                <select class="form-control select2" name="filter_field" id="filter_field">
                    <option value="">Please select a Field</option>
                    <option value="account_number">Member Number</option>
                    <option value="first_name">First Name</option>
                    <option value="middle_name">Middle Name</option>
                    <option value="last_name">Last Name</option>
                    <option value="brgy">Brgy</option>
                    <option value="alliance">Alliance</option>	
					@if(Auth::user()->user_access !== "encoder_voter")
					<option value="affiliation">Affiliation</option>
					@endif
                    <option value="position">Position</option>
                    <option value="party_list">Party List</option>
                    <option value="sectoral">Sectoral</option>
                    <option value="beneficiary">Beneficiary</option>
                    <option value="religion">Religion</option>
                    <option value="civil_status">Civil Status</option>
                    <option value="contact_number">Contact Number</option>
                    <option value="remarks">Remarks</option>
                </select>
                <span class="err-message">{{ _lang('filter_field is required') }}</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Enter what to search') }}</label>
                <input type="text" class="form-control" name="filter_search" id="filter_search" placeholder="Search" />
                <span class="err-message">{{ _lang('Search Value is required') }}</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('ID Card Template') }}</label>
                <select class="form-control select2" name="template_id" required>
                    <option value="">Please select a Template</option>
                    @foreach($templates as $template)
                    <option value="{{$template->id}}">{{$template->name}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('ID Card Template is required') }}</span>
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group">
                <label class="control-label">--</label>
                <button class="btn btn-primary form-control" type="submit">Create</button>
            </div>
        </div>
    </div>
</form>