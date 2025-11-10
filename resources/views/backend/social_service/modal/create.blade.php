<form method="post" class="validate" style="width:100%;" autocomplete="off" action="{{ url('social_service/store') }}" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="row">
    
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Control Number') }}</label>
                <input type="text" class="form-control" name="control_number" id="control_number" value="{{ old('control_number') }}" required>
                <span class="err-message">{{ _lang('Control Number is required.') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Last Name') }}</label>
                <input type="text" class="form-control" name="last_name" id="last_name" value="{{ old('last_name') }}" required>
                <span class="err-message">{{ _lang('Last Name is required.') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('First Name') }}</label>
                <input type="text" class="form-control" name="first_name" id="first_name" value="{{ old('first_name') }}" required>
                <span class="err-message">{{ _lang('First Name is required.') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Middle Name') }}</label>
                <input type="text" class="form-control" name="middle_name" id="middle_name" value="{{ old('middle_name') }}">
                <span class="err-message">{{ _lang('Middle Name is required.') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Suffix') }}</label>
                <input type="text" class="form-control" name="suffix" id="suffix" value="{{ old('suffix') }}">
                <span class="err-message">{{ _lang('Suffix is required.') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Contact Number') }}</label>
                <input type="tel" class="form-control telephone" name="contact_number" id="contact_number" value="{{ old('contact_number','+63') }}" required>
                <span class="err-message">{{ _lang('Contact Number is required.') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Barangay') }}</label>
                <select class="form-control select2" name="brgy" required>
                    <option value="">Please select Barangay</option>
                    @foreach($brgys as $brgy)
                    <option value="{{$brgy}}">{{$brgy}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Barangay is required') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Address') }}</label>
                <input type="text" class="form-control" name="address" id="address" value="{{ old('address') }}" required>
                <span class="err-message">{{ _lang('Address is required.') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Organization') }}</label>
                <input type="text" class="form-control" name="organization" id="organization" value="{{ old('organization') }}" required>
                <span class="err-message">{{ _lang('Organization is required.') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Purpose') }}</label>
                <select class="form-control select2" name="purpose" id="purpose" required>
                    <option value="">Please select Purpose</option>
                    @foreach($purposes as $purpose)
                    <option value="{{$purpose}}">{{$purpose}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Purpose is required') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Amount') }}</label>
                <input type="number" class="form-control" name="amount" id="amount" value="{{ old('amount') }}" min="500" max="5000" required>
                <span class="err-message">{{ _lang('Amount is required.') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Date File') }}</label>
                <input type="text" class="form-control datepicker" name="file_date" id="file_date" value="{{ old('file_date') }}" required>
                <span class="err-message">{{ _lang('Date File is required.') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Date Processed') }}</label>
                <input type="text" class="form-control datepicker" name="processed_date" id="processed_date" value="{{ old('processed_date') }}" required>
                <span class="err-message">{{ _lang('Date Processed is required.') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Referred By') }}</label>
                <input type="text" class="form-control" name="referred_by" id="referred_by" value="{{ old('referred_by') }}" required>
                <span class="err-message">{{ _lang('Referred By is required.') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Processed By') }}</label>
                <input type="text" class="form-control" name="processed_by" id="processed_by" value="{{ old('processed_by') }}" required>
                <span class="err-message">{{ _lang('Processed By is required.') }}</span>
            </div>
        </div>

        <div class="col-md-8">
            <div class="form-group">
                <label class="control-label">{{ _lang('Remarks') }}</label>
                <textarea class="form-control" name="remarks" id="remarks" value="{{ old('remarks') }}"></textarea>
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
            