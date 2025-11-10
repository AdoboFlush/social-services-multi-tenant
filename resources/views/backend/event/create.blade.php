@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Add New Event</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{url('events')}}">Events</a></li>
                    <li class="breadcrumb-item active">Add New Event</li>
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
                    <h3 class="card-title">Add New Event</h3>
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

                    <form method="post" class="validate" style="width:100%;" autocomplete="off" action="{{ url('events/store') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Event Name') }}</label>
                                    <input type="text" class="form-control" name="event_name" id="event_name" value="{{ old('event_name') }}" required>
                                    <span class="err-message">{{ _lang('Name is required.') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Hosted By') }}</label>
                                    <input type="text" class="form-control" name="hosted_by" id="hosted_by" value="{{ old('hosted_by') }}" required>
                                    <span class="err-message">{{ _lang('Hosted By is required.') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Venue') }}</label>
                                    <input type="text" class="form-control" name="venue" id="venue" value="{{ old('venue') }}" required>
                                    <span class="err-message">{{ _lang('Venue is required.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Start Date') }}</label>
                                    <input type="date" class="form-control" name="start_date" id="start_date" min="" value="{{ old('start_date') }}" required>
                                    <span class="err-message">{{ _lang('Start Date is required.') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Start Time') }}</label>
                                    <input type="time" class="form-control" name="start_time" id="start_time" value="{{ old('start_time') }}" required>
                                    <span class="err-message">{{ _lang('Start Time is required.') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('End Date') }}</label>
                                    <input type="date" class="form-control" name="end_date" id="end_date" value="{{ old('end_date') }}" required>
                                    <span class="err-message">{{ _lang('End Date is required.') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('End Time') }}</label>
                                    <input type="time" class="form-control" name="end_time" id="end_time" value="{{ old('end_time') }}" required>
                                    <span class="err-message">{{ _lang('End Time is required.') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Minimum Attendees') }}</label>
                                    <input type="number" class="form-control" value="0" min="0" max="100000" name="minimum_attendees" id="minimum_attendees" value="{{ old('minimum_attendees') }}" required>
                                    <span class="err-message">{{ _lang('Minimum Attendees is required.') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Maximum Attendees') }}</label>
                                    <input type="number" class="form-control" value="0" min="0" max="100000" name="maximum_attendees" id="maximum_attendees" value="{{ old('maximum_attendees') }}" required>
                                    <span class="err-message">{{ _lang('Maximum Attendees is required.') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Color *</label>
                                    <input type="text" value="{{ old('color') }}" id="color" name="color" class="form-control field-colorpicker colorpicker-element control-font-color" data-colorpicker-id="1" data-original-title="" title="" required>
                                    <span class="err-message">{{ _lang('Color is required.') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Description') }}</label>
                                    <textarea class="form-control" name="description" id="description" value="{{ old('description') }}"></textarea>
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
                                    <select class="form-control select2" name="request_type_id" id="request_type_id">
                                        <option value="">Please select Request type</option>
                                        @foreach($purposes as $purpose)
                                        <option value="{{$purpose->id}}">{{$purpose->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Purpose') }}</label>
                                    <select class="form-control select2" name="purpose[]" id="purpose" multiple="multiple" disabled>
                                        <option value="">Please select Purpose</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Amount') }}</label>
                                    <input type="number" class="form-control" name="amount" id="amount" value="{{ old('amount') }}">
                                </div>
                            </div>

                        </div>
                        <hr />

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
    $(function() {
        $('.field-colorpicker').colorpicker();

        $('#request_type_id').on('change', function(e){
            let request_id = $(this).val();
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
        });

        var dateToday = new Date();
        var month = dateToday.getMonth() + 1;
        var day = dateToday.getDate();
        var year = dateToday.getFullYear();

        if (month < 10)
            month = '0' + month.toString();
        if (day < 10)
            day = '0' + day.toString();

        var min_date = year + '-' + month + '-' + day;

        // to delete after they encode event history
        min_date ='2024-01-01';
        
        $('#start_date').attr('min', min_date);        
        $('#end_date').attr('min', min_date);

        $('#start_date').on('change', function(e){
            let start_date = e.target.value;
            $('#end_date').attr('min', start_date);
        });
    });

</script>

@endsection