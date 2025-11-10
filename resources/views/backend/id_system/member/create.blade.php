@extends('backend.id_system.layout')

@section('card-header')
<h3 class="card-title">Create a Member</h3>
@endsection

@section('tab-content')

<form method="post" style="width:100%;" autocomplete="off" action="{{ url('id_system/members/store') }}" enctype="multipart/form-data">
    {{ csrf_field() }}

    @if(Session::has('success'))
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success">{{ session('success') }}</div>
            </div>
        </div>
    @endif

    @if(Session::has('warning'))
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning">{{ session('warning') }}</div>
            </div>
        </div>
    @endif
    
    @if(Session::has('error'))
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-danger">{{ session('error') }}</div>
            </div>
        </div>
    @endif

    <input type="hidden" name="is_voter" id="is_voter" value="" />
    <input type="hidden" name="parent_id" id="parent_id" value="" />

    <div class="row">
        <div class="col-md-3 mb-3">
            <label class="control-label">{{ _lang('Search To Voters') }} : </label>
            <select class="form-control select-2-ajax" id="voter_list">
                <option value="">Please select from voter</option>
            </select>
        </div>
        <div class="col-md-1 mb-3">
            <label class="control-label">--</label>
            <button type="button" id="add_voter" class="btn btn-primary form-control">{{ _lang('Add') }}</button>
        </div>
        <div class="col-md-1 mb-3">
            <label class="control-label">Is a Voter</label>
            <input type="text" class="form-control" value="No" id="is_voter_label" readonly>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">Please enter the member information</div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Last Name') }}</label>
                <input type="text" class="form-control" name="last_name" id="last_name" value="{{ old('last_name') }}" required>
                <span class="err-message">{{ _lang('Last Name is required.') }}</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('First Name') }}</label>
                <input type="text" class="form-control" name="first_name" id="first_name" value="{{ old('first_name') }}" required>
                <span class="err-message">{{ _lang('First Name is required.') }}</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Middle Name') }}</label>
                <input type="text" class="form-control" name="middle_name" id="middle_name" value="{{ old('middle_name') }}">
                <span class="err-message">{{ _lang('Middle Name is required.') }}</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Suffix') }}</label>
                <input type="text" class="form-control" name="suffix" id="suffix" value="{{ old('suffix') }}">
                <span class="err-message">{{ _lang('Suffix is required.') }}</span>
            </div>
        </div>
        
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Date of Birth') }}</label>
                <input type="text" class="form-control datepicker" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" required>
                <span class="err-message">{{ _lang('Date Of Birth is required.') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label" for="gender">{{ _lang('Gender') }}</label>
                <select class="form-control select2" name="gender" id="gender" required>
                    <option value="">Please select Gender</option>
                    <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>Male</option>
                    <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Female</option>
                </select>
                <span class="err-message">{{ _lang('Gender is required') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Address') }}</label>
                <input type="text" class="form-control" name="address" id="address" value="{{ old('address') }}" required>
                <span class="err-message">{{ _lang('Address is required.') }}</span>
            </div>
        </div>
        
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Barangay') }}</label>
                <select class="form-control select2" name="brgy" id="brgy" required>
                    <option value="">Please select Barangay</option>
                    @foreach($brgys as $brgy)
                    <option value="{{$brgy}}" {{ old('brgy') == $brgy ? 'selected' : '' }}>{{$brgy}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Barangay is required') }}</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Alliance') }}</label>
                <select class="form-control select2" name="alliance" id="alliance">
                    <option value="">Please select Alliance</option>
                    @foreach($alliances as $alliance)
                    <option value="{{$alliance}}" {{ old('alliance') == $alliance ? 'selected' : '' }}>{{$alliance}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Alliance is required') }}</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Affiliation') }}</label>
                <select class="form-control select2" name="affiliation" id="affiliation">
                    <option value="">Please select Affiliation</option>
                    @foreach($affiliations as $affiliation)
                    <option value="{{$affiliation}}" {{ old('affiliation') == $affiliation ? 'selected' : '' }}>{{$affiliation}}</option>
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
                <select class="form-control select2" name="religion" id="religion">
                    <option value="">Please select Religion</option>
                    @foreach($religions as $religion)
                    <option value="{{$religion}}" {{ old('religion') == $religion ? 'selected' : '' }}>{{$religion}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Religion is required') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Civil Status') }}</label>
                <select class="form-control select2" name="civil_status" id="civil_status">
                    <option value="">Please select Civil Status</option>
                    @foreach($civil_statuses as $civil_status)
                    <option value="{{$civil_status}}" {{ old('civil_status') == $civil_status ? 'selected' : '' }}>{{$civil_status}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Civil Status is required') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Contact Number') }}</label>
                <input type="tel" class="form-control telephone" name="contact_number" id="contact_number" value="+63" />
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
                <input type="text" class="form-control" name="precinct" id="precinct" value="{{ old('precinct') }}" required>
                <span class="err-message">{{ _lang('Precinct is required.') }}</span>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">In case of Emergency, Please contact:</div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Contact Person Last Name') }}</label>
                <input type="text" class="form-control" name="contact_person_last_name" id="contact_person_last_name" value="{{ old('contact_person_last_name') }}">
                <span class="err-message">{{ _lang('Contact Person Last Name is required.') }}</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Contact Person First Name') }}</label>
                <input type="text" class="form-control" name="contact_person_first_name" id="contact_person_first_name" value="{{ old('contact_person_first_name') }}">
                <span class="err-message">{{ _lang('Contact Person First Name is required.') }}</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Contact Person Middle Name') }}</label>
                <input type="text" class="form-control" name="contact_person_middle_name" id="contact_person_middle_name" value="{{ old('contact_person_middle_name') }}">
                <span class="err-message">{{ _lang('Contact Person Middle Name is required.') }}</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Contact Person Suffix') }}</label>
                <input type="text" class="form-control" name="contact_person_suffix" id="contact_person_suffix" value="{{ old('contact_person_suffix') }}">
                <span class="err-message">{{ _lang('Contact Person Suffix is required.') }}</span>
            </div>
        </div>
        
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Contact Person Address') }}</label>
                <input type="text" class="form-control" name="contact_person_address" id="contact_person_address" value="">
                <span class="err-message">{{ _lang('Contact Number Address is required.') }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Contact Number') }}</label>
                <input type="tel" class="form-control telephone" name="contact_person_number" id="contact_person_number" value="+63">
                <span class="err-message">{{ _lang('Contact Number is required.') }}</span>
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


@endsection


@section('js-script')

<script>

$(document).ready(function(){

    $(".select-2-ajax").select2({
        ajax: {
            url: "{{ url('voter/search') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
            return {
                q: params.term, // search term
                page: params.page
            };
            },
            processResults: function (data, params) {
            params.page = params.page || 1;
            return {
                results: data,
                pagination: {
                more: (params.page * 30) < data.total_count
                }
            };
            },
            cache: true
        },
        placeholder: 'Search from Voters',
        minimumInputLength: 4,
    });

    $("#add_voter").on('click', function(e){
        let voter_id = $("#voter_list").find('option:selected').val();
        $.ajax("{{ url('voter/get') }}/" + voter_id, {
            type: 'GET',
            success: function (data, status, xhr) {
                $('#first_name').val(data.first_name).prop('readonly', true);
                $('#last_name').val(data.last_name).prop('readonly', true);
                $('#middle_name').val(data.middle_name).prop('readonly', true);
                $('#suffix').val(data.suffix).prop('readonly', true);
                $('#birth_date').val(data.birth_date).prop('readonly', true);
                $('#address').val(data.address).prop('readonly', true);
                $('#precinct').val(data.precinct).prop('readonly', true);
                $('#contact_number').val(data.contact_number);
                $("#gender").val(data.gender).trigger('change').attr('readonly', true);
                $("#brgy").val(data.brgy).trigger('change').attr('readonly', true);
                $("#alliance").val(data.alliance).trigger('change').attr('readonly', true);
                $("#affiliation").val(data.affiliation).trigger('change').attr('readonly', true);
                $("#religion").val(data.religion).trigger('change').attr('readonly', true);
                $("#civil_status").val(data.civil_status).trigger('change').attr('readonly', true);
                $("#is_voter").val(1);
                $("#parent_id").val(data.id);
                $("#is_voter_label").val("Yes");
            },
            error: function (jqXhr, textStatus, errorMessage) {
                alert('Voter not found');
            }
        });
    });

});    


</script>

@endsection