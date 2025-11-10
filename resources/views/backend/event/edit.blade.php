@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Event</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{url('events')}}">Events</a></li>
                    <li class="breadcrumb-item active">Edit Event</li>
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
                    <h3 class="card-title">Edit Event</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">

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

                    <form method="post" class="validate" style="width:100%;" autocomplete="off" action="{{ url('events/update') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <input type="hidden" name="update_id" value="{{ $data->id }}">

                        <div class="mb-3 font-weight-bold">Event Information
                            <span><input type="checkbox" name="is_active" id="is_active" class="ml-5 mr-1" {{ $data->active == 1 ? 'checked' : '' }} />Is Active</span>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Event Name') }}</label>
                                    <input type="text" class="form-control" name="event_name" id="event_name" value="{{ $data->name }}" required>
                                    <span class="err-message">{{ _lang('Name is required.') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Hosted By') }}</label>
                                    <input type="text" class="form-control" name="hosted_by" id="hosted_by" value="{{ $data->hosted_by }}" required>
                                    <span class="err-message">{{ _lang('Hosted By is required.') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Venue') }}</label>
                                    <input type="text" class="form-control" name="venue" id="venue" value="{{ $data->venue }}" required>
                                    <span class="err-message">{{ _lang('Venue is required.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Start Date') }}</label>
                                    <input type="date" class="form-control" name="start_date" id="start_date" value="{{ substr($data->start_at,0, 10) }}" required>
                                    <span class="err-message">{{ _lang('Start Date is required.') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Start Time') }}</label>
                                    <input type="time" class="form-control" name="start_time" id="start_time" value="{{ substr($data->start_at, 11, 9) }}" required>
                                    <span class="err-message">{{ _lang('Start Time is required.') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('End Date') }}</label>
                                    <input type="date" class="form-control" name="end_date" id="end_date" value="{{ substr($data->end_at,0, 10) }}" required>
                                    <span class="err-message">{{ _lang('End Date is required.') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('End Time') }}</label>
                                    <input type="time" class="form-control" name="end_time" id="end_time" value="{{ substr($data->end_at, 11, 9) }}" required>
                                    <span class="err-message">{{ _lang('End Time is required.') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Minimum Attendees') }}</label>
                                    <input type="number" class="form-control" value="{{ $data->minimum_attendees }}" min="0" max="100000" name="minimum_attendees" id="minimum_attendees" value="{{ old('minimum_attendees') }}" required>
                                    <span class="err-message">{{ _lang('Minimum Attendees is required.') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Maximum Attendees') }}</label>
                                    <input type="number" class="form-control" value="{{ $data->maximum_attendees }}" min="0" max="100000" name="maximum_attendees" id="maximum_attendees" value="{{ old('maximum_attendees') }}" required>
                                    <span class="err-message">{{ _lang('Maximum Attendees is required.') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Color:</label>
                                    <input type="text" value="{{ $data->color }}" id="color" name="color" class="form-control field-colorpicker colorpicker-element control-font-color" data-colorpicker-id="1" data-original-title="" title="">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Description') }}</label>
                                    <textarea class="form-control" name="description" id="description">{{ $data->description }}</textarea>
                                    <span class="err-message">{{ _lang('Description is required.') }}</span>
                                </div>
                            </div>

                        </div>

                        <hr />
                        <div class="row">
                            <div class="mt-2 col-md-3">
                                <div><h4>Social Service Assistance</h4></div>
                                <p>Create 'Social Service Assistance' for attendees.</p>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Request Type') }}</label>
                                    <select class="form-control select2" name="request_type_id" id="request_type_id" >
                                        <option value="">Please select Request type</option>
                                        @if($purposes)
                                            @foreach($purposes as $purpose)
                                                <option value="{{$purpose->id}}" {{ $data->request_type_id == $purpose->id ? 'selected="selected"' : ''}}>{{$purpose->name}}</option>
                                            @endforeach
                                        @endif                                      
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Purpose') }}</label>
                                    <select class="form-control select2" name="purpose[]" id="purpose" multiple="multiple">
                                        @php
                                            $selected_purposes = json_decode($data->purpose, true);
                                        @endphp
                                        @if($selected_purposes)
                                            @foreach($selected_purposes as $selected_purpose)
                                                <option value="{{$selected_purpose}}" selected>{{$selected_purpose}}</option>
                                            @endforeach
                                        @endif                                  
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Amount') }}</label>
                                    <input type="number" class="form-control" name="amount" id="amount" value="{{ $data->amount }}">
                                </div>
                            </div>

                        </div>
                        <hr />

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-save" id="btn_save">{{ _lang('Save') }}</button>
                                    <button type="reset" class="btn btn-danger" id="btn_reset">{{ _lang('Reset') }}</button>
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
    $(function() {
        $('.field-colorpicker').colorpicker();

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
        });
        
        var date_today = new Date();
        var month = date_today.getMonth() + 1;
        var day = date_today.getDate();
        var year = date_today.getFullYear();

        if (month < 10)
            month = '0' + month.toString();
        if (day < 10)
            day = '0' + day.toString();

        var min_date = year + '-' + month + '-' + day;

        let event_start_date = document.getElementById('start_date').value;
        let event_end_date = document.getElementById('end_date').value;

        if (min_date > event_start_date){
            console.log('past start')
            //$('#start_date').attr('disabled', true);
            //$('#start_time').attr('disabled', true);
        }

        if (min_date > event_end_date){
            //$('#end_date').attr('disabled', true);
            //$('#end_time').attr('disabled', true);

            // $('#event_name').attr('readonly', true);
            // $('#hosted_by').attr('readonly', true);
            // $('#venue').attr('readonly', true);
            // $('#minimum_attendees').attr('readonly', true);
            // $('#maximum_attendees').attr('readonly', true);
            // $('#color').attr('readonly', true);
            // $('#description').attr('readonly', true);

            $('#request_type_id').attr('readonly', true);
            // $('#purpose').attr('readonly', true);
            // $('#amount').attr('readonly', true);

            // $('#btn_save').attr('disabled', true);
            $('#btn_reset').attr('readonly', true);
        }

        // $('#start_date').attr('min', min_date);
        // $('#end_date').attr('min', min_date);

        $('#start_date').on('change', function(e){
            let start_date = e.target.value;
            $('#end_date').attr('min', start_date);
        });
    });
</script>

@endsection