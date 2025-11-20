@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">Add New Request</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item"><a href="{{url('social_services')}}">Social Service Assistance</a></li>
      <li class="breadcrumb-item active">Add New Request</li>
      </ol>
    </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Add New Social Service Assistance Request</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    
                    <form method="post" style="width:100%;" autocomplete="off" action="{{ url('social_service/store') }}" enctype="multipart/form-data">
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

                        <div class="row">

                            <!-- <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Control Number') }}</label>
                                    <input type="text" class="form-control" name="control_number" id="control_number" value="{{ old('control_number') }}" required>
                                    <span class="err-message">{{ _lang('Control Number is required.') }}</span>
                                </div>
                            </div> -->

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Request Type') }}</label>
                                    <select class="form-control select2" name="request_type_id" id="request_type_id" required>
                                        <option value="">Please select Request type</option>
                                        @foreach($purposes as $purpose)
                                        <option value="{{$purpose->id}}">{{$purpose->name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="err-message">{{ _lang('Request type is required') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Purpose') }}</label>
                                    <select class="form-control select2" name="purpose[]" id="purpose" multiple="multiple" disabled required>
                                        <option value="">Please select Purpose</option>
                                    </select>
                                    <span class="err-message">{{ _lang('Purpose is required') }}</span>
                                </div>
                            </div>
                    
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Amount') }}</label>
                                    <input type="number" class="form-control" name="amount" id="amount" value="{{ !empty(old('amount')) ? old('amount') : 0 }}">
                                    <span class="err-message">{{ _lang('Amount is required.') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3 font-weight-bold">Beneficiary Information
                            <span><input type="checkbox" name="is_voter" id="is_voter" class="ml-5 mr-1"  {{ old('is_voter') ? 'checked' : '' }}/>Is a Voter?</span>
                            <span><input type="checkbox" name="is_deceased" id="is_deceased" class="ml-5 mr-1"  {{ old('is_deceased') ? 'checked' : '' }}/>Is Deceased?</span>
                        </div>
                        <div class="row" id="voter-container">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Precinct') }}</label>
                                    <input type="text" class="form-control" name="precinct" id="precinct" value="" readonly>
                                    <span class="err-message">{{ _lang('Precinct is required.') }}</span>
                                </div>
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
                                    <input type="text" class="form-control" name="suffix" id="suffix" value="{{ old('suffix') }}" >
                                    <span class="err-message">{{ _lang('Suffix is required.') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Birth date') }}</label>
                                    <input type="text" class="form-control datepicker" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" required>
                                    <span class="err-message">{{ _lang('Birth Date is required.') }}</span>
                                </div>
                            </div>
                    
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Contact Number') }}</label>
                                    <input type="tel" class="form-control telephone" name="contact_number" id="contact_number" value="{{ !empty(old('contact_number')) ? old('contact_number') : '+63' }}" >
                                    <span class="err-message">{{ _lang('Contact Number is required.') }}</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Barangay') }}</label>
                                    <select class="form-control select2" name="brgy" required>
                                        <option value="">Please select Barangays</option>
                                        @foreach($brgys as $brgy)
                                        <option value="{{$brgy}}" {{ old('brgy') === $brgy ? "selected" : "" }}>{{$brgy}}</option>
                                        @endforeach
                                    </select>
                                    <span class="err-message">{{ _lang('Barangay is required.') }}</span>
                                </div>
                            </div>
                    
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" >{{ _lang('Address') }}</label>
                                    <input type="text" class="form-control" name="address" id="address" value="{{ old('address') }}" >
                                    <span class="err-message">{{ _lang('Address is required.') }}</span>
                                </div>
                            </div>
                    
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Organization') }}</label>
                                    <input type="text" class="form-control" name="organization" id="organization" value="{{ old('organization') }}">
                                    <span class="err-message">{{ _lang('Organization is required.') }}</span>
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

                            <!-- Event Dropdown -->
                            <div class="col-md-4">
                                <label for="event_id" class="control-label">Event</label>
                                <select id="event_id" name="event_id" class="form-control select2-event" style="width: 100%;" disabled>
                                    <option value="">Select Event</option>
                                </select>
                            </div>

                        </div>
                        
                        <div class="mb-3"><span class="font-weight-bold">Requestor Information</span> 
                            <span><input type="checkbox" name="same_with_beneficiary" id="same_with_beneficiary" class="ml-5 mr-1"/>Same with the beneficiary?</span>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Last Name') }}</label>
                                    <input type="text" class="form-control" name="requestor_last_name" id="requestor_last_name" value="{{ old('requestor_last_name') }}" required>
                                    <span class="err-message">{{ _lang('Last Name is required.') }}</span>
                                </div>
                            </div>
                    
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('First Name') }}</label>
                                    <input type="text" class="form-control" name="requestor_first_name" id="requestor_first_name" value="{{ old('requestor_first_name') }}" required>
                                    <span class="err-message">{{ _lang('First Name is required.') }}</span>
                                </div>
                            </div>
                    
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Middle Name') }}</label>
                                    <input type="text" class="form-control" name="requestor_middle_name" id="requestor_middle_name" value="{{ old('requestor_middle_name') }}">
                                    <span class="err-message">{{ _lang('Middle Name is required.') }}</span>
                                </div>
                            </div>
                    
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Suffix') }}</label>
                                    <input type="text" class="form-control" name="requestor_suffix" id="requestor_suffix" value="{{ old('requestor_suffix') }}" >
                                    <span class="err-message">{{ _lang('Suffix is required.') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Requestor relationship to beneficiary') }}</label>
                                    <input type="text" class="form-control" name="requestor_relationship_to_beneficiary" id="requestor_relationship_to_beneficiary" value="{{ old('requestor_relationship_to_beneficiary') }}" >
                                    <span class="err-message">{{ _lang('Relationship is required.') }}</span>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Remarks') }}</label>
                                    <textarea class="form-control" name="remarks" id="remarks">{{ old('remarks') }}</textarea>
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

                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
        </div>
    </div>
</div>
@endsection

@section('js-script')
<script>
$(document).ready(function(){
    
    @if(Session::has('success'))
        swal("{{ session('success') }}", { icon: "success" });
    @endif

    $('#is_voter').on('click', function(e){
        if($(this).is(':checked')){
            $('#precinct').attr('readonly', false);
        }else{
            $('#precinct').attr('readonly', true);
            $('#precinct').val( '' );
        }
    });

    $('#same_with_beneficiary').on('click', function(e){
        if($(this).is(':checked')){
            $('#requestor_first_name').val( $('#first_name').val() ).attr('readonly', true);
            $('#requestor_last_name').val( $('#last_name').val() ).attr('readonly', true);
            $('#requestor_middle_name').val( $('#middle_name').val() ).attr('readonly', true);
            $('#requestor_suffix').val( $('#suffix').val() ).attr('readonly', true);
        } else {
            $('#requestor_first_name').val( '' ).attr('readonly', false);
            $('#requestor_last_name').val( '' ).attr('readonly', false);
            $('#requestor_middle_name').val( '' ).attr('readonly', false);
            $('#requestor_suffix').val( '' ).attr('readonly', false);
        }
    });

    $('#request_type_id').on('change', function(e){
        let request_id = $(this).val();
        let label = $('#request_type_id option:selected').text();
        if(label === 'RICE DISTRIBUTION') {
            let processed_by = '{{ auth()->user()->full_name }}';
            let processed_date = '{{ now()->format("Y-m-d") }}';
            $('#processed_by').val(processed_by);
            $('#processed_date').val(processed_date);
        } else {
            $('#processed_by').val('');
            $('#processed_date').val('');
        }
        $('#purpose').html('<option value="">Loading Options... </option>');
        $('#purpose').attr('disabled', true);

        $.get("{{url('tag/get_child_tags')}}/" + request_id, function(data, status){
            if(status == 'success'){
                $('#purpose').html('<option value="">Please select a purpose</option>');
                $.each(data, function(key, value){
                    $('#purpose').append('<option value="'+value.name+'">'+value.name+'</option>');
                });
                $('#purpose').attr('disabled', false);
                $('#purpose').select2();
            }
        });
        $.get("{{url('/social_services/get_current_control_number')}}/" + request_id, function(data, status){
            if(status == 'success'){
                if(status == 'success'){
                    if(data.length > 0){
                        $.each(data, function(key, value){
                            $('#control_number').val(value);
                        }); 
                    }else{
                        $('#control_number').val('00000001');
                    }
                } 
            }
        });

    });

    // Event select2 initialization
    $('#event_id').select2({
        placeholder: 'Select Event',
        minimumInputLength: 4,
        ajax: {
            url: function() {
                var requestTypeId = $('#request_type_id').val() || 0;
                return '/social_service/events/' + requestTypeId;
            },
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    })
                };
            },
            cache: true
        },
        allowClear: true
    });

    // Disable event field if no request type is selected
    function toggleEventField() {
        if($('#request_type_id').val()) {
            $('#event_id').prop('disabled', false);
        } else {
            $('#event_id').val(null).trigger('change');
            $('#event_id').prop('disabled', true);
        }
    }
    toggleEventField();
    $('#request_type_id').on('change', function() {
        toggleEventField();
        $('#event_id').val(null).trigger('change');
    });
});
</script>
@endsection

