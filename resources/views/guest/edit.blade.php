@extends('layouts.public')

@section('content')

<div class="container mb-1 mt-1">
    <div class="card card-outline card-success" style="width: 100%; height: auto;">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-8">
                    <h4>{{ _lang('Update Picture and E-Signature') }}</h4>
                </div>
            </div>

            <form method="POST" autocomplete="off" action="{{ route('guest.profile.update', [], false) }}">
                @csrf
                <input type="hidden" name="id_request_id" id="id_request_id" value="{{ $id_request->id }}" />
                <input type="hidden" name="name_on_id" id="name_on_id" value="{{ $id_request->name_on_id }}" />
                <input type="hidden" name="member_id" id="member_id" value="{{$member->id}}" />
                <input type="hidden" name="template_id" id="template_id" value="{{ $id_request->template->id }}" />
                <input type="hidden" name="profile_pic" id="profile_pic" value="" />
                <input type="hidden" name="signature" id="signature" value="" />
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">                   
                            <div class="card-header bg-olive">
                                ID Photo
                            </div>
                            <div class="card-body">
                                <div id="results" style="border-style:dashed;width:100%;height:auto;padding:5px;">
                                    <img id="snapshot-image" src="{{ isset($id_request) && !empty($id_request->profile_pic) ? asset('uploads/profile/'.$id_request->profile_pic) : asset('images/avatar-classic.png') }}" width="100%" height="auto" />
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="button" class="btn btn-warning btn-xs btn-block btn-disabled" data-toggle="modal" data-target="#capture_photo"><i class="nav-icon fa fa-camera mr-2"></i>Take a Photo</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">                         
                            <div class="card-header bg-olive">
                                Signature
                            </div>                      
                            <div class="card-body">
                                <div id="captured-signature" style="border-style: dashed; width:100%; min-height:100px;background-color:#d9d9d9;">
                                    <img id="signature-image" src="{{ isset($id_request) && !empty($id_request->signature) ? asset('uploads/profile/'.$id_request->signature) : asset('images/blank_signature.png')  }}" width="100%" height="auto" />
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="button" class="btn btn-warning btn-xs btn-block" data-toggle="modal" data-target="#capture_signature"><i class="nav-icon fa fa-signature mr-2"></i>Write a signature</button>
                            </div>   
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mt-3">
                    <a class="btn btn-secondary" href="/profile"><i class="nav-icon fa fa-arrow-left"></i> Back </a>
                    <button type="submit" class="btn btn-success float-right"><i class="nav-icon fa fa-edit"></i> Update </button>                
                </div>
            </form>
        </div>

    </div>

</div>

<div id="capture_photo" class="modal animated bounceInDown" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="max-width:95%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> Capture Photo </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="my_camera" class="text-center"></div>
                            <input type="button" class="btn btn-succcess btn-success btn-block mt-2 " value="Capture" onClick="take_snapshot()">
                            <input type="hidden" name="image" class="image-tag">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="capture_signature" class="modal animated bounceInDown" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="max-width:95%;">
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
                        <div class="col-md-12 text-center">
                            <span>Please write your signature here:</span>
                            <div class="wrapper row">
                                <div class="col-12">
                                    <canvas id="signature-pad" class="signature-pad" style="border-style: dashed;" width='max' height='max'></canvas>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-warning btn-block" id="clear">Clear</button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-success btn-block" id="save">Save</button>
                                </div>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

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
        html2canvas(document.querySelector("#id-canvass-back"), {
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

    Webcam.set({
        width: 'auto',
        height: 'auto',
        dest_width: 250,
        dest_height: 250,
        image_format: 'jpeg',
        jpeg_quality: 90
    });

    Webcam.attach('#my_camera');

    function take_snapshot() {
        Webcam.snap(function(data_uri) {
            $(".image-tag").val(data_uri);
            $("#snapshot-image").attr('src', data_uri);
            $("#profile_pic").val(data_uri);
        });
        $('#capture_photo').modal('hide');
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
        $('#capture_signature').modal('hide');
        $("#signature").val(data);
    });

    cancelButton.addEventListener('click', function(event) {
        signaturePad.clear();
    });
</script>
@endsection