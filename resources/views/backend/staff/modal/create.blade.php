<form method="post" class="ajax-submit" autocomplete="off" action="{{ route('staffs.store', [], false) }}" enctype="multipart/form-data">

	{{ csrf_field() }}

	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('First Name') }}</label>
		<input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" required>
	  </div>
	</div>

	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('Last Name') }}</label>
		<input type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" required>
	  </div>
	</div>

	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('Email') }}</label>
		<input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
	  </div>
	</div>

	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('Phone') }}</label>
		<input type="tel" class="form-control telephone" name="phone" value="{{ old('phone','+1') }}" required>
	  </div>
	</div>

	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('Password') }}</label>
		<input type="password" class="form-control" name="password" required>
	  </div>
	</div>

	<div class="col-md-12">
		<div class="form-group">
			<label class="control-label">{{ _lang('Confirm Password') }}</label>
			<input type="password" class="form-control" name="password_confirmation" required>
		</div>
	</div>

	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('User Access') }}</label>
		<select class="form-control select2" name="user_access" id="user-type" required>
		  @foreach($roles as $role)
		  <option value="{{ $role->name }}">{{ $role->name }}</option>
		  @endforeach
		</select>
	  </div>
	</div>

	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('User Type') }}</label>
		<select class="form-control" name="user_type" id="user_type" required>
		  <option value="{{\App\User::ADMIN}}">{{\App\User::ADMIN}}</option>
		  <option value="{{\App\User::TAGGER}}">{{\App\User::TAGGER}}</option>
		  <option value="{{\App\User::PAYMASTER}}">{{\App\User::PAYMASTER}}</option>
		  <option value="{{\App\User::WATCHER}}">{{\App\User::WATCHER}}</option>
		</select>
	  </div>
	</div>

	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('Status') }}</label>
		<select class="form-control select2" id="status" name="status" required>
		  <option value="1">{{ _lang('Active') }}</option>
		  <option value="0">{{ _lang('Inactive') }}</option>
		</select>
	  </div>
	</div>

	<div class="col-md-12">
		<div class="form-group">
			<label class="control-label">{{ _lang('[FOR TAGGER] Affiliation Access (separated by semicolon ; if multiple.)') }}</label>
			<input type="text" class="form-control" name="affiliation_access" value="{{ old('affiliation_access') }}">
		</div>
	</div>

	<div class="col-md-12">
		<div class="form-group">
			<label class="control-label">{{ _lang('[FOR TAGGER] Excluded affiliation(s) (separated by semicolon ; if multiple.)') }}</label>
			<input type="text" class="form-control" name="excluded_affiliations" value="{{ old('excluded_affiliations') }}">
		</div>
	</div>

	<div class="col-md-12">
		<div class="form-group">
			<label class="control-label">{{ _lang('[FOR TAGGER] Barangay(s) access (separated by semicolon ; if multiple.)') }}</label>
			<input type="text" class="form-control" name="brgy_access" value="{{ old('brgy_access') }}">
		</div>
	</div>

	<div class="col-md-12">
		<div class="form-group">
			<label class="control-label">{{ _lang('[FOR TAGGER] Hide fields (separated by semicolon ; if multiple.)') }}</label>
			<br><i>Supported Fields : alliance; alliance_1; affiliation; affiliation_subgroup; affiliation_1; sectoral; sectoral_subgroup; religion; organization; contact_number; is_deceased; civil_status; remarks; party_list; party_list_1; position;</i>
			<input type="text" class="form-control" name="hide_fields" value="{{ old('hide_fields') }}">
		</div>
	</div>

	<div class="col-md-12">
		<div class="form-group">
			<label class="control-label">{{ _lang('[FOR TAGGER] Area Access') }}</label>
			<select class="form-control" id="area_access" name="area_access">
				<option value="0">{{ _lang('*') }}</option>
				<option value="1">{{ _lang('Area 1') }}</option>
				<option value="2">{{ _lang('Area 2') }}</option>
				<option value="3">{{ _lang('Area 3') }}</option>
				<option value="4">{{ _lang('Area 4') }}</option>
				<option value="5">{{ _lang('Area 5') }}</option>
				<option value="6">{{ _lang('Area 6') }}</option>
			</select>
		</div>
	</div>
	

	<div class="col-md-12">
		<div class="form-group">
			<span class="mr-5"><input type="checkbox" name="has_export" /> <b> [FOR TAGGER] Has Export </b></span>
			<span class="mr-5"><input type="checkbox" name="bypass_update" /> <b> [FOR TAGGER] Bypass Update </b></span>
			<span class="mr-5"><input type="checkbox" name="has_activity_logs_access" /> <b> [FOR TAGGER] Has Activity Logs Access </b></span>
			<span class="mr-5"><input type="checkbox" name="has_area_access" /> <b> [FOR TAGGER] Has Area Search </b></span>
			<span class="mr-5"><input type="checkbox" name="for_viewing_only" /> <b> [FOR TAGGER] For Viewing only </b></span>
			<span class="mr-5"><input type="checkbox" name="can_clear_field" /> <b> [FOR TAGGER] Clear Field Access</b></span>
		</div>
	</div>

	<div class="col-md-12">
		<div class="form-group">
			<label class="control-label">{{ _lang('Profile Picture') }} ( 300 X 300 {{ _lang('for better view') }} )</label>
			<input type="file" accept="image/*" class="dropify" name="profile_picture" data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG" data-default-file="">
		</div>
	</div>

	<div class="col-md-12">
		<div class="form-group">
			<button type="reset" class="btn btn-danger">{{ _lang('Reset') }}</button>
			<button type="submit" class="btn btn-primary">{{ _lang('Save') }}</button>
		</div>
	</div>

</form>

<script>

$("#user_type").val("{{ old('user_type') }}");

</script>