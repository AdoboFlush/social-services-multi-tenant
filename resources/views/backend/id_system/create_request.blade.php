@extends('backend.id_system.layout')

@section('tab-content')

<div class="bs-stepper linear">
    <div class="bs-stepper-header" role="tablist">
      <!-- your steps here -->
        <div class="step active" data-target="#member-information">
            <button type="button" class="step-trigger" role="tab" aria-controls="member-information" id="member-information-trigger" aria-selected="true">
            <span class="bs-stepper-circle">1</span>
            <span class="bs-stepper-label">Member Information</span>
            </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#id-picture">
            <button type="button" class="step-trigger" role="tab" aria-controls="id-picture" id="id-picture-trigger" aria-selected="false" disabled="disabled">
            <span class="bs-stepper-circle">2</span>
            <span class="bs-stepper-label">Capture ID Picture and Signature</span>
            </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#review">
            <button type="button" class="step-trigger" role="tab" aria-controls="review" id="review-trigger" aria-selected="false" disabled="disabled">
            <span class="bs-stepper-circle">3</span>
            <span class="bs-stepper-label">Review Information</span>
            </button>
        </div>
        </div>
        <div class="bs-stepper-content">
        <form method="post" class="validate" style="width:100%;" autocomplete="off" action="#" enctype="multipart/form-data">
            {{ csrf_field() }}
        <!-- your steps content here -->
            <div id="member-information" class="content active dstepper-block" role="tabpanel" aria-labelledby="member-information-trigger">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">Please enter the member information</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('Last Name') }}</label>
                            <input type="text" class="form-control" name="last_name" id="last_name" value="{{ old('last_name') }}" required>
                            <span class="err-message">{{ _lang('Last Name is required.') }}</span>
                        </div>
                    </div>
            
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('First Name') }}</label>
                            <input type="text" class="form-control" name="first_name" id="first_name" value="{{ old('first_name') }}" required>
                            <span class="err-message">{{ _lang('First Name is required.') }}</span>
                        </div>
                    </div>
            
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('Middle Name') }}</label>
                            <input type="text" class="form-control" name="middle_name" id="middle_name" value="{{ old('middle_name') }}">
                            <span class="err-message">{{ _lang('Middle Name is required.') }}</span>
                        </div>
                    </div>
            
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('Suffix') }}</label>
                            <input type="text" class="form-control" name="suffix" id="suffix" value="{{ old('suffix') }}">
                            <span class="err-message">{{ _lang('Suffix is required.') }}</span>
                        </div>
                    </div>
                    
                </div>
            
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('Date of Birth') }}</label>
                            <input type="text" class="form-control datepicker" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" required>
                            <span class="err-message">{{ _lang('Date Of Birth is required.') }}</span>
                        </div>
                    </div>
            
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="gender">{{ _lang('Gender') }}</label>
                            <select class="form-control select2" name="gender" required>
                                <option value="">Please select Gender</option>
                                <option value="M">Male</option>
                                <option value="F">Female</option>
                            </select>
                            <span class="err-message">{{ _lang('Gender is required') }}</span>
                        </div>
                    </div>
            
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('Address') }}</label>
                            <input type="text" class="form-control" name="address" id="address" value="{{ old('address') }}" required>
                            <span class="err-message">{{ _lang('Address is required.') }}</span>
                        </div>
                    </div>
            
                    
                </div>
            
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('Barangay') }}</label>
                            <select class="form-control select2" name="brgy" required>
                                <option value="">Please select Barangay</option>
                                @foreach($brgys as $brgy)
                                <option value="{{$brgy}}">{{$brgy}}</option>
                                @endforeach
                            </select>
                            <span class="err-message">{{ _lang('Barangay is required') }}</span>
                        </div>
                    </div>
            
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('Alliance') }}</label>
                            <select class="form-control select2" name="alliance">
                                <option value="">Please select Alliance</option>
                                @foreach($alliances as $alliance)
                                <option value="{{$alliance}}">{{$alliance}}</option>
                                @endforeach
                            </select>
                            <span class="err-message">{{ _lang('Alliance is required') }}</span>
                        </div>
                    </div>
            
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('Affiliation') }}</label>
                            <select class="form-control select2" name="affiliation">
                                <option value="">Please select Affiliation</option>
                                @foreach($affiliations as $affiliation)
                                <option value="{{$affiliation}}">{{$affiliation}}</option>
                                @endforeach
                            </select>
                            <span class="err-message">{{ _lang('Affiliation is required') }}</span>
                        </div>
                    </div>
            
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('Beneficiaries') }}</label>
                            <select class="form-control select2" name="beneficiary">
                                <option value="">Please select Beneficiary</option>
                                @foreach($beneficiaries as $beneficiary)
                                <option value="{{$beneficiary}}">{{$beneficiary}}</option>
                                @endforeach
                            </select>
                            <span class="err-message">{{ _lang('Beneficiaries is required') }}</span>
                        </div>
                    </div>
            
                </div>
            
                <div class="row">
            
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('Religion') }}</label>
                            <select class="form-control select2" name="religion">
                                <option value="">Please select Religion</option>
                                @foreach($religions as $religion)
                                <option value="{{$religion}}">{{$religion}}</option>
                                @endforeach
                            </select>
                            <span class="err-message">{{ _lang('Religion is required') }}</span>
                        </div>
                    </div>
            
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('Civil Status') }}</label>
                            <select class="form-control select2" name="civil_status" required>
                                <option value="">Please select Civil Status</option>
                                @foreach($civil_statuses as $civil_status)
                                <option value="{{$civil_status}}">{{$civil_status}}</option>
                                @endforeach
                            </select>
                            <span class="err-message">{{ _lang('Civil Status is required') }}</span>
                        </div>
                    </div>
            
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('Contact Number') }}</label>
                            <input type="tel" class="form-control telephone" name="contact_number" id="contact_number" value="+63">
                            <span class="err-message">{{ _lang('Contact Number is required.') }}</span>
                        </div>
                    </div>
                </div>
            
                <div class="row">
            
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('Remarks') }}</label>
                            <textarea class="form-control" name="remarks" id="remarks" value="{{ old('remarks') }}"></textarea>
                            <span class="err-message">{{ _lang('Remarks is required.') }}</span>
                        </div>
                    </div>
            
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('Precinct') }}</label>
                            <input type="text" class="form-control" name="precinct" id="address" value="{{ old('precinct') }}" required>
                            <span class="err-message">{{ _lang('Precinct is required.') }}</span>
                        </div>
                    </div>
            
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">In case of Emergency, Please contact:</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('Contact Person Last Name') }}</label>
                            <input type="text" class="form-control" name="contact_person_last_name" id="contact_person_last_name" value="{{ old('contact_person_last_name') }}" required>
                            <span class="err-message">{{ _lang('Contact Person Last Name is required.') }}</span>
                        </div>
                    </div>
            
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('Contact Person First Name') }}</label>
                            <input type="text" class="form-control" name="contact_person_first_name" id="contact_person_first_name" value="{{ old('contact_person_first_name') }}" required>
                            <span class="err-message">{{ _lang('Contact Person First Name is required.') }}</span>
                        </div>
                    </div>
            
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('Contact Person Middle Name') }}</label>
                            <input type="text" class="form-control" name="contact_person_middle_name" id="contact_person_middle_name" value="{{ old('contact_person_middle_name') }}">
                            <span class="err-message">{{ _lang('Contact Person Middle Name is required.') }}</span>
                        </div>
                    </div>
            
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('Contact Person Suffix') }}</label>
                            <input type="text" class="form-control" name="contact_person_suffix" id="contact_person_suffix" value="{{ old('contact_person_suffix') }}">
                            <span class="err-message">{{ _lang('Contact Person Suffix is required.') }}</span>
                        </div>
                    </div>
                    
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('Contact Person Address') }}</label>
                            <input type="text" class="form-control" name="contact_person_address" id="contact_person_address" value="" required>
                            <span class="err-message">{{ _lang('Contact Number Address is required.') }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('Contact Number') }}</label>
                            <input type="tel" class="form-control telephone" name="contact_person_number" id="contact_person_number" value="+63">
                            <span class="err-message">{{ _lang('Contact Number is required.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="float-right">
                    <button type="button" class="btn btn-primary" onclick="stepper.next()">Next</button>
                </div>
                
        </div>

        <div id="id-picture" class="content" role="tabpanel" aria-labelledby="id-picture-trigger">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="alert alert-info">
                                    Please choose a profile picture
                                    <button type="button" class="btn btn-warning float-right" data-toggle="modal" data-target="#capture_photo" style="position:absolute;z-index:2; right:10px; top:5px;"><i class="nav-icon fa fa-camera"></i> Capture Photo</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="results" style="border-style:dashed;width:100%;height:auto;padding:5px;">
                                    <img id="snapshot-image" src="{{ asset('images/qc-logo.png') }}" width="100%" height="auto" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="alert alert-info">
                                    Please add signature
                                    <button type="button" class="btn btn-warning float-right" style="position:absolute;z-index:2; right:10px; top:5px;" data-toggle="modal" data-target="#capture_signature"><i class="nav-icon fa fa-edit"></i> Capture Signature</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="captured-signature" style="border-style: dashed; width:100%; min-height:240px;">

                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="col-md-4">
                    </div>
                </div>
            </div>
            <div class="float-right">
                <button type="button" class="btn btn-primary" onclick="stepper.previous()">Previous</button>
                <button type="button" class="btn btn-primary" onclick="stepper.next()">Next</button>
            </div>
        </div>

        <div id="review" class="content" role="tabpanel" aria-labelledby="review-trigger">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">Please review the following information</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-6">
                        <div class="card">
                            <div class="card-header">
                              <h3 class="card-title">ID Preview</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="id-canvass" style="position: relative;">
                                    <img src="{{ asset('images/sample_id.png') }}" alt="Snow" style="width:750px;">
                                    <div style="z-index:1000; font-weight:1000; position: absolute;top: 160px;left: 320px;">HELLO TEST !!! </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                          </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-6">
                    </div>
                </div>
            </div>
            <div class="float-right">
                <button type="button" class="btn btn-primary" onclick="stepper.previous()">Previous</button>
                <button type="button" type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>

    </div>
    </form>

</div>

<div id="capture_photo" class="modal animated bounceInDown" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="max-width:30%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> Capture Photo </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="my_camera"></div>
                            <input type="button" class="btn btn-warning mt-1" value="Take Snapshot" onClick="take_snapshot()">
                            <input type="button" class="btn btn-success mt-1" value="Upload Photo">
                            <input type="hidden" name="image" class="image-tag">
                        </div>
                    </div>
                </div>
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
                                <button type="button" class="btn btn-success" id="save">Save</button>
                                <button type="button" class="btn btn-warning" id="clear">Clear</button>
                                <button type="button" class="btn btn-info" id="clear">Upload Custom Signature</button>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>

// BS-Stepper Init
document.addEventListener('DOMContentLoaded', function () {
    window.stepper = new Stepper(document.querySelector('.bs-stepper'))
})

Webcam.set({
    width: 320,
    height: 240,
    image_format: 'jpeg',
    jpeg_quality: 90
});

Webcam.attach( '#my_camera' );

function take_snapshot() {
    Webcam.snap( function(data_uri) {
        $(".image-tag").val(data_uri);
        $("#snapshot-image").attr('src', data_uri);
    } );
}

var signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
  backgroundColor: 'rgba(255, 255, 255, 0)',
  penColor: 'rgb(0, 0, 0)'
});
var saveButton = document.getElementById('save');
var cancelButton = document.getElementById('clear');

saveButton.addEventListener('click', function(event) {
  var data = signaturePad.toDataURL('image/png');

  // Send data to server instead...
  window.open(data);
});

cancelButton.addEventListener('click', function(event) {
  signaturePad.clear();
});

</script>

@endsection