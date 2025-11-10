@extends('layouts.app')


@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">Edit User</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
	  <li class="breadcrumb-item"><a href="/admin/users">Users</a></li>
      <li class="breadcrumb-item active">Edit User</li>
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
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
                    <form method="post" id="editForm" class="validate" autocomplete="off" action="{{ action('UserController@update', $user->id) }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
						<input name="_method" type="hidden" value="PUT">
						<div class="row">
							<div class="col-md-12">
							    <div class="row">
							    	<div class="col-md-3">
										<div class="form-group">
											<label class="control-label">{{ _lang('Account Number') }}</label>
											<input type="text" class="form-control"  name="account_number" id="account_number" disabled value="{{ $user->account_number }}" />
										</div>
									</div>

									<div class="col-md-3 userDetails">
										<div class="form-group">
											<label class="control-label">{{ _lang('Registration Date') }}</label>
											<input type="text" class="form-control" value="{{ $user->created_at }}" readonly>
										</div>
									</div>

									<div class="col-md-3 userDetails"></div>
									<div class="col-md-3 userDetails"></div>

									<div class="col-md-3">
										<div class="form-group">
											<label class="control-label">{{ _lang('First Name') }}</label>
											<input type="text" class="form-control" name="first_name" value="{{ $user->first_name }}" required>
										</div>
									</div>

									<div class="col-md-3">
										<div class="form-group">
											<label class="control-label">{{ _lang('Last Name') }}</label>
											<input type="text" class="form-control" name="last_name" value="{{ $user->last_name }}" required>
										</div>
									</div>

									<div class="col-md-3{{ $user->account_type == 'business' ? '' : ' d-none' }}" id="business_name">
										<div class="form-group">
											<label class="control-label">{{ _lang('Business Name') }}</label>
											<input type="text" class="form-control" name="business_name" value="{{ $user->business_name }}">
										</div>
									</div>

									<div class="col-md-3">
										<div class="form-group">
											<label class="control-label">{{ _lang('Email') }}</label>
											<input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
										</div>
									</div>

									<div class="col-md-6{{ $user->account_type == 'business' ? '' : ' d-none' }}" id="business_name">
										<div class="form-group">
											<label class="control-label">{{ _lang('Website') }}</label>
											<textarea class="form-control" name="website_url">{{ $user->user_information->website_url }}</textarea>
										</div>
									</div>

									<div class="col-md-3">
										<div class="form-group">
											<label class="control-label">{{ _lang('Phone') }}</label>
											<input type="tel" class="form-control telephone" name="phone" value="{{ $user->phone }}">
										</div>
									</div>
									<div class="col-md-3 userDetails">
									  <div class="form-group">
										<label class="control-label">{{ _lang('Date of Birth') }}</label>
										<input type="text" class="form-control datepicker" name="date_of_birth" value="{{ $user->user_information->date_of_birth }}">
									  </div>
									</div>

									<div class="col-md-6 userDetails">
										<div class="form-group">
											<label class="control-label">{{ _lang('Address') }}</label>
											<textarea class="form-control" name="address">{{ $user->user_information->address }}</textarea>
										</div>
									</div>


									<div class="col-md-3 userDetails">
										<div class="form-group">
											<label class="control-label">{{ _lang('City') }}</label>
											<input type="text" class="form-control" name="city" value="{{ $user->user_information->city }}">
										</div>
									</div>

									<div class="col-md-3 userDetails">
										<div class="form-group">
											<label class="control-label">{{ _lang('State') }}</label>
											<input type="text" class="form-control" name="state" value="{{ $user->user_information->state }}">
										</div>
									</div>

									<div class="col-md-3 userDetails">
										<div class="form-group">
											<label class="control-label">{{ _lang('Zip') }}</label>
											<input type="text" class="form-control" name="zip" value="{{ $user->user_information->zip }}">
										</div>
									</div>

									<div class="col-md-3 userDetails">
									  <div class="form-group">
										<label class="control-label">{{ _lang('Country Of Residence') }}</label>
										<select class="form-control" name="country_of_residence">
											<option value="">{{ _lang('Country Of Residence') }}</option>
											@foreach ($countries as $country)
												<option
													{{ $user->user_information->country_of_residence == $country->name ? 'selected' : '' }}
													value="{{ $country->name }}"
												>
													{{ $country->name }}
												</option>
											@endforeach
										</select>
									  </div>
									</div>
									<div class="col-md-3 userDetails">
										<div class="form-group">
											<label class="control-label">{{ _lang('Language') }}</label>
											<select class="form-control" name="language" id="language">
												<option value="">{{ _lang('Select language') }}</option>
												@foreach(get_language_list() as $language)
													<option value="{{ $language }}" {{ $user->user_information->language == $language ? 'selected' : '' }}>{{ $language }}</option>
												@endforeach
											</select>
										</div>
									</div>


									<div class="col-md-3 userDetails">
										<div class="form-group">
											<label class="control-label">{{ _lang('Status') }}</label>
											<select class="form-control" id="status" name="status" required>
												<option value="1">{{ _lang('Active') }}</option>
												<option value="0">{{ _lang('In-Active') }}</option>
											</select>
										</div>
									</div>
									<div class="col-md-6 userDetails">
										<div class="form-group">
											<label class="control-label">{{ _lang('Profile Picture') }} ( 300 X 300 {{ _lang('for better view') }} )</label>
											<input type="file"  accept="image/*" class="dropify" name="profile_picture" data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG" data-default-file="{{ $user->profile_picture != "" ? asset('uploads/profile/'.$user->profile_picture) : '' }}">
										</div>
									</div>
									<div class="col-md-3 userDetails">
										<div class="form-group">
											<label class="control-label">{{ _lang('Remarks') }}</label>
											<textarea class="form-control" name="remarks" rows="5">{{ $user->user_information->remarks }}</textarea>
										</div>
									</div>
									<div class="col-md-12 userDetails">
										<div class="form-group">
											<input type="hidden" name="view" id="view" value="{{ $data['viewOnly'] }}">
											<button type="submit" class="btn btn-primary">{{ _lang('Update') }}</button>
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

@endsection

@section('js-script')
    
    <script type="text/javascript">
        $("#account_type").val("{{ $user->account_type }}");
        $("#status").val("{{ $user->status }}");
        $("#account_status").val("{{ $user->is_dormant ? 'Dormant' : $user->account_status }}");
        $(document).on('change','#account_type',function(){
            if($(this).val() == 'business'){
                $("#business_name").removeClass('d-none');
				$("#is_included_on_dormancy").addClass('d-none');
            }else{
                $("#business_name").addClass('d-none');
				$("#is_included_on_dormancy").removeClass('d-none');
            }
        });
        @if($data['viewOnly'] == 1)
            $(document).ready(function(){
                $(".userDetails").hide();
                $("#viewBtn").click(function() {
                    $("#viewBtn").hide();
                    $(".userDetails").show();
                });
            });
        @endif
    </script>

@endsection

