@extends('backend.id_system.layout')

@section('card-header')
<h3 class="card-title">Create a Request</h3>
@endsection

@section('tab-content')

<form method="post" style="width:100%;" autocomplete="off" action="{{ url('id_system/requests/store') }}" enctype="multipart/form-data">
    
    <input type="hidden" name="profile_pic" id="profile_pic" value="" />
    <input type="hidden" name="signature" id="signature" value="" />
    <input type="hidden" name="member_id" id="member_id" value="" />
    {{ csrf_field() }}

    <div class="row">
        
        <div class="col-md-4">

            <div class="alert alert-info">
                ID Information and Settings
            </div>
            
            <div class="card">
                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-md-10">
                            <label class="control-label">{{ _lang('Member') }} : </label>
                            <select class="form-control select-2-ajax" id="member_list">
                                <option value="">Please select from Member</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="control-label">--</label>
                            <button type="button" id="add_member" class="btn btn-primary form-control">{{ _lang('Add') }}</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{ _lang('Name will appear on ID') }}</label>
                        <input type="text" class="form-control" name="name_on_id" id="name_on_id" value="{{ old('name_on_id') }}" required>
                        <span class="err-message">{{ _lang('Name on ID is required.') }}</span>
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{ _lang('ID Card Template') }}</label>
                        <select class="form-control select2" name="template_id" required>
                            <option value="">Please select a Template</option>
                            @foreach($templates as $template)
                            <option value="{{$template->id}}">{{$template->name}}</option>
                            @endforeach
                        </select>
                        <span class="err-message">{{ _lang('ID Card Template is required') }}</span>
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{ _lang('Remarks') }}</label>
                        <textarea class="form-control" name="remarks" id="remarks" value="{{ old('remarks') }}"></textarea>
                        <span class="err-message">{{ _lang('Remarks is required.') }}</span>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="alert alert-info">
                Please choose a profile picture
                <button type="button" class="btn btn-warning float-right" data-toggle="modal" data-target="#capture_photo" style="position:absolute;z-index:2; right:55px; top:5px;"><i class="nav-icon fa fa-camera"></i> Capture Photo</button>
				<button type="button"  id="remove_profile_pic" class="btn btn-danger float-right" style="position:absolute;z-index:2; right:10px; top:5px;"><i class="nav-icon fa fa-trash"></i></button>
			</div>
            <div class="card">
                <div class="card-body">
                    <div id="results" style="border-style:dashed;width:100%;height:auto;padding:5px;min-height:240px;background-color:#d9d9d9;">
                        <img id="snapshot-image" src="" width="100%" height="auto" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="alert alert-info">
                Please add signature
                <button type="button" class="btn btn-warning float-right" style="position:absolute;z-index:2; right:55px; top:5px;" data-toggle="modal" data-target="#capture_signature"><i class="nav-icon fa fa-edit"></i> Capture Signature</button>
				<button type="button" id="remove_signature" class="btn btn-danger float-right" style="position:absolute;z-index:2; right:10px; top:5px;"><i class="nav-icon fa fa-trash"></i></button>
			</div>
            <div class="card">
                <div class="card-body">
                    <div id="captured-signature" style="border-style: dashed; width:100%; min-height:240px;background-color:#d9d9d9;">
                        <img id="signature-image" src="" width="100%" height="auto" />
                    </div>
                </div>
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

<div id="capture_photo" class="modal animated bounceInDown" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="max-width:20%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> Capture Photo </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- <div style="position:relative; left: 5%; top: 10%;"> --}}
                    <div id="my_camera"></div>
                {{-- </div> --}}
                <button type="button" class="btn btn-block btn-warning mt-1" value="Take Snapshot" onClick="take_snapshot()">
                    <i class="nav-icon fa fa-camera"></i> Take Snapshot
                </button>
                <input type="hidden" name="image" class="image-tag">
            </div>
        </div>
    </div>
</div>

<div id="capture_signature" class="modal animated bounceInDown" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="max-width:30%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> Capture Signature </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <span>Please write your signature here:</span>
                            <div class="wrapper">
                                <canvas id="signature-pad" class="signature-pad" style="border-style: dashed;" width=500 height=220></canvas>
                                <button type="button" class="btn btn-success" id="save">
                                    <i class="nav-icon fa fa-edit"></i> Save Signature
                                </button>
                                <button type="button" class="btn btn-warning" id="clear">Clear</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js-script')

<script src="{{asset('adminLTE/plugins/bs-stepper/js/bs-stepper.min.js')}}"></script>
<script src="{{asset('js/webcam.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>

$(document).ready(function () {
    
    $(".select-2-ajax").select2({
        ajax: {
            url: "{{ url('id_system/members/search') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
            return {
                q: params.term, // search term
                page: params.page
            };
            },
            processResults: function (data, params) {
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
        placeholder: 'Search from Members',
        minimumInputLength: 4,
    });

    $("#add_member").on('click', function(e){
        let member_id = $("#member_list").find('option:selected').val();
        $.ajax("{{ url('id_system/members/get') }}/" + member_id, {
            type: 'GET',
            success: function (data, status, xhr) {
                $('#member_id').val(data.id).prop('readonly', true);
                $('#name_on_id').val(data.full_name);
            },
            error: function (jqXhr, textStatus, errorMessage) {
                alert('Member not found');
            }
        });
    });

});

Webcam.set({
    width: 320,
    height: 240,
    dest_width: 320,
    dest_height: 240,
    image_format: 'jpeg',
    jpeg_quality: 200
});
Webcam.attach( '#my_camera' );

$('#remove_profile_pic').on('click', function(e){
	$(".image-tag").val("");
	$('#snapshot-image').attr('src', "");
	$("#profile_pic").val("");
});

$('#remove_signature').on('click', function(e){
	$('#signature-image').attr('src', "");
	$("#signature").val("");
	$('#capture_signature').modal('hide');
});

function take_snapshot() {
    Webcam.snap( function(data_uri) {
        $(".image-tag").val(data_uri);
        $("#snapshot-image").attr('src', data_uri);
        $("#profile_pic").val(data_uri);
    } );
    $('#capture_photo').modal('hide');
    crop();
}

var signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
  backgroundColor: 'rgba(255, 255, 255, 0)',
  penColor: 'rgb(0, 0, 0)'
});
var saveButton = document.getElementById('save');
var cancelButton = document.getElementById('clear');

saveButton.addEventListener('click', function(event) {
  var data = signaturePad.toDataURL('image/png');
  $('#signature-image').attr('src', data);
  $("#signature").val(data);
  $('#capture_signature').modal('hide');
});

cancelButton.addEventListener('click', function(event) {
  signaturePad.clear();
});

function cropImage(imgUri, width = 400, height = 300, xstart=0, ystart=0, callback) {
    try {
        let resize_canvas = document.createElement('canvas');
        let orig_src = new Image();
        orig_src.src = imgUri;
        orig_src.onload = function () {
            resize_canvas.width = width;
            resize_canvas.height = height;
            let cnv = resize_canvas.getContext('2d');
            cnv.drawImage(orig_src, xstart, ystart, width, height, 0, 0, width, height);
            let newimgUri = resize_canvas.toDataURL("image/png").toString();
            callback(newimgUri);
        }
    }
    catch (e) {
        console.log("Couldn't crop image due to", e);
        window.alert("Couldn't crop image due to", e);
        callback(imgUri);
    }
}

function crop() {
    croppedImageString = $('#snapshot-image').attr('src');
    cropImage(croppedImageString, 240, 240, 40, 0, (imgUri) => {
        $(".image-tag").val(imgUri);
        $('#snapshot-image').attr('src', imgUri);
        $("#profile_pic").val(imgUri);
    });
}

</script>

@endsection