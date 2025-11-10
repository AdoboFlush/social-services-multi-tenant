@extends('backend.id_system.layout')

@section('card-header')
<h3 class="card-title">Batch Preview</h3>
@endsection

@section('tab-content')

<style>

@media print {
  body * {
    visibility: hidden;
  }
  #print-area, #print-area * {
    visibility: visible;
  }
  #print-area {
    position: absolute;
    left: 0;
    top: 0;
	-webkit-print-color-adjust:exact;
	width: 100%;
	margin: 0;
  }
  
  @page {size: landscape}
}

#print-area {
	background-color:#d9d9d9; 
	max-height: 700px; 
	overflow-y:auto;
}


</style>

<div class="container-fluid pb-5 mb-3" id="print-area">
	
	<div class="row mt-2 mb-3">
	
		@foreach($id_requests as $id_request)
		
		<div class="col-md-12">
		
		@php
			$data = @$id_request['data'];
			$front = @$id_request['front'];
			$back = @$id_request['back'];
			$front_bg = @$id_request['front_bg'];
			$back_bg = @$id_request['back_bg'];
		@endphp

		<div class="row">
			<div class="col-md-12">
				<div class="id-canvass" data-id="{{$data->id}}" data-file-name="{{date('Y-m-d')."_".$data->member->full_name}}" style="display: flex;flex-direction: row;">
					
					<div id="id-output-all-{{$data->id}}" class="id-output-double" data-id="{{$data->id}}" data-file-name="{{date('Y-m-d')."_".$data->member->full_name}}">
					</div>
						
					<div id="id-output-{{$data->id}}" class="id-output-single"  style="display: flex;flex-direction: row;">
						<div id="id-output-front-{{$data->id}}" data-id="{{$data->id}}" data-file-name="{{date('Y-m-d')."_".$data->member->full_name}}-front">
						</div>
						<div id="id-output-back-{{$data->id}}" data-id="{{$data->id}}" data-file-name="{{date('Y-m-d')."_".$data->member->full_name}}-back">
						</div>
					</div>
					
					<div id="id-canvass-front-{{$data->id}}" style="position: relative;">
						<img id="id_front_background" src="{{ !empty($front_bg) ? asset('uploads/templates/'.$front_bg) : asset('images/id_canvass.png') }}" alt="img" style="width:750px;">
						@foreach($front as $key => $fields)
							@php
								$addCss = 'white-space:nowrap;';
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
								<div id="qr" class="card-image" style="z-index:1000; position: absolute; {{$addCss}}">
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

					<div id="id-canvass-back-{{$data->id}}" style="position: relative;" class="d-none">
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
			</div>
		</div>
		</div>
		@endforeach
	</div>
</div>

<div class="form-group mt-3">
	 <button id="batch-download" class="btn btn-success">Download All (Single)</button>
	 <!-- <button id="batch-download-double" class="btn btn-warning">Download All (Double)</button> -->
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

$(document).ready(function(){
	
	$(".id-canvass").each( function () {
		var doc_id = $(this).data("id");
		var doc_file_name = $(this).data("file-name");
		
		html2canvas(document.querySelector("#id-canvass-front-" + doc_id), {width: 750}).then(canvas => {
			$(document).find("#id-output-front-" + doc_id).append(canvas);
			$("#id-output-front-" + doc_id + " canvas").attr("data-id", doc_id).attr("data-file-name", doc_file_name + '-front');
			$("#id-canvass-front-" + doc_id).remove();
		});
		
		// html2canvas(document.querySelector("#id-canvass-back-" + doc_id), {width: 750}).then(canvas => {
		// 	$(document).find("#id-output-back-" + doc_id).append(canvas);
		// 	$("#id-output-back-" + doc_id + " canvas").attr("data-id", doc_id).attr("data-file-name", doc_file_name + '-back');
		// 	$("#id-canvass-back-" + doc_id).remove();
		// });
		
		// $("#id-output-all-" + doc_id).hide();
		// html2canvas(document.querySelector("#id-output-" + doc_id), {width: 1500}).then(canvas => {
			// $(document).find("#id-output-all-" + doc_id).append(canvas);
			// $("#id-output-all-" + doc_id + " canvas").attr("data-id", doc_id).attr("data-file-name", doc_file_name);
		// });
		
	});
	
	
    $(document).on('click', '#batch-download', function(){
		
		var zip = new JSZip();
		var img = zip.folder("images");
			
		$(".id-output-single canvas").each( function () {
			var canv = $(this);
			var imgDataRaw = canv[0].toDataURL("image/png");
			var imgData = imgDataRaw.replace("data:image/png;base64,", "");
			var doc_id = $(this).data("id");
			var doc_file_name = $(this).data("file-name");
			img.file(doc_file_name + ".png", imgData, {base64: true});

			$.post('{{ url("id_system/requests/update_download_stats") }}/' + doc_id, {'_token' : '{{csrf_token()}}'}, 
				function( res ) {
					console.log(res);
				});
		});
		
		zip.generateAsync({type:"blob"}).then(function(content) {
			// see FileSaver.js
			saveAs(content, "{{date('Y-m-d H:i:s')}}-member-ids.zip");
		});
		
    });
	
	// $(document).on('click', '#batch-download-double', function(){
		
		// var zip = new JSZip();
		// var img = zip.folder("images");
		
		// $(".id-output-double canvas").each( function () {
			// var canv = $(this);
			// var imgDataRaw = canv[0].toDataURL("image/png");
			// var imgData = imgDataRaw.replace("data:image/png;base64,", "");
			// var doc_id = $(this).data("id");
			// var doc_file_name = $(this).data("file-name");
			// img.file(doc_file_name + ".png", imgData, {base64: true});
		// });
		
		// zip.generateAsync({type:"blob"}).then(function(content) {
			// see FileSaver.js
			// saveAs(content, "{{date('Y-m-d H:i:s')}}-member-ids-double.zip");
		// });
		
	// });
	
	$(document).on('click', '#print-all', function(){
		
	});
	
});

</script>

@endsection
