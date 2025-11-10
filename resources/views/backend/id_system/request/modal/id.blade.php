@extends('layouts.default');

@section('content')

<div class="container" style="padding-left:5rem;">

    <div class="id-canvass-front" style="position: relative; margin-bottom:2rem;">

        <img id="id_front_background" src="{{ !empty($front_bg) ? asset('uploads/templates/'.$front_bg) : asset('images/id_canvass.png') }}" alt="img" style="width:750px; border:solid;">
        @foreach($front as $key => $fields)
            @php
                $addCss = 'white-space:nowrap;';
                $width = 0;
                if($key == 'bg') continue;
                if(is_array($fields)){
                    foreach($fields as $name => $prop){
                        if(in_array($key, ['profile_pic','signature','qr'])){
                            $addCss .= "$name:$prop;";
                            if($name == "width") {
                                $width = intval(str_replace("px", "",$prop));
                            }
                        }else{
                            if(!in_array($name, ['inset','width','height'])){
                                $addCss .= "$name:$prop;";
                            }
                        }
                    }
                }
            @endphp
    
            @if($key == 'profile_pic')
                @if(!empty($data->profile_pic))
                <div id="profile_pic" style="z-index:1000; position: absolute; {{$addCss}}">
                    <img src="{{ asset('uploads/profile/'.$data->profile_pic) }}" alt="img" style="width:100%;">
                </div>
                @endif
            @elseif($key == 'signature')
                @if(!empty($data->signature))
                <div id="signature" style="z-index:1000; position: absolute; {{$addCss}}">
                    <img src="{{ asset('uploads/profile/'.$data->signature) }}" alt="img" style="width:100%;">
                </div>
                @endif
            @elseif($key == 'qr')
                <div id="qr" style="z-index:1000; position: absolute;  {{$addCss}}">
                    {!! QrCode::size($width)->generate($data->id_qr_value) !!}
                </div>
            @elseif($key == 'full_name')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->name_on_id }}</div>
            @elseif($key == 'id_number')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->id_number }}</div>
            @elseif($key == 'address')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->address }}</div>
            @elseif($key == 'brgy')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->brgy }}</div>
            @elseif($key == 'birth_date')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->birth_date }}</div>
            @elseif($key == 'contact_number')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->contact_number }}</div>
            @elseif($key == 'account_number')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->account_number }}</div>
            @elseif($key == 'gender')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->gender }}</div>
            @elseif($key == 'affiliation')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->affiliation }}</div>
            @elseif($key == 'alliance')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->alliance }}</div>
            @elseif($key == 'civil_status')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->civil_status }}</div>
            @elseif($key == 'religion')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->religion }}</div>
            @elseif($key == 'precinct')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->precinct }}</div>
            @elseif($key == 'contact_person_full_name')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->contact_person_full_name }}</div>
            @elseif($key == 'contact_person_number')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->contact_person_number }}</div>
            @elseif($key == 'contact_person_address')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->contact_person_address }}</div>
            @else
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">[{{ $key }}]</div>
            @endif
            
        @endforeach
    </div>
    
    <div class="id-canvass-back" style="position: relative;">
        <img id="id_back_background" src="{{ !empty($back_bg) ? asset('uploads/templates/'.$back_bg) : asset('images/id_canvass.png') }}" alt="img" style="width:750px; border:solid;">
        
        @foreach($back as $key => $fields)
            @php
                $addCss = 'white-space:nowrap;';
                $width = 0;
                if($key == 'bg') continue;
                if(is_array($fields)){
                    foreach($fields as $name => $prop){
                        if(in_array($key, ['profile_pic','signature','qr'])){
                            $addCss .= "$name:$prop;";
                            if($name == "width") {
                                $width = intval(str_replace("px", "",$prop));
                            }
                        }else{
                            if(!in_array($name, ['inset','width','height'])){
                                $addCss .= "$name:$prop;";
                            }
                        }
                    }
                }
            @endphp
    
            @if($key == 'profile_pic')
                <div id="profile_pic" style="z-index:1000; position: absolute; {{$addCss}}">
                    <img src="{{ asset('uploads/profile/'.$data->profile_pic) }}" alt="img" style="width:100%;">
                </div>
            @elseif($key == 'signature')
                <div id="signature" style="z-index:1000; position: absolute; {{$addCss}}">
                    <img src="{{ asset('uploads/profile/'.$data->signature) }}" alt="img" style="width:100%;">
                </div>
            @elseif($key == 'qr')
                <div id="qr" style="z-index:1000; position: absolute; {{$addCss}}">
                    {!! QrCode::size($width)->generate($data->id_qr_value) !!}
                </div>
            @elseif($key == 'full_name')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->name_on_id }}</div>
            @elseif($key == 'id_number')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->id_number }}</div>
            @elseif($key == 'address')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->address }}</div>
            @elseif($key == 'brgy')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->brgy }}</div>
            @elseif($key == 'birth_date')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->birth_date }}</div>
            @elseif($key == 'contact_number')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->contact_number }}</div>
            @elseif($key == 'account_number')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->account_number }}</div>
            @elseif($key == 'gender')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->gender }}</div>
            @elseif($key == 'affiliation')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->affiliation }}</div>
            @elseif($key == 'alliance')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->alliance }}</div>
            @elseif($key == 'civil_status')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->civil_status }}</div>
            @elseif($key == 'religion')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->religion }}</div>
            @elseif($key == 'precinct')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->precinct }}</div>
            @elseif($key == 'contact_person_full_name')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->contact_person_full_name }}</div>
            @elseif($key == 'contact_person_number')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->contact_person_number }}</div>
            @elseif($key == 'contact_person_address')
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->contact_person_address }}</div>
            @else
                <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">[{{ $key }}]</div>
            @endif
            
        @endforeach
        
    </div>

</div>

@endsection
