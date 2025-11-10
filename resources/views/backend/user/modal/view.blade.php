<div class="card">

	<div class="card-body p-2">

	    <table class="table table-bordered">

			<tr><td colspan="2" class="text-center"><img class="img-lg thumbnail" src="{{ $user->profile_picture != "" ? asset('uploads/profile/'.$user->profile_picture) : asset('images/avatar.png') }}"></td></tr>

			<tr><td>{{ _lang('First Name') }}</td><td>{{ $user->first_name }}</td></tr>
			<tr><td>{{ _lang('Last Name') }}</td><td>{{ $user->last_name }}</td></tr>
			<tr><td>{{ _lang('Email') }}</td><td>{{ $user->email }}</td></tr>
			<tr><td>{{ _lang('Phone') }}</td><td>{{ $user->phone }}</td></tr>	
			<tr><td>{{ _lang('User Type') }}</td><td>{{ ucwords($user->user_type) }}</td></tr>	
			<tr><td>{{ _lang('Status') }}</td><td>{!! $user->status == 1 ? status(_lang('Active'),'success') : status(_lang('In-Active'),'danger') !!}</td></tr>	
			<tr><td>{{ _lang('Date of Birth') }}</td><td>{{ $user->user_information->date_of_birth }}</td></tr>
			<tr><td>{{ _lang('Country Of Residence') }}</td><td>{{ $user->user_information->country_of_residence }}</td></tr>
			<tr><td>{{ _lang('Address') }}</td><td>{{ $user->user_information->address }}</td></tr>
			<tr><td>{{ _lang('City') }}</td><td>{{ $user->user_information->city }}</td></tr>
			<tr><td>{{ _lang('State') }}</td><td>{{ $user->user_information->state }}</td></tr>
			<tr><td>{{ _lang('Zip') }}</td><td>{{ $user->user_information->zip }}</td></tr>

	    
	        @if($user->user_information->others != '')

	        $others = unserialize($user->user_information->others);

	        foreach($others as $key => $val)
				<tr><td>{{ $key }}</td><td>{{ $val }}</td></tr>
	        @enforeach

	        @endif

	    </table>

	</div>

</div>

