@extends('backend.id_system.layout')

@section('card-header')
<h3 class="card-title">Edit a Member</h3>
@endsection

@section('tab-content')

<form method="post" style="width:100%;" autocomplete="off" action="{{ url('id_system/members/update') }}" enctype="multipart/form-data">
    {{ csrf_field() }}

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

    <input type="hidden" name="update_id" value="{{ $data->id }}">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">Update member information</div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Account Number') }}</label>
                <input type="text" class="form-control" name="account_number" id="account_number" value="{{ $data->account_number }}" disabled />
                <span class="err-message">{{ _lang('Account Number is required.') }}</span>
            </div>
        </div>
        <div class="col-md-1 mb-3">
            <label class="control-label">Is a Voter</label>
            <input type="text" class="form-control" value="{{ $data->is_voter ? 'Yes' : 'No' }}" id="is_voter_label" readonly>
        </div>
        @can('update_member_password')
        <div class="col-md-8 mb-3">
            <span class="float-right">
                <a href="#" data-toggle="modal" data-target="#reset_password_modal" class="btn btn-warning">Reset Password</a>
            </span>
        </div>
        @endcan
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Last Name') }}</label>
                <input type="text" class="form-control" name="last_name" id="last_name" value="{{ $data->last_name }}" required>
                <span class="err-message">{{ _lang('Last Name is required.') }}</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('First Name') }}</label>
                <input type="text" class="form-control" name="first_name" id="first_name" value="{{ $data->first_name }}" required>
                <span class="err-message">{{ _lang('First Name is required.') }}</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Middle Name') }}</label>
                <input type="text" class="form-control" name="middle_name" id="middle_name" value="{{ $data->middle_name }}">
                <span class="err-message">{{ _lang('Middle Name is required.') }}</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Suffix') }}</label>
                <input type="text" class="form-control" name="suffix" id="suffix" value="{{ $data->suffix }}">
                <span class="err-message">{{ _lang('Suffix is required.') }}</span>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Date of Birth') }}</label>
                <input type="text" class="form-control datepicker" name="birth_date" id="birth_date" value="{{ $data->birth_date }}" required @if($data->is_voter) readonly @endif>
                <span class="err-message">{{ _lang('Date Of Birth is required.') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label" for="gender">{{ _lang('Gender') }}</label>
                <select class="form-control select2" name="gender">
                    <option value="">Please select Gender</option>
                    <option value="M" {{ $data->gender == 'M' ? 'selected="selected"' : '' }}>Male</option>
                    <option value="F" {{ $data->gender == 'F' ? 'selected="selected"' : '' }}>Female</option>
                </select>
                <span class="err-message">{{ _lang('Gender is required') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Address') }}</label>
                <input type="text" class="form-control" name="address" id="address" value="{{ $data->address }}" required @if($data->is_voter) readonly @endif>
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
                    <option value="{{$brgy}}" {{ $data->brgy == $brgy ? 'selected="selected"' : ''}}>{{$brgy}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Barangay is required') }}</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Alliance') }}</label>
                <select class="form-control select2" name="alliance" @if($data->is_voter) disabled @endif>
                    <option value="">Please select Alliance</option>
                    @foreach($alliances as $alliance)
                    <option value="{{$alliance}}" {{ $data->alliance == $alliance ? 'selected="selected"' : ''}}>{{$alliance}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Alliance is required') }}</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Affiliation') }}</label>
                <select class="form-control select2" name="affiliation" @if($data->is_voter) disabled @endif>
                    <option value="">Please select Affiliation</option>
                    @foreach($affiliations as $affiliation)
                    <option value="{{$affiliation}}" {{ $data->affiliation == $affiliation ? 'selected="selected"' : ''}}>{{$affiliation}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Affiliation is required') }}</span>
            </div>
        </div>

    </div>

    <div class="row">

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Religion') }}</label>
                <select class="form-control select2" name="religion" @if($data->is_voter) disabled @endif>
                    <option value="">Please select Religion</option>
                    @foreach($religions as $religion)
                    <option value="{{$religion}}" {{ $data->religion == $religion ? 'selected="selected"' : ''}}>{{$religion}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Religion is required') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Civil Status') }}</label>
                <select class="form-control select2" name="civil_status" @if($data->is_voter) disabled @endif>
                    <option value="">Please select Civil Status</option>
                    @foreach($civil_statuses as $civil_status)
                    <option value="{{$civil_status}}" {{ $data->civil_status == $civil_status ? 'selected="selected"' : ''}}>{{$civil_status}}</option>
                    @endforeach
                </select>
                <span class="err-message">{{ _lang('Civil Status is required') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Contact Number') }}</label>
                <input type="tel" class="form-control telephone" name="contact_number" id="contact_number" value="{{ $data->contact_number }}" @if($data->is_voter) readonly @endif>
                <span class="err-message">{{ _lang('Contact Number is required.') }}</span>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-8">
            <div class="form-group">
                <label class="control-label">{{ _lang('Remarks') }}</label>
                <textarea class="form-control" name="remarks" id="remarks"> {{ $data->remarks }} </textarea>
                <span class="err-message">{{ _lang('Remarks is required.') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Precinct') }}</label>
                <input type="text" class="form-control" name="precinct" id="precinct" value="{{ $data->precinct }}" required @if($data->is_voter) readonly @endif>
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
                <input type="text" class="form-control" name="contact_person_last_name" id="contact_person_last_name" value="{{ $data->contact_person_last_name }}">
                <span class="err-message">{{ _lang('Contact Person Last Name is required.') }}</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Contact Person First Name') }}</label>
                <input type="text" class="form-control" name="contact_person_first_name" id="contact_person_first_name" value="{{ $data->contact_person_first_name }}">
                <span class="err-message">{{ _lang('Contact Person First Name is required.') }}</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Contact Person Middle Name') }}</label>
                <input type="text" class="form-control" name="contact_person_middle_name" id="contact_person_middle_name" value="{{ $data->contact_person_middle_name }}">
                <span class="err-message">{{ _lang('Contact Person Middle Name is required.') }}</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Contact Person Suffix') }}</label>
                <input type="text" class="form-control" name="contact_person_suffix" id="contact_person_suffix" value="{{ $data->contact_person_suffix }}">
                <span class="err-message">{{ _lang('Contact Person Suffix is required.') }}</span>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Contact Person Address') }}</label>
                <input type="text" class="form-control" name="contact_person_address" id="contact_person_address" value="{{ $data->contact_person_address }}">
                <span class="err-message">{{ _lang('Contact Number Address is required.') }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">{{ _lang('Contact Person Number') }}</label>
                <input type="tel" class="form-control telephone" name="contact_person_number" id="contact_person_number" value="{{ $data->contact_person_number }}">
                <span class="err-message">{{ _lang('Contact Number is required.') }}</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-save">{{ _lang('Update') }}</button>
                <button type="reset" class="btn btn-danger">{{ _lang('Reset') }}</button>
            </div>
        </div>
    </div>

</form>

<div id="reset_password_modal" class="modal fade" role="dialog">
    <form id="reset_password" method="post" autocomplete="off" action="{{url('/id_system/members/reset_password/')}}" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="modal-dialog modal-dialog-sm">
            <!-- Modal content-->
            <div class="modal-content bg-warning">
                <div class="modal-header">
                    <h5 class="modal-title">{{ _lang('Reset Password') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                <input type="hidden" name="account_number" id="account_number" value="{{ $data->account_number }}"  />
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('New Password') }}</label>
                            <div class="input-group show-hide-password">
                                <input type="password" class="form-control" name="password" required>
                                <div class="input-group-append">
                                    <div class="input-group-text cursor-pointer btn-show-hide-password">
                                        <i class="fa fa-lock"></i>
                                    </div>
                                </div>
                                @if ($errors->has('password'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">{{ _lang('Confirm Password') }}</label>
                            <div class="input-group show-hide-password">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <div class="input-group-append">
                                    <div class="input-group-text cursor-pointer btn-show-hide-password">
                                        <i class="fa fa-lock"></i>
                                    </div>
                                </div>
                                @if ($errors->has('confirm_password'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('confirm_password') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group text-right">
                            <button type="button" id="reset_password_btn" class="btn btn-primary">{{ _lang('Continue') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection


@section('js-script')

<script>
    $(document).ready(function(){
        $("#reset_password_btn").on("click",function(){
            $.ajax({
                url: $('#reset_password').attr('action'),
                method: $('#reset_password').attr('method'),
                data: $('#reset_password').serialize(),
                beforeSend: function(){
                    $("#preloader").css("display","block");
                },
                success: function(data){
                    setTimeout(function(){
                        if(data['result'] == "success"){
                            toastr.success('Password reset successfull.');
                        } else {
                            toastr.error('Password reset failed.');
                            toastr.error('Check if member is already registered.');
                        }
                        $('.modal').modal('hide');
                        $("#preloader").css("display","none");
                    }, 500);
                },
                error: function (error) {
                    setTimeout(function(){
                        $("#preloader").css("display","none");
                        $.each( error['responseJSON']['errors'], function( i, val ) {
                            toastr.error(val);
                        });
                    }, 500);
                }
            });
        });
    });
</script>


@endsection