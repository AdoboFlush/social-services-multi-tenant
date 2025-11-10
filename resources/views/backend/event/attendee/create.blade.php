@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Add New Attendee</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/events/show/'.$event->id)}}">{{"Event ($event->name)"}}</a></li>
                    <li class="breadcrumb-item active">Add New Attendee</li>
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
                    <h3 class="card-title">Add New Attendee for Event: {{ $event->name.' ('.$event->start_at.' - '.substr($event->end_at, 11, 9).')' }}</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form method="post" class="validate" style="width:100%;" autocomplete="off" action="{{ url('/events/attendees/store') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="is_voter" id="is_voter" value="" />
                        <input type="hidden" name="parent_id" id="parent_id" value="" />
                        <input type="hidden" name="event_id" id="event_id" value="{{$event->id}}" />
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Event Name') }} : </label>
                                    <input type="text" class="form-control" value="{{ $event->name.' ('.$event->start_at.' - '.substr($event->end_at, 11, 9).')' }}" id="event_name" readonly>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="control-label">{{ _lang('Search From Voters') }} : </label>
                                <select class="form-control select-2-ajax" id="voter_list">
                                    <option value="">Please select from voter</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label class="control-label">--</label>
                                <button type="button" id="add_voter" class="btn btn-primary form-control">{{ _lang('Add') }}</button>
                            </div>
                            <div class="col-md-1">
                                <label class="control-label">Is a Voter</label>
                                <input type="text" class="form-control" value="No" id="is_voter_label" readonly>
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
                                    <select class="form-control select2" name="gender" id="gender">
                                        <option value="">Please select Gender</option>
                                        <option value="M">Male</option>
                                        <option value="F">Female</option>
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
                                        <option value="{{$brgy}}">{{$brgy}}</option>
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
                                        <option value="{{$alliance}}">{{$alliance}}</option>
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
                                        <option value="{{$affiliation}}">{{$affiliation}}</option>
                                        @endforeach
                                    </select>
                                    <span class="err-message">{{ _lang('Affiliation is required') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Beneficiaries') }}</label>
                                    <select class="form-control select2" name="beneficiary" id="beneficiary">
                                        <option value="">Please select Beneficiary</option>
                                        @foreach($beneficiaries as $beneficiary)
                                        <option value="{{$beneficiary}}">{{$beneficiary}}</option>
                                        @endforeach
                                    </select>
                                    <span class="err-message">{{ _lang('Beneficiaries is required') }}</span>
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
                                        <option value="{{$religion}}">{{$religion}}</option>
                                        @endforeach
                                    </select>
                                    <span class="err-message">{{ _lang('Religion is required') }}</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Civil Status') }}</label>
                                    <select class="form-control select2" name="civil_status" id="civil_status" required>
                                        <option value="">Please select Civil Status</option>
                                        @foreach($civil_statuses as $civil_status)
                                        <option value="{{$civil_status}}">{{$civil_status}}</option>
                                        @endforeach
                                    </select>
                                    <span class="err-message">{{ _lang('Civil Status is required') }}</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Contact Number') }}</label>
                                    <input type="tel" class="form-control telephone" name="contact_number" id="contact_number" value="+63">
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
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-save">{{ _lang('Save') }}</button>
                                    <button type="reset" class="btn btn-danger">{{ _lang('Reset') }}</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js-script')
<script>
    $(document).ready(function() {

        $(".select-2-ajax").select2({
            ajax: {
                url: "{{ url('voter/search') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data, params) {
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
            placeholder: 'Search a Voter',
            minimumInputLength: 4,
        });

        $("#add_voter").on('click', function(e) {
            let voter_id = $("#voter_list").find('option:selected').val();
            $.ajax("{{ url('voter/get') }}/" + voter_id, {
                type: 'GET',
                success: function(data, status, xhr) {
                    if (data.first_name){
                        $('#first_name').val(data.first_name).prop('readonly', true);
                    }       
                    if (data.middle_name){
                        $('#last_name').val(data.last_name).prop('readonly', true);
                    }   
                    if (data.middle_name){
                        $('#middle_name').val(data.middle_name).prop('readonly', true);
                    }         
                    if (data.suffix){
                        $('#suffix').val(data.suffix).prop('readonly', true);
                    }              
                    if (data.birth_date){
                        $('#birth_date').val(data.birth_date).prop('readonly', true);
                    }                   
                    if (data.address){
                        $('#address').val(data.address).prop('readonly', true);
                    }                 
                    if (data.precinct){
                        $('#precinct').val(data.precinct).prop('readonly', true);
                    }      
                    if (data.contact_number){
                        $('#contact_number').val(data.contact_number);
                    }                    
                    if (data.gender){
                        $("#gender").val(data.gender).trigger('change').attr('readonly', true);
                    }                    
                    if (data.brgy){
                        $("#brgy").val(data.brgy).trigger('change').attr('readonly', true);
                    }                    
                    if (data.alliance){
                        $("#alliance").val(data.alliance).trigger('change').attr('readonly', true);
                    }                    
                    if (data.affiliation){
                        $("#affiliation").val(data.affiliation).trigger('change').attr('readonly', true);
                    }                    
                    if (data.beneficiary){
                        $("#beneficiary").val(data.beneficiary).trigger('change').attr('readonly', true);
                    }           
                    if (data.religion){
                        $("#religion").val(data.religion).trigger('change').attr('readonly', true);
                    } 
                    if (data.civil_status){
                        $("#civil_status").val(data.civil_status).trigger('change').attr('readonly', true);
                    }            
                    $("#is_voter").val(1);
                    $("#parent_id").val(data.id);
                    $("#is_voter_label").val("Yes");
                },
                error: function(jqXhr, textStatus, errorMessage) {
                    alert('Voter not found');
                }
            });
        });

    });
</script>
@endsection