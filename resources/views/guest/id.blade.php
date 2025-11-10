<div class="container card">
    <div class="card-body">
        <div class="row">
            <div class="col-8"><p>ID #: {{ $data->id_number }}</p> </div>
            <div class="col-4"></div>
        </div>
        <div class="row text-center">
            <div class="col-6">
                <button 
                    id="download-front" 
                    class="btn btn-info btn-sm" 
                    data-file-name="{{ date('Y-m-d')."_".$data->member->full_name }}-front.png"
                    type="button"
                >
                    <i class="fa fa-download"></i>
                    Download Front
                </button>
            </div>
            <div class="col-6"> 
                <button 
                    id="download-back" 
                    class="btn btn-warning btn-sm" 
                    data-file-name="{{ date('Y-m-d')."_".$data->member->full_name }}-back.png" 
                    type="button"
                >
                    <i class="fa fa-download"></i>
                    Download Back
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5 mb-3" style="background-color:#d9d9d9; overflow-y:auto;">
     <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div id="id-output-all"></div>
            <div id="id-canvass-all">
                <div id="id-output-front"></div>
                <div id="id-canvass-front" class="mb-5" style="position: relative;">
                    <img id="id_front_background" src="{{ !empty($front_bg) ? asset('uploads/templates/'.$front_bg) : asset('images/id_canvass.png') }}" alt="img" style="width:750px;">
                    @foreach($front as $key => $fields)
                        @php
                            $addCss = '';
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
								@if(!empty($data->profile_pic))
                                <img src="{{ asset('uploads/profile/'.$data->profile_pic) }}" alt="img" style="width:100%;">
								@endif
                            </div>
                        @elseif($key == 'signature')
                            @if(!empty($data->signature))
                            <div id="signature" style="z-index:1000; position: absolute; {{$addCss}}">
                                <img src="{{ asset('uploads/profile/'.$data->signature) }}" alt="img" style="width:100%;">
                            </div>
                            @endif
                        @elseif($key == 'qr')
                            <div id="qr" style="z-index:1000; position: absolute;  {{$addCss}}">
                                {!! QrCode::size($width)->generate( $data->id_qr_value ) !!}
                            </div>
                        @elseif($key == 'full_name')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; width : 90%; {{$addCss}} ">{{ strtoupper($data->name_on_id) }}</div>
                        @elseif($key == 'id_number')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->id_number }}</div>
                        @elseif($key == 'address')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; width : 90%; {{$addCss}} ">{{ strtoupper($data->member->address) }}</div>
                        @elseif($key == 'brgy')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; width : 90%; {{$addCss}} ">{{ strtoupper($data->member->brgy) }}</div>
                        @elseif($key == 'birth_date')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->birth_date }}</div>
                        @elseif($key == 'contact_number')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->contact_number }}</div>
                        @elseif($key == 'account_number')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->account_number }}</div>
                        @elseif($key == 'gender')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ strtoupper($data->member->gender) }}</div>
                        @elseif($key == 'affiliation')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ strtoupper($data->member->affiliation) }}</div>
                        @elseif($key == 'alliance')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ strtoupper($data->member->alliance) }}</div>
                        @elseif($key == 'civil_status')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ strtoupper($data->member->civil_status) }}</div>
                        @elseif($key == 'religion')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ strtoupper($data->member->religion) }}</div>
                        @elseif($key == 'precinct')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ strtoupper($data->member->precinct) }}</div>
                        @elseif($key == 'contact_person_full_name')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ strtoupper($data->member->contact_person_full_name) }}</div>
                        @elseif($key == 'contact_person_number')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->contact_person_number }}</div>
                        @elseif($key == 'contact_person_address')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ strtoupper($data->member->contact_person_address) }}</div>
                        @else
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">[{{ $key }}]</div>
                        @endif
                        
                    @endforeach
                </div>

                <div id="id-output-back"></div>
                <div id="id-canvass-back" class="mb-5" style="position: relative;">
                    <img id="id_back_background" src="{{ !empty($back_bg) ? asset('uploads/templates/'.$back_bg) : asset('images/id_canvass.png') }}" alt="img" style="width:750px;">
                    
                    @foreach($back as $key => $fields)
                        @php
                            $addCss = '';
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
                            <div id="qr" style="z-index:1000; position: absolute;  {{$addCss}}">
                                {!! QrCode::size($width)->generate( $data->id_qr_value ) !!}
                            </div>
                        @elseif($key == 'full_name')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ strtoupper($data->name_on_id )}}</div>
                        @elseif($key == 'id_number')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->id_number }}</div>
                        @elseif($key == 'address')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ strtoupper($data->member->address) }}</div>
                        @elseif($key == 'brgy')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; width : 90%; {{$addCss}} ">{{ strtoupper($data->member->brgy) }}</div>
                        @elseif($key == 'birth_date')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->birth_date }}</div>
                        @elseif($key == 'contact_number')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->contact_number }}</div>
                        @elseif($key == 'account_number')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->account_number }}</div>
                        @elseif($key == 'gender')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ strtoupper($data->member->gender) }}</div>
                        @elseif($key == 'affiliation')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ strtoupper($data->member->affiliation) }}</div>
                        @elseif($key == 'alliance')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ strtoupper($data->member->alliance) }}</div>
                        @elseif($key == 'civil_status')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ strtoupper($data->member->civil_status) }}</div>
                        @elseif($key == 'religion')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ strtoupper($data->member->religion) }}</div>
                        @elseif($key == 'precinct')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ strtoupper($data->member->precinct) }}</div>
                        @elseif($key == 'contact_person_full_name')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ strtoupper($data->member->contact_person_full_name) }}</div>
                        @elseif($key == 'contact_person_number')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ $data->member->contact_person_number }}</div>
                        @elseif($key == 'contact_person_address')
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">{{ strtoupper($data->member->contact_person_address) }}</div>
                        @else
                            <div id="{{ $key }}" style="z-index:1000; font-weight:1000; position: absolute; {{$addCss}} ">[{{ $key }}]</div>
                        @endif
                        
                    @endforeach

                </div>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>
