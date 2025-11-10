<style>
    .paper-area {
        background-color:#fff; 
        overflow-y:auto;
        width: 2480px;
        height: 3508px;
        padding-left:200px;
        padding-top:100px;
    }
    
    .id_background {
        width: 750px;
        height: auto;
    }
    
    .card-area {
        position: relative;
        display: inline-block;
        margin-right: 1cm;
        margin-bottom:1cm;
        border: 2px solid #000;
    }
    
    .card-text {
        z-index:1000; 
        font-weight:1000; 
        position: absolute;
    }
    
    .card-image {
        z-index:1000;
        position: absolute;
    }
    
</style>

<div class="paper-area" data-file-name="{{$file_name}}" data-last-page={{$last_page}}>
    @foreach($id_requests as $id_request)

        @php
            $data = @$id_request['data'];;
            $front = @$id_request['front'];
            $back = @$id_request['back'];
            $front_bg = @$id_request['front_bg'];
            $back_bg = @$id_request['back_bg'];

            if($part == "back") {
                $bg_img = $back_bg;
                $part_fields = $back;
            } else {
                $bg_img = $front_bg;
                $part_fields = $front;
            }

        @endphp
            
        <div class="card-area" data-file-name="{{$data->name_on_id}}.png" id="id-canvass-{{$data->id}}" data-id="{{$data->id}}">
            <div id="id-markup-{{$data->id}}">
                <img class="id_background" src="{{ !empty($bg_img) ? asset('uploads/templates/'.$bg_img) : asset('images/id_canvass.png') }}" alt="img">
                @foreach($part_fields as $key => $fields)
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
                        <div id="profile_pic" class="card-image" style="{{$addCss}}">
                            <img src="{{ asset('uploads/profile/'.$data->profile_pic) }}" alt="img" style="width:100%;">
                        </div>
                        @endif
                    @elseif($key == 'signature')
                        @if(!empty($data->signature))
                        <div id="signature" class="card-image" style="{{$addCss}}">
                            <img src="{{ asset('uploads/profile/'.$data->signature) }}" alt="img" style="width:100%;">
                        </div>
                        @endif
                    @elseif($key == 'qr')
                        <div id="qr"  class="card-image" style="{{$addCss}}">
                            {!! QrCode::size($width)->generate( $data->id_qr_value ) !!}
                        </div>
                    @elseif($key == 'full_name')
                        <div id="{{ $key }}" class="card-text" style="{{$addCss}}">{{ $data->name_on_id }}</div>
                    @elseif($key == 'id_number')
                        <div id="{{ $key }}" class="card-text" style="{{$addCss}} ">{{ $data->id_number }}</div>
                    @elseif($key == 'address')
                        <div id="{{ $key }}" class="card-text" style="{{$addCss}} ">{{ $data->member->address }}</div>
                    @elseif($key == 'brgy')
                        <div id="{{ $key }}" class="card-text" style="{{$addCss}} ">{{ $data->member->brgy }}</div>
                    @elseif($key == 'birth_date')
                        <div id="{{ $key }}" class="card-text" style="{{$addCss}} ">{{ $data->member->birth_date }}</div>
                    @elseif($key == 'contact_number')
                        <div id="{{ $key }}" class="card-text" style="{{$addCss}} ">{{ $data->member->contact_number }}</div>
                    @elseif($key == 'account_number')
                        <div id="{{ $key }}" class="card-text" style="{{$addCss}} ">{{ $data->member->account_number }}</div>
                    @elseif($key == 'gender')
                        <div id="{{ $key }}" class="card-text" style="{{$addCss}} ">{{ $data->member->gender }}</div>
                    @elseif($key == 'affiliation')
                        <div id="{{ $key }}" class="card-text" style="{{$addCss}} ">{{ $data->member->affiliation }}</div>
                    @elseif($key == 'alliance')
                        <div id="{{ $key }}" class="card-text" style="{{$addCss}} ">{{ $data->member->alliance }}</div>
                    @elseif($key == 'civil_status')
                        <div id="{{ $key }}" class="card-text" style="{{$addCss}} ">{{ $data->member->civil_status }}</div>
                    @elseif($key == 'religion')
                        <div id="{{ $key }}" class="card-text" style="{{$addCss}} ">{{ $data->member->religion }}</div>
                    @elseif($key == 'precinct')
                        <div id="{{ $key }}" class="card-text" style="{{$addCss}} ">{{ $data->member->precinct }}</div>
                    @elseif($key == 'contact_person_full_name')
                        <div id="{{ $key }}" class="card-text" style="{{$addCss}} ">{{ $data->member->contact_person_full_name }}</div>
                    @elseif($key == 'contact_person_number')
                        <div id="{{ $key }}" class="card-text" style="{{$addCss}} ">{{ $data->member->contact_person_number }}</div>
                    @elseif($key == 'contact_person_address')
                        <div id="{{ $key }}" class="card-text" style="{{$addCss}} ">{{ $data->member->contact_person_address }}</div>
                    @else
                        <div id="{{ $key }}" class="card-text" style="{{$addCss}} ">[{{ $key }}]</div>
                    @endif  
                @endforeach
            </div>
        </div>

    @endforeach
</div>
