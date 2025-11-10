@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">{{ _lang('Update Profile') }}</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item active">{{ _lang('Update Profile') }}</li>
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

					<form action="{{ url('profile/update')}}" autocomplete="off" class="validate" enctype="multipart/form-data" method="post">

						<div class="row">

							<div class="col-md-6">

								@csrf

								<div class="form-group">

									<label class="control-label">{{ _lang('First Name') }}</label>

									<input type="text" class="form-control" name="first_name" value="{{ $profile->first_name }}" required disabled>

								</div>

								<div class="form-group">

									<label class="control-label">{{ _lang('Last Name') }}</label>

									<input type="text" class="form-control" name="last_name" value="{{ $profile->last_name }}" required disabled>

								</div>

								<div class="form-group">

									<label class="control-label">{{ _lang('Email') }}</label>

									<input type="text" class="form-control" name="email" value="{{ $profile->email }}" required disabled>

								</div>

								<div class="form-group">

									<label class="control-label">{{ _lang('Phone') }}</label>

									<input type="tel" class="form-control telephone" name="phone" value="{{ $profile->phone }}" required>

								</div>



                                @if(Auth::user()->user_type == 'user')

									 <div class="form-group">

										<label class="control-label">{{ _lang('Date of Birth') }}</label>

										 <input type="text"  class="form-control datepicker{{ $errors->has('date_of_birth') ? ' is-invalid' : '' }}" name="date_of_birth" value="{{ $profile->user_information->date_of_birth }}" disabled>


									 </div>

									 <div class="form-group">

										<label class="control-label">{{ _lang('Country Of Residence') }}</label>

										<select class="select2 form-control" name="country_of_residence">

											<option value="">{{ _lang('Select Country Of Residence') }}</option>

							                {{ get_country_list($profile->user_information->country_of_residence) }}

										</select>

									 </div>

									<div class="form-group">
										<label class="control-label">{{ _lang('Language') }}</label>
										<select class="form-control" name="language" id="language">
											<option value="">{{ _lang('Select Language') }}</option>
											@foreach(get_language_list() as $language)
												<option value="{{ $language }}" {{ $profile->user_information->language == $language ? 'selected' : '' }}>{{ $language }}</option>
											@endforeach
										</select>
									</div>



								 @endif





							</div>

							<div class="col-md-6">

                                @if(Auth::user()->user_type == 'user')

								 <div class="form-group">

									<label class="control-label">{{ _lang('Address') }}</label>

									<input type="text" class="form-control" name="address" value="{{ $profile->user_information->address }}" required>

								 </div>





								 <div class="form-group">

									<label class="control-label">{{ _lang('City') }}</label>

									<input type="text" class="form-control" name="city" value="{{ $profile->user_information->city }}" required>

								 </div>



								 <div class="form-group">

									<label class="control-label">{{ _lang('State') }}</label>

									<input type="text" class="form-control" name="state" value="{{ $profile->user_information->state }}" required>

								 </div>



								 <div class="form-group">

									<label class="control-label">{{ _lang('Zip/Postal Code') }}</label>

									<input type="text" class="form-control" name="zip" value="{{ $profile->user_information->zip }}" required>

								 </div>

                                @endif



								 <div class="form-group">

									<label class="control-label">{{ _lang('Profile Picture') }}</label>

									<input type="file" accept="image/*" class="form-control dropify" data-default-file="{{ $profile->profile_picture != "" ? asset('uploads/profile/'.$profile->profile_picture) : '' }}" name="profile_picture" data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG">

								</div>

							</div>

						</div>

						<div class="form-group">
							<button type="submit" class="btn btn-primary col-md-2 col-sm-12 mb-2">{{ _lang('Update Profile') }}</button>
						</div>
					</form>

				</div>

			</div>

		</div>

	</div>

</div>

@endsection

@section('js-script')

@endsection