<form method="post" class="validate" style="width:100%;" autocomplete="off" action="{{ url('voter/update') }}" enctype="multipart/form-data">
    {{ csrf_field() }}
    <input type="hidden" name="voter_id" value="{{ $voter->id }}">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Last Name') }}</label>
                <input type="text" class="form-control" name="last_name" id="last_name" value="{{ $voter->last_name }}" required>
                <span class="err-message">{{ _lang('Last Name is required.') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('First Name') }}</label>
                <input type="text" class="form-control" name="first_name" id="first_name" value="{{ $voter->first_name }}" required>
                <span class="err-message">{{ _lang('First Name is required.') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Middle Name') }}</label>
                <input type="text" class="form-control" name="middle_name" id="middle_name" value="{{ $voter->middle_name }}" required>
                <span class="err-message">{{ _lang('Middle Name is required.') }}</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Date of Birth') }}</label>
                <input type="text" class="form-control datepicker" name="birth_date" id="birth_date" value="{{ $voter->birth_date }}" required>
                <span class="err-message">{{ _lang('Date Of Birth is required.') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label" for="gender">{{ _lang('Gender') }}</label>
                <select class="form-control select2" name="gender">
                    <option value="">Please select Gender</option>
                    <option value="M" {{ $voter->gender == 'M' ? 'selected="selected"' : '' }} >Male</option>
                    <option value="F" {{ $voter->gender == 'F' ? 'selected="selected"' : '' }} >Female</option>
                </select>
                <span class="err-message">{{ _lang('Gender is required') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Address') }}</label>
                <input type="text" class="form-control" name="address" id="address" value="{{ $voter->address }}" required>
                <span class="err-message">{{ _lang('Address is required.') }}</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Barangay') }}</label>
                <select class="form-control select2" name="brgy">
                    <option value="">Please select Barangay</option>
                    @foreach($brgys as $brgy)
                    <option value="{{$brgy}}" {{ $voter->brgy == $brgy ? 'selected="selected"' : ''}} >{{$brgy}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Barangay is required') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Alliance') }}</label>
                <select class="form-control select2" name="alliance">
                    <option value="">Please select Alliance</option>
                    @foreach($alliances as $alliance)
                    <option value="{{$alliance}}" {{ $voter->alliance == $alliance ? 'selected="selected"' : ''}} >{{$alliance}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Alliance is required') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Affiliation') }}</label>
                <select class="form-control select2" name="affiliation">
                    <option value="">Please select Affiliation</option>
                    @foreach($affiliations as $affiliation)
                    <option value="{{$affiliation}}" {{ $voter->affiliation == $affiliation ? 'selected="selected"' : ''}} >{{$affiliation}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Affiliation is required') }}</span>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Religion') }}</label>
                <select class="form-control select2" name="religion">
                    <option value="">Please select Religion</option>
                    @foreach($religions as $religion)
                    <option value="{{$religion}}" {{ $voter->religion == $religion ? 'selected="selected"' : ''}} >{{$religion}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Religion is required') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Civil Status') }}</label>
                <select class="form-control select2" name="civil_status">
                    <option value="">Please select Civil Status</option>
                    @foreach($civil_statuses as $civil_status)
                    <option value="{{$civil_status}}" {{ $voter->civil_status == $civil_status ? 'selected="selected"' : ''}} >{{$civil_status}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Civil Status is required') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Contact Number') }}</label>
                <input type="tel" class="form-control telephone" name="contact_number" id="contact_number" value="{{ old('contact_number','+63') }}" required>
                <span class="err-message">{{ _lang('Contact Number is required.') }}</span>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-8">
            <div class="form-group">
                <label class="control-label">{{ _lang('Remarks') }}</label>
                <textarea class="form-control" name="remarks" id="remarks" value="{{ old('remarks') }}"></textarea>
                <span class="err-message">{{ _lang('Remarks is required.') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Precinct') }}</label>
                <input type="text" class="form-control" name="precinct" id="address" value="{{ $voter->precinct }}" required>
                <span class="err-message">{{ _lang('Precinct is required.') }}</span>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-save">{{ _lang('Update') }}</button>
                <button type="reset" class="btn btn-danger">{{ _lang('Reset') }}</button>						
            </div>
        </div>
    </div>

</form>
            