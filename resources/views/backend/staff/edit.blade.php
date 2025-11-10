@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0">{{ _lang('Update Staff') }}</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="/">Home</a></li>
					<li class="breadcrumb-item"><a href="/">Staffs</a></li>
					<li class="breadcrumb-item active">{{ _lang('Update Staff') }}</li>
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

					<form method="post" class="validate" autocomplete="off" action="{{ route('staffs.update', ['id' => $id], false) }}" enctype="multipart/form-data">
						<div class="row">
							<div class="col-md-6">
								{{ csrf_field()}}
								<input name="_method" type="hidden" value="PATCH">

								<div class="form-group">
									<label class="control-label">{{ _lang('First Name') }}</label>
									<input type="text" class="form-control" name="first_name" value="{{ $user->first_name }}" required>
								</div>

								<div class="form-group">
									<label class="control-label">{{ _lang('Last Name') }}</label>
									<input type="text" class="form-control" name="last_name" value="{{ $user->last_name }}" required>
								</div>

								<div class="form-group">
									<label class="control-label">{{ _lang('Email') }}</label>
									<input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
								</div>

								<div class="form-group">
									<label class="control-label">{{ _lang('Phone') }}</label>
									<input type="tel" class="form-control telephone" name="phone" value="{{ $user->phone }}" required>
								</div>

								<div class="form-group">
									<label class="control-label">{{ _lang('Password') }}</label>
									<input type="password" class="form-control" name="password" value="">
								</div>

								<div class="form-group">
									<label class="control-label">{{ _lang('Confirm Password') }}</label>
									<input type="password" class="form-control" name="password_confirmation" value="">
								</div>

								<div class="form-group">
									<label class="control-label">{{ _lang('User Access') }}</label>
									<select class="form-control select2" name="user_access" id="user_access" required>
										@foreach($roles as $role)
										<option value="{{ $role->name }}" {{ ($user->user_access == $role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
										@endforeach
									</select>
								</div>

								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label">{{ _lang('User Type') }}</label>
										<select class="form-control" name="user_type" id="user_type" required>
											<option value="{{\App\User::ADMIN}}" {{ ($user->user_type == \App\User::ADMIN) ? 'selected' : '' }}>{{\App\User::ADMIN}}</option>
											<option value="{{\App\User::TAGGER}}" {{ ($user->user_type == \App\User::TAGGER) ? 'selected' : '' }}>{{\App\User::TAGGER}}</option>
											<option value="{{\App\User::PAYMASTER}}" {{ ($user->user_type == \App\User::PAYMASTER) ? 'selected' : '' }}>{{\App\User::PAYMASTER}}</option>
											<option value="{{\App\User::WATCHER}}" {{ ($user->user_type == \App\User::WATCHER) ? 'selected' : '' }}>{{\App\User::WATCHER}}</option>
										</select>
									</div>
								</div>

								<div class="form-group">
									<label class="control-label">{{ _lang('Status') }}</label>
									<select class="form-control select2" id="status" name="status" required>
										<option value="1">{{ _lang('Active') }}</option>
										<option value="0">{{ _lang('Inactive') }}</option>
									</select>
								</div>

								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label">{{ _lang('[FOR TAGGER] Affiliation Access (separated by semicolon ; if multiple.)') }}</label>
										<input type="text" class="form-control" name="affiliation_access" value="{{ $affiliation_access }}">
									</div>
								</div>

								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label">{{ _lang('[FOR TAGGER] Excluded affiliation(s) (separated by semicolon ; if multiple.)') }}</label>
										<input type="text" class="form-control" name="excluded_affiliations" value="{{ $excluded_affiliations }}">
									</div>
								</div>

								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label">{{ _lang('[FOR TAGGER] Barangay(s) access (separated by semicolon ; if multiple.)') }}</label>
										<input type="text" class="form-control" name="brgy_access" value="{{ $brgy_access }}">
									</div>
								</div>

								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label">{{ _lang('[FOR TAGGER] Hide fields (separated by semicolon ; if multiple.)') }}</label>
										<br><i>Supported Fields : alliance; alliance_1; affiliation; affiliation_subgroup; affiliation_1; sectoral; sectoral_subgroup; religion; organization; contact_number; is_deceased; civil_status; remarks; party_list; party_list_1; position;</i>
										<input type="text" class="form-control" name="hide_fields" value="{{ $hide_fields }}">
									</div>
								</div>

								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label">{{ _lang('[FOR TAGGER]  Area Access') }}</label>
										<select class="form-control" id="area_access" name="area_access">
											<option value="0" {{ $area_access == 0 ? 'selected="selected"' : '' }}>{{ _lang('*') }}</option>
											<option value="1" {{ $area_access == 1 ? 'selected="selected"' : '' }}>{{ _lang('Area 1') }}</option>
											<option value="2" {{ $area_access == 2 ? 'selected="selected"' : '' }}>{{ _lang('Area 2') }}</option>
											<option value="3" {{ $area_access == 3 ? 'selected="selected"' : '' }}>{{ _lang('Area 3') }}</option>
											<option value="4" {{ $area_access == 4 ? 'selected="selected"' : '' }}>{{ _lang('Area 4') }}</option>
											<option value="5" {{ $area_access == 5 ? 'selected="selected"' : '' }}>{{ _lang('Area 5') }}</option>
											<option value="6" {{ $area_access == 6 ? 'selected="selected"' : '' }}>{{ _lang('Area 6') }}</option>
										</select>
									</div>
								</div>

								<div class="col-md-12">
									<div class="form-group">
										<span class="mr-5"><input type="checkbox" name="has_export" {{ $has_export ? "checked" : "" }} /> <b> [FOR TAGGER] Has Export </b></span>
										<span class="mr-5"><input type="checkbox" name="bypass_update" {{ $bypass_update ? "checked" : "" }} /> <b> [FOR TAGGER] Bypass Update </b></span>
										<span class="mr-5"><input type="checkbox" name="has_activity_logs_access" {{ $has_activity_logs_access ? "checked" : "" }} /> <b> [FOR TAGGER] Has Ac Logs Access </b></span>
										<span class="mr-5"><input type="checkbox" name="has_area_search" {{ $has_area_search ? "checked" : "" }} /> <b> [FOR TAGGER] Has Area Search </b></span>
										<span class="mr-5"><input type="checkbox" name="for_viewing_only" {{ $for_viewing_only ? "checked" : "" }} /> <b> [FOR TAGGER] For Viewing only </b></span>
										<span class="mr-5"><input type="checkbox" name="can_clear_field" {{ $can_clear_field ? "checked" : "" }} /> <b> [FOR TAGGER] Clear Field Access</b></span>
									</div>
								</div>

								<div class="form-group">
									<button type="submit" class="btn btn-success">{{ _lang('Update') }}</button>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">{{ _lang('Profile Picture') }} ( 300 X 300 {{ _lang('for better view') }} )</label>
									<input type="file" accept="image/*,.csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" class="dropify" class="dropify" name="profile_picture" data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG" data-default-file="{{ $user->profile_picture != "" ? asset('uploads/profile/'.$user->profile_picture) : '' }}">
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
<script>
	$("#user_type").val("{{ $user->user_type }}");
	$("#status").val("{{ $user->status }}");
</script>
@endsection