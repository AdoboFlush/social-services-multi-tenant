@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">Edit Request</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item"><a href="{{url('social_services')}}">Social Service Assistance</a></li>
      <li class="breadcrumb-item active">Edit Request</li>
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
                  <h3 class="card-title">Edit Social Service Assistance Request</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    
                    <form method="post" style="width:100%;" autocomplete="off" action="{{ url('social_service/update') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="social_service_id" value="{{ $socialService->id }}">
                        <input type="hidden" name="previous_status" value="{{ $socialService->status }}">
                        <input type="hidden" name="encoder_id" value="{{ $socialService->encoder_id }}">

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

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Control Number') }}</label>
                                    <input type="text" class="form-control" name="control_number" id="control_number" value="{{ $socialService->control_number }}" readonly>
                                    <span class="err-message">{{ _lang('Control Number is required.') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Request Type') }}</label>
                                    <select class="form-control select2" name="request_type_id" id="request_type_id" required>
                                        <option value="">Please select Request type</option>
                                        @foreach($purposes as $purpose)
                                        <option value="{{$purpose->id}}" {{ $socialService->request_type_id == $purpose->id ? 'selected="selected"' : ''}}>{{$purpose->name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="err-message">{{ _lang('Request type is required') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Purpose') }}</label>
                                    <select class="form-control select2" name="purpose[]" id="purpose" multiple="multiple" readonly required>
                                        @php
                                            $selected_purposes = json_decode($socialService->purpose, true);
                                        @endphp
                                        @foreach($selected_purposes as $selected_purpose)
                                            <option value="{{$selected_purpose}}" selected>{{$selected_purpose}}</option>
                                        @endforeach
                                    </select>
                                    <span class="err-message">{{ _lang('Purpose is required') }}</span>
                                </div>
                            </div>
                    
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Amount') }}</label>
                                    <input type="number" class="form-control" name="amount" id="amount" value="{{ $socialService->amount }}">
                                    <span class="err-message">{{ _lang('Amount is required.') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 font-weight-bold">Beneficiary Information

                            <span><input type="checkbox" name="is_voter" id="is_voter" class="ml-5 mr-1"  {{ $socialService->is_voter == 1 ? 'checked' : '' }}/>Is a Voter?</span>

                            @if($socialService->is_voter == 1)
                            <span><input type="checkbox" id="enable_voter_edit" class="ml-5 mr-1" />Enable Voter Info Editing</span>
                            @endif

                            <span><input type="checkbox" name="is_deceased" id="is_deceased" class="ml-5 mr-1"  {{ $socialService->is_deceased == 1 ? 'checked' : '' }}/>Is Deceased?</span>

                        </div>

                        <div class="row" id="voter-container">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Precinct') }}</label>
                                    <input type="text" class="form-control" name="precinct" id="precinct" value="{{ $socialService->precinct }}" readonly>
                                    <span class="err-message">{{ _lang('Precinct is required.') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Last Name') }}</label>
                                    <input type="text" class="form-control" name="last_name" id="last_name" value="{{ $socialService->last_name }}" {{ $socialService->is_voter == 1 ? 'readonly' : '' }} required>
                                    <span class="err-message">{{ _lang('Last Name is required.') }}</span>
                                </div>
                            </div>
                    
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('First Name') }}</label>
                                    <input type="text" class="form-control" name="first_name" id="first_name" value="{{ $socialService->first_name }}" {{ $socialService->is_voter == 1 ? 'readonly' : '' }} required>
                                    <span class="err-message">{{ _lang('First Name is required.') }}</span>
                                </div>
                            </div>
                    
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Middle Name') }}</label>
                                    <input type="text" class="form-control" name="middle_name" id="middle_name" value="{{ $socialService->middle_name }}" {{ $socialService->is_voter == 1 ? 'readonly' : '' }}>
                                    <span class="err-message">{{ _lang('Middle Name is required.') }}</span>
                                </div>
                            </div>
                    
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Suffix') }}</label>
                                    <input type="text" class="form-control" name="suffix" id="suffix" value="{{ $socialService->suffix }}" {{ $socialService->is_voter == 1 ? 'readonly' : '' }}>
                                    <span class="err-message">{{ _lang('Suffix is required.') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Birth date') }}</label>
                                    <input type="text" class="form-control datepicker" name="birth_date" id="birth_date" value="{{ $socialService->birth_date }}">
                                    <span class="err-message">{{ _lang('Birth Date is required.') }}</span>
                                </div>
                            </div>
                    
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Contact Number') }}</label>
                                    <input type="tel" class="form-control telephone" name="contact_number" id="contact_number" value="{{ !empty($socialService->contact_number) ? $socialService->contact_number : '+63' }}" {{ $socialService->is_voter == 1 ? 'readonly' : '' }}>
                                    <span class="err-message">{{ _lang('Contact Number is required.') }}</span>
                                </div>
                            </div>

                            @if($socialService->is_voter == 1)

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Barangay') }}</label>
                                    <input type="text" class="form-control" name="brgy" id="brgy" value="{{ $socialService->brgy }}" {{ $socialService->is_voter == 1 ? 'readonly' : '' }}>
                                    <span class="err-message">{{ _lang('Barangay is required.') }}</span>
                                </div>
                            </div>

                            @else

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Barangay') }}</label>
                                    <select class="form-control select2" name="brgy" {{ $socialService->is_voter == 1 ? 'readonly' : '' }} required>
                                        <option value="">Please select Barangay</option>
                                        @foreach($brgys as $brgy)
                                        <option value="{{$brgy}}" {{ $socialService->brgy == $brgy ? 'selected="selected"' : ''}}>{{$brgy}}</option>
                                        @endforeach
                                    </select>
                                    <span class="err-message">{{ _lang('Barangay is required.') }}</span>
                                </div>
                            </div>
                            
                            @endif

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" >{{ _lang('Address') }}</label>
                                    <input type="text" class="form-control" name="address" id="address" value="{{ $socialService->address }}" {{ $socialService->is_voter == 1 ? 'readonly' : '' }}>
                                    <span class="err-message">{{ _lang('Address is required.') }}</span>
                                </div>
                            </div>
                    
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Organization') }}</label>
                                    <input type="text" class="form-control" name="organization" id="organization" value="{{ $socialService->organization }}">
                                    <span class="err-message">{{ _lang('Organization is required.') }}</span>
                                </div>
                            </div>
                    
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Date File') }}</label>
                                    <input type="text" class="form-control datepicker" name="file_date" id="file_date" value="{{ $socialService->file_date }}" required>
                                    <span class="err-message">{{ _lang('Date File is required.') }}</span>
                                </div>
                            </div>
                    
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Date Processed') }}</label>
                                    <input type="text" class="form-control datepicker" name="processed_date" id="processed_date" value="{{ $socialService->processed_date }}" required>
                                    <span class="err-message">{{ _lang('Date Processed is required.') }}</span>
                                </div>
                            </div>
                    
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Referred By') }}</label>
                                    <input type="text" class="form-control" name="referred_by" id="referred_by" value="{{ $socialService->referred_by }}" required>
                                    <span class="err-message">{{ _lang('Referred By is required.') }}</span>
                                </div>
                            </div>
                    
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Processed By') }}</label>
                                    <input type="text" class="form-control" name="processed_by" id="processed_by" value="{{ $socialService->processed_by }}" required>
                                    <span class="err-message">{{ _lang('Processed By is required.') }}</span>
                                </div>
                            </div>

                            <div class="col-md-4"></div>

                            @can('social_service_status_update')
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Received By') }}</label>
                                    <input type="text" class="form-control" name="received_by" id="received_by" value="{{ $socialService->received_by }}" >
                                    <span class="err-message">{{ _lang('Received By is required.') }}</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Date Received') }}</label>
                                    <input type="text" class="form-control datepicker" name="received_date" id="received_date" value="{{ $socialService->received_date }}">
                                    <span class="err-message">{{ _lang('Date Processed is required.') }}</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Date Released') }}</label>
                                    <input type="text" class="form-control datepicker" name="release_date" id="release_date" value="{{ $socialService->release_date }}">
                                    <span class="err-message">{{ _lang('Date Released is required.') }}</span>
                                </div>
                            </div> 
                            @endcan

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="event_id">Event</label>
                                    <select class="form-control" name="event_id" id="event_id" style="width: 100%;">
                                        @if(!empty($event_id) && !empty($event_name))
                                            <option value="{{ $event_id }}" selected>{{ $event_name }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                        </div>
                        
                        <div class="mb-3"><span class="font-weight-bold">Requestor Information</span> 
                            
                            <span><input type="checkbox" name="same_with_beneficiary" id="same_with_beneficiary" class="ml-5 mr-1" {{ $socialService->requestor_same_to_beneficiary == 1 ? 'checked' : '' }}/>Same with the beneficiary?</span>

                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Last Name') }}</label>
                                    <input type="text" class="form-control" name="requestor_last_name" id="requestor_last_name" value="{{ $socialService->requestor_last_name }}" {{ $socialService->requestor_same_to_beneficiary == 1 ? 'readonly' : '' }} required>
                                    <span class="err-message">{{ _lang('Last Name is required.') }}</span>
                                </div>
                            </div>
                    
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('First Name') }}</label>
                                    <input type="text" class="form-control" name="requestor_first_name" id="requestor_first_name" value="{{ $socialService->requestor_first_name }}" {{ $socialService->requestor_same_to_beneficiary == 1 ? 'readonly' : '' }} required>
                                    <span class="err-message">{{ _lang('First Name is required.') }}</span>
                                </div>
                            </div>
                    
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Middle Name') }}</label>
                                    <input type="text" class="form-control" name="requestor_middle_name" id="requestor_middle_name" value="{{ $socialService->requestor_middle_name }}" {{ $socialService->requestor_same_to_beneficiary == 1 ? 'readonly' : '' }}>
                                    <span class="err-message">{{ _lang('Middle Name is required.') }}</span>
                                </div>
                            </div>
                    
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Suffix') }}</label>
                                    <input type="text" class="form-control" name="requestor_suffix" id="requestor_suffix" value="{{ $socialService->requestor_suffix }}" {{ $socialService->requestor_same_to_beneficiary == 1 ? 'readonly' : '' }}>
                                    <span class="err-message">{{ _lang('Suffix is required.') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Requestor relationship to beneficiary') }}</label>
                                    <input type="text" class="form-control" name="requestor_relationship_to_beneficiary" id="requestor_relationship_to_beneficiary" value="{{ $socialService->requestor_relationship_to_beneficiary }}" >
                                    <span class="err-message">{{ _lang('Relationship is required.') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Remarks') }}</label>
                                    <textarea class="form-control" name="remarks" id="remarks">{{ $socialService->remarks }}</textarea>
                                    <span class="err-message">{{ _lang('Remarks is required.') }}</span>
                                </div>
                            </div>
                            <div class="col-md-3">

                                @can('social_service_status_update')
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Status') }}</label>
                                    <select class="form-control" name="status" id="status" required>

                                        <option value="{{App\SocialServiceAssistance::STATUS_APPROVED}}" {{ $socialService->status == App\SocialServiceAssistance::STATUS_APPROVED ? 'selected="selected"' : ''}}>{{App\SocialServiceAssistance::STATUS_APPROVED}}</option>

                                        <option value="{{App\SocialServiceAssistance::STATUS_ON_HOLD}}" {{ $socialService->status == App\SocialServiceAssistance::STATUS_ON_HOLD ? 'selected="selected"' : ''}}>{{App\SocialServiceAssistance::STATUS_ON_HOLD}}</option>

                                        <option value="{{App\SocialServiceAssistance::STATUS_REJECTED}}" {{ $socialService->status == App\SocialServiceAssistance::STATUS_REJECTED ? 'selected="selected"' : ''}}>{{App\SocialServiceAssistance::STATUS_REJECTED}}</option>

                                        <option value="{{App\SocialServiceAssistance::STATUS_PENDING}}" {{ $socialService->status == App\SocialServiceAssistance::STATUS_PENDING ? 'selected="selected"' : ''}}>{{App\SocialServiceAssistance::STATUS_PENDING}}</option>

                                        <option value="{{App\SocialServiceAssistance::STATUS_RELEASED}}" {{ $socialService->status == App\SocialServiceAssistance::STATUS_RELEASED ? 'selected="selected"' : ''}}>{{App\SocialServiceAssistance::STATUS_RELEASED}}</option>

                                    </select>
                                    <span class="err-message">{{ _lang('Status is required') }}</span>
                                </div>
                                @endcan

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

    var last_control_number = "{{ $socialService->control_number }}";
    var last_request_type = "{{ $socialService->request_type_id }}";
    
    $('#is_voter').on('click', function(e){
        if($(this).is(':checked')){
            $('#precinct').attr('readonly', false);
        }else{
            $('#precinct').attr('readonly', true);
            $('#precinct').val( '' );
        }
    });

    $('#enable_voter_edit').on('click', function(e){
        if($(this).is(':checked')){
            $('#first_name').attr('readonly', false);
            $('#last_name').attr('readonly', false);
            $('#middle_name').attr('readonly', false);
            $('#suffix').attr('readonly', false);
            $('#address').attr('readonly', false);
            $('#brgy').attr('readonly', false);
            $('#contact_number').attr('readonly', false);
            $('#precinct').attr('readonly', false);
        } else {
            $('#first_name').attr('readonly', true);
            $('#last_name').attr('readonly', true);
            $('#middle_name').attr('readonly', true);
            $('#suffix').attr('readonly', true);
            $('#address').attr('readonly', true);
            $('#brgy').attr('readonly', true);
            $('#contact_number').attr('readonly', true);
            $('#precinct').attr('readonly', true);
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
        $('#purpose').html('<option value="">Loading Options... </option>');
        $('#purpose').attr('readonly', true);
        $.get("{{url('tag/get_child_tags')}}/" + request_id, function(data, status){
            if(status == 'success'){
                $('#purpose').html('<option value="">Please select a purpose</option>');
                $.each(data, function(key, value){
                    $('#purpose').append('<option value="'+value.name+'">'+value.name+'</option>');
                });
                $('#purpose').attr('readonly', false);
                $('#purpose').select2();
            }
        });
        if(request_id == last_request_type){
            $('#control_number').val(last_control_number);
        }else{
            $.get("{{url('/social_services/get_current_control_number')}}/" + request_id, function(data, status){
                if(status == 'success'){
                    if(data.length > 0){
                        $.each(data, function(key, value){
                            $('#control_number').val(value);
                        }); 
                    }else{
                        $('#control_number').val('00000001');
                    }
                }
            });
        }
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

