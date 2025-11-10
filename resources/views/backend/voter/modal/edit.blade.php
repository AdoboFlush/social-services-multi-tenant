<form method="post" class="validate" style="width:100%;" autocomplete="off" action="{{ url('voter/update') }}" enctype="multipart/form-data">
    {{ csrf_field() }}
    <input type="hidden" name="voter_id" value="{{ $voter->id }}">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Last Name') }}</label>
                <input type="text" class="form-control" name="last_name" id="last_name" value="{{ $voter->last_name }}" required>
                <span class="err-message">{{ _lang('Last Name is required.') }}</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('First Name') }}</label>
                <input type="text" class="form-control" name="first_name" id="first_name" value="{{ $voter->first_name }}" required>
                <span class="err-message">{{ _lang('First Name is required.') }}</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Middle Name') }}</label>
                <input type="text" class="form-control" name="middle_name" id="middle_name" value="{{ $voter->middle_name }}">
                <span class="err-message">{{ _lang('Middle Name is required.') }}</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Suffix') }}</label>
                <input type="text" class="form-control" name="suffix" id="suffix" value="{{ $voter->suffix }}">
                <span class="err-message">{{ _lang('Suffix is required.') }}</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Date of Birth') }}</label>
                <input type="text" class="form-control datepicker" name="birth_date" id="birth_date" value="{{ $voter->birth_date }}" required>
                <span class="err-message">{{ _lang('Date Of Birth is required.') }}</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="gender">{{ _lang('Gender') }}</label>
                <select class="form-control select2" name="gender">
                    <option value="">Please select Gender</option>
                    <option value="M" {{ $voter->gender == 'M' ? 'selected' : '' }}>Male</option>
                    <option value="F" {{ $voter->gender == 'F' ? 'selected' : '' }}>Female</option>
                </select>
                <span class="err-message">{{ _lang('Gender is required') }}</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Address') }}</label>
                <input type="text" class="form-control" name="address" id="address" value="{{ $voter->address }}" required>
                <span class="err-message">{{ _lang('Address is required.') }}</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Barangay') }}</label>
                <select class="form-control select2" name="brgy" required>
                    <option value="">Please select Barangay</option>
                    @foreach($brgys as $brgy)
                    <option value="{{$brgy}}" {{ $voter->brgy == $brgy ? 'selected' : '' }}>{{$brgy}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Barangay is required') }}</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Religion') }}</label>
                <select class="form-control select2" name="religion">
                    <option value="">Please select Religion</option>
                    @foreach($religions as $religion)
                    <option value="{{$religion}}" {{ $voter->religion == $religion ? 'selected' : '' }}>{{$religion}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Religion is required') }}</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Civil Status') }}</label>
                <select class="form-control select2" name="civil_status">
                    <option value="">Please select Civil Status</option>
                    @foreach($civil_statuses as $civil_status)
                    <option value="{{$civil_status}}" {{ $voter->civil_status == $civil_status ? 'selected' : '' }}>{{$civil_status}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Civil Status is required') }}</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Contact Number') }}</label>
                <input type="tel" class="form-control telephone" name="contact_number" id="contact_number" value="{{ !empty($voter->contact_number) ? $voter->contact_number : '+63' }}">
                <span class="err-message">{{ _lang('Contact Number is required.') }}</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Precinct') }}</label>
                <input type="text" class="form-control" name="precinct" id="precinct" value="{{ $voter->precinct }}" required>
                <span class="err-message">{{ _lang('Precinct is required.') }}</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Alliance') }}</label>
                <span id="alliance_select" style="display:inline">
                    <select class="form-control select2" name="alliance" id="alliance">
                        <option value="">N/A</option>
                        @foreach($alliances as $alliance)
                        <option value="{{$alliance}}" {{ $voter->alliance == $alliance ? 'selected' : '' }}>{{$alliance}}</option>
                        @endforeach
                    </select>
                </span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Affiliation') }}</label>
                <span id="affiliation_select" style="display:inline">
                    <select class="form-control select2" name="affiliation" id="affiliation">
                        <option value="">N/A</option>
                        @foreach($affiliations as $affiliation)
                        <option value="{{$affiliation}}" {{ $voter->affiliation == $affiliation ? 'selected' : '' }}>{{$affiliation}}</option>
                        @endforeach
                    </select>
                </span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Sectoral') }}</label>
                <span id="sectoral_select" style="display:inline">
                    <select class="form-control select2" name="sectoral" id="sectoral">
                        <option value="">N/A</option>
                        @foreach($sectorals as $sectoral)
                        <option value="{{$sectoral}}" {{ $voter->sectoral == $sectoral ? 'selected' : '' }}>{{$sectoral}}</option>
                        @endforeach
                    </select>
                </span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Position') }}</label>
                <span id="position_select" style="display:inline">
                    <select class="form-control select2" name="position" id="position">
                        <option value="">N/A</option>
                        @foreach($positions as $position)
                        <option value="{{$position}}" {{ $voter->position == $position ? 'selected' : '' }}>{{$position}}</option>
                        @endforeach
                    </select>
                </span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Party List') }}</label>
                <span id="party_list_select" style="display:inline">
                    <select class="form-control select2" name="party_list" id="party_list">
                        <option value="">N/A</option>
                        @foreach($party_lists as $party_list)
                        <option value="{{$party_list}}" {{ $voter->party_list == $party_list ? 'selected' : '' }}>{{$party_list}}</option>
                        @endforeach
                    </select>
                </span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Beneficiaries') }}</label>
                <select class="form-control select2" name="beneficiary">
                    <option value="">Please select Beneficiary</option>
                    @foreach($beneficiaries as $beneficiary)
                    <option value="{{$beneficiary}}" {{ $voter->beneficiary == $beneficiary ? 'selected' : '' }}>{{$beneficiary}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Beneficiaries is required') }}</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Remarks') }}</label>
                <textarea class="form-control" name="remarks" id="remarks">{{ $voter->remarks }}</textarea>
                <span class="err-message">{{ _lang('Remarks is required.') }}</span>
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