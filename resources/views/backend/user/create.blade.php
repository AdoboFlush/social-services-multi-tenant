@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
	  <div class="row mb-2">
	  <div class="col-sm-6">
		<h1 class="m-0">{{ _lang('Add New User') }}</h1>
	  </div><!-- /.col -->
	  <div class="col-sm-6">
		<ol class="breadcrumb float-sm-right">
		<li class="breadcrumb-item"><a href="/">Home</a></li>
		<li class="breadcrumb-item active">{{ _lang('Add New User') }}</li>
		</ol>
	  </div><!-- /.col -->
	  </div><!-- /.row -->
	</div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
@endsection
  
@section('content')

<div class="container-fluid">
	<div class="row">
		<div class="col-12">
		<div class="card">
			<div class="card-body">
			<form method="post" class="validate" autocomplete="off" action="{{ route('users.store') }}" enctype="multipart/form-data">
				<input type="hidden" name="email_notif" value="0">
				<div class="row">
					<div class="col-md-6">
						{{ csrf_field() }}
					    <div class="row">

							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">{{ _lang('First Name') }}</label>
									<input type="text" class="form-control" name="first_name" id="first_name" value="{{ old('first_name') }}" required>
									<span class="err-message">{{ _lang('First Name is required.') }}</span>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">{{ _lang('Last Name') }}</label>
									<input type="text" class="form-control" name="last_name" id="last_name" value="{{ old('last_name') }}" required>
									<span class="err-message">{{ _lang('Last Name is required.') }}</span>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">{{ _lang('Email') }}</label>
									<input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}" required>
									<span class="err-message">{{ _lang('Email is required.') }}</span>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">{{ _lang('Phone') }}</label>
									<input type="tel" class="form-control telephone" name="phone" id="phone" value="{{ old('phone','+1') }}" required>
									<span class="err-message">{{ _lang('Phone is required.') }}</span>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">{{ _lang('Password') }}</label>
									<input type="password" class="form-control" name="password" id="password" value="" required>
									<span class="err-message">{{ _lang('Password is required.') }}</span>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
								<label class="control-label">{{ _lang('Confirm Password') }}</label>
								<input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
								<span class="err-message">{{ _lang('Confirm Password is required.') }}</span>
								</div>
							</div>

							<div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Date of Birth') }}</label>
								<input type="text" class="form-control datepicker" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" required>
								<span class="err-message">{{ _lang('Date  Of Birth is required.') }}</span>
							  </div>
							</div>


							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">{{ _lang('Status') }}</label>
									<select class="form-control" id="status" name="status" required>
										<option value="1">{{ _lang('Active') }}</option>
										<option value="0">{{ _lang('In-Active') }}</option>
									</select>
								</div>
							</div>

							<div class="col-md-12">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Address') }}</label>
								<textarea class="form-control" name="address">{{ old('address') }}</textarea>
							  </div>
							</div>

							<div class="col-md-12">
								<div class="form-group">
									<button type="submit" class="btn btn-primary btn-save">{{ _lang('Save') }}</button>
									<button type="reset" class="btn btn-danger">{{ _lang('Reset') }}</button>
									<button type="button" class="btn btn-secondary" id="import">{{ _lang('Import') }}</button>								
								</div>
							</div>

						</div>
					</div>

					<div class="col-md-6">
						<div class="row">
                            <div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Country Of Residence') }}</label>
								<select class="form-control" name="country_of_residence" id="country_of_residence" required>
									<option value="">{{ _lang('Select Country Of Residence') }}</option>
					                {{ get_country_list(old('country_of_residence')) }}
								</select>
							  </div>
							</div>

							<div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('City') }}</label>
								<input type="text" class="form-control" name="city" value="{{ old('city') }}">
							  </div>
							</div>

							<div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('State') }}</label>
								<input type="text" class="form-control" name="state" value="{{ old('state') }}">
							  </div>
							</div>

							<div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Zip') }}</label>
								<input type="text" class="form-control" name="zip" value="{{ old('zip') }}">
							  </div>
							</div>

							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label">{{ _lang('Profile Picture') }} ( 300 X 300 {{ _lang('for better view') }} )</label>
									<input type="file" class="dropify" name="profile_picture" data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG" data-default-file="">
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>

			</div>
		</div>
	  </div>
    </div>
</div>

@include('backend.user.modal.import')

@endsection


@section('js-script')
<script type="text/javascript">
$("#user_type").val("{{ old('user_type') }}");
$(document).on('change','#account_type',function(){
	if($(this).val() == 'business'){
		$("#business_name").removeClass('d-none');
	}else{
		$("#business_name").addClass('d-none');
	}
});
$(document).ready(function(){
	$('.btn-save').on('click', function(){
		var form = $('form');
		validateFields(form);
	});
	$('#import').on('click',function(){
        $("#import_csv_form").trigger("reset");
        $("#import_modal").modal('show');
	});
	$('#review_csv').on('click',function(){
		var form = document.getElementById('import_csv_form');
		var link  = $('#import_csv_form').attr('action') + "/users/review";
		var data = new FormData(form);
		if(validateFields($('#import_csv_form'))){
			$.ajax({
			url: link,
			method: 'POST',
			data: data,
			processData: false,
			contentType: false,
				beforeSend: function(){
					$("#preloader").css("display","block"); 
				},success: function(data){
					setTimeout(function(){
						$("#preloader").css("display","none"); 
						$('#import_modal').modal('hide');
						$('#main_modal .modal-title').text("Review Extracted CSV");
						$('#main_modal .modal-body').html(data);
						$("#main_modal >.modal-dialog").addClass("fullscreen-modal");
						$('#main_modal').modal('show'); 
					   }, 500); 
				},
				error: function (request, status, error) {
					setTimeout(function(){
						$("#preloader").css("display","none");
						$('#import_csv_modal').modal('hide');
						toastr.error(data);
                	}, 500); 
				}
			});
		}
		
	});
});
</script>
@endsection


