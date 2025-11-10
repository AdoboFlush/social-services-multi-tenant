@extends('layouts.public')

@section('content')
<div class="container mb-1 mt-1">    
    <div class="card card-outline card-success" style="width: 100%; height: auto;">
        <div class="card-body">
            @if(isset($event_count) && $event_count["ongoing"] > 0)
                <div class="info-box bg-gradient-danger">
                    <span class="info-box-icon"><i class="far fa-calendar-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><strong>You are invited!</strong></span>
                        <a href="#" class="ajax-modal text-sm" data-title="{{ _lang('Events')}}" data-href="{{ route('guest.profile.event') }}" >
                            <i class="fa fa-plus-circle"></i> More info
                        </a>
                    </div>
                </div> 
            @endif

            @if(isset($event_count) &&  $event_count["ongoing"] == 0 && $event_count["upcoming"] > 0) 
                <div class="info-box bg-gradient-warning">
                    <span class="info-box-icon"><i class="far fa-calendar-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><strong>You are invited!</strong></span>
                        <a href="#" class="ajax-modal text-sm" data-title="{{ _lang('Events')}}" data-href="{{ route('guest.profile.event') }}" >
                            <i class="fa fa-plus-circle"></i> More info
                        </a>
                    </div>
                </div>   
            @endif

            <div class="row mb-2">
                <div class="col-8">
                    <h4>{{ _lang('Member Profile') }}</h4>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mt text-sm">Name</div>
                <div class="col-12"><strong>{{strtoupper($member->last_name)}} , {{strtoupper($member->first_name)}} {{strtoupper($member->middle_name)}} {{strtoupper($member->suffix)}} </strong></div>

                <div class="col-12 mt text-sm">Date of birth</div>
                <div class="col-12"><strong>{{$member->birth_date}} </strong></div>

                <div class="col-12 mt text-sm">Gender</div>
                <div class="col-12"><strong>{{$member->gender ? strtoupper($member->gender) : '-'}} </strong></div>

                <div class="col-12 mt text-sm">Marital Status</div>
                <div class="col-12"><strong>{{$member->civil_status ? strtoupper($member->civil_status) :'-'}} </strong></div>

                <div class="col-12 mt text-sm">Address</div>
                <div class="col-12"><strong>{{strtoupper($member->address)}} {{strtoupper($member->brgy)}} {{strtoupper($member->area)}}</strong></div>
            </div>

            <hr></hr>

            <div class="row mb-2">
                <div class="col-8">
                    <h5>{{ _lang('Member ID') }}</h5>
                </div>
            </div>
            @if(isset($id_request))
            <div class="col-md-12">
                <a class="btn btn-block btn-success btn-sm ajax-modal" href="#" data-title="{{ _lang('Member ID') }}" data-href="{{ route('guest.profile.id', $id_request->id) }}">Show Member ID</a>
                @if($template->allowed_user_update)
                <a class="btn btn-block btn-primary btn-sm" href="{{ route('guest.profile.edit') }}">Update ID</a>
                @endif
            </div>
            @else
            <div class="alert alert-warning col-md-12 text-center mt-2">
                No Member ID created yet. Create your member ID by clicking <a href="{{route('guest.profile.create')}}"><strong> here </strong></a>
            </div>
            @endif

            <hr></hr>

            <div class="row mb-2">
                <div class="col-8">
                    <h5>{{ _lang('Assistance') }}</h5>
                </div>
            </div>
            <div class="col-md-12">
                <a class="btn btn-block btn-info btn-sm ajax-modal mt-2" href="#" data-title="{{ _lang('Assistance History')}}" data-href="{{ route('guest.profile.assistance') }}">Show Assistance History</a>
            </div>

            <hr></hr>

            <div class="row mb-2">
                <div class="col-8">
                    <h5>{{ _lang('Event') }}</h5>
                </div>
            </div>
            <div class="col-md-12">
                <a class="btn btn-block btn-info btn-sm ajax-modal mt-2" href="#" data-title="{{ _lang('Event History')}}" data-href="{{ route('guest.profile.event') }}">Show Event History</a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js-script')

<script>

	 $(document).on('click', '#download-front', function() {
        html2canvas(document.querySelector("#id-canvass-front"), {
            width: 750
        }).then(canvas => {
            document.querySelector("#id-output-front").appendChild(canvas);
            let canv = $("#id-output-front canvas");    
            let img = canv[0].toDataURL("image/png");
            let link = document.createElement('a');
            link.download = $("#download-front").data('file-name');
            link.href = img;
            link.click();
            $("#id-output-front").hide();
        });
    });

    $(document).on('click', '#download-back', function() {
        html2canvas(document.querySelector("#id-canvass-back"), 
            width: 750
        }).then(canvas => {
            document.querySelector("#id-output-back").appendChild(canvas);
            let canv = $("#id-output-back canvas");
            let img = canv[0].toDataURL("image/png");
            let link = document.createElement('a');
            link.download = $("#download-back").data('file-name');
            link.href = img;
            link.click();
            $("#id-output-back").hide();
        });
    });
	
</script>

@endsection