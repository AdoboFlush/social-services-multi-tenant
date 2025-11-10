<form action="{{url('voters/import_to_members')}}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning">Import Voters in Members table with these Parameters:</div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Filter Field') }}</label>
                <select class="form-control select2" name="filter_field" id="filter_field">
                    <option value="">Please select a Field</option>
                    <option value="first_name">First Name</option>
                    <option value="middle_name">Middle Name</option>
                    <option value="last_name">Last Name</option>
                    <option value="brgy">Brgy</option>
                    <option value="precinct">Precinct</option>
                    <option value="gender">Gender</option>
                    <option value="sectoral">Sectoral</option>
                    <option value="position">Position</option>
                    <option value="party_list">Party List</option>
                    <option value="alliance">Alliance</option>
                    @if(Auth::user()->user_access !== "encoder_voter")
                    <option value="affiliation">Affiliation</option>
                    @endif
                    <option value="religion">Religion</option>
                    <option value="civil_status">Civil Status</option>
                    <option value="contact_number">Contact Number</option>
                </select>
                <span class="err-message">{{ _lang('Filter Field is required') }}</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Search Value') }}</label>
                <input type="text" class="form-control" name="filter_search" id="filter_search" placeholder="Search" />
                <span class="err-message">{{ _lang('Search Value is required') }}</span>
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group">
                <label class="control-label">--</label>
                <button class="btn btn-primary form-control" type="submit">Import</button>
            </div>
        </div>
        
    </div>
</form>