@extends('backend.id_system.layout')

@section('card-header')
<h3 class="card-title">Batch Download (A4 size)</h3>
@endsection

@section('tab-content')

<style>

.download-area{
}

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

<div class="download-area">

@foreach($chunked_id_requests as $key => $id_requests)

<div class="output-area" data-file-name="{{$key}}">
</div>
<div class="paper-area">
    @foreach($id_requests as $id_request)

        @php
            $data = @$id_request['data'];;
            $front = @$id_request['front'];
            $back = @$id_request['back'];
            $front_bg = @$id_request['front_bg'];
            $back_bg = @$id_request['back_bg'];
        @endphp
            
        <div class="card-area" data-file-name="{{$data->name_on_id}}.png" id="id-canvass-{{$data->id}}" data-id="{{$data->id}}">
            <div id="id-markup-{{$data->id}}">
                <img class="id_background" src="{{ !empty($front_bg) ? asset('uploads/templates/'.$front_bg) : asset('images/id_canvass.png') }}" alt="img">
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
                            {!! QrCode::size($width)->generate($data->id_qr_value) !!}
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

@endforeach

</div>


<div class="form-group mt-3">
    <button id="download" class="btn btn-success">Download</button>
</div>

@endsection

@section('custom-css')
<link rel="stylesheet" href="{{ asset('css/print.min.css')}}">
@endsection

@section('js-script')

<script src="{{asset('js/print.min.js')}}"></script>
<script src="{{asset('js/html2canvas.js')}}"></script>
<script src="{{asset('js/jszip.min.js')}}"></script>
<script src="https://unpkg.com/html5-qrcode"></script>

<script>

var is_all_ids_rendered = false;

$(document).ready(function(){

	$(".card-area").each( function () {
		var doc_id = $(this).data("id");
		var doc_file_name = $(this).data("file-name");
		html2canvas(document.querySelector("#id-canvass-" + doc_id), {width: 750}).then(canvas => {
			$(document).find("#id-canvass-" + doc_id).append(canvas);
			$("#id-canvass-" + doc_id + " canvas")
                .attr("data-id", doc_id)
                .attr("data-file-name", doc_file_name)
                .css({width: "1012px", height: "636px"});
			$("#id-markup-" + doc_id).remove();
		});
	});
	
    $(document).on('click', '#download', function(){
        $(".paper-area").each( function () {
            html2canvas(this, {width: 2480}).then(canvas => {
                $(document).find(".output-area").append(canvas);
                $(".paper-area").remove();
                $("#is_all_rendered").val(1);
                downloadZip();
            });
        });     
	});
	
});

function downloadZip(){
    var zip = new JSZip();
    var img = zip.folder("images");

    $(".output-area canvas").each( function () {
        var canv = $(this);
        var imgDataRaw = canv[0].toDataURL("image/png");
        var imgData = imgDataRaw.replace("data:image/png;base64,", "");
        var doc_file_name = $(this).parent().data("file-name");
        img.file(doc_file_name + ".png", imgData, {base64: true});
    });
    
    zip.generateAsync({type:"blob"}).then(function(content) {
        saveAs(content, "{{date('Y-m-d H:i:s')}}-member-ids.zip");
    });
}

</script>

@endsection
