@extends('layouts.app')

@section('content')
<div class="sb-page-header pb-2 sb-page-header-dark bg-gradient-primary-to-secondary">
	<div class="container-fluid">
		<div class="sb-page-header-content py-3">
			<h1 class="sb-page-header-title">
				<div class="sb-page-header-icon"><i data-feather="user"></i></div>
				<span>{{ _lang('My Profile Overview') }}</span>
			</h1>
		</div>
	</div>
</div>

<div class="container-fluid mt-4">
	<div class="row">
		<div class="col-md-12">
			<div class="card p-3">
				<div class="card-body">
					<h4 class="card-title panel-title">{{ _lang('My Profile Overview') }}</h4>

					<table class="table">
						<tr><td colspan="2" class="text-center"><img class="img-lg thumbnail" src="{{ $user->profile_picture != "" ? asset('uploads/profile/'.$user->profile_picture) : asset('images/avatar.png') }}"></td></tr>
						<tr><td>{{ _lang('First Name') }}</td><td>{{ $user->first_name }}</td></tr>
						<tr><td>{{ _lang('Last Name') }}</td><td>{{ $user->last_name }}</td></tr>
						<tr><td>{{ _lang('Email') }}</td><td>{{ $user->email }}</td></tr>
						<tr><td>{{ _lang('Phone') }}</td><td>{{ $user->phone }}</td></tr>	
						<tr><td>{{ _lang('User Type') }}</td><td>{{ ucwords($user->user_type) }}</td></tr>	
						<tr><td>{{ _lang('Status') }}</td><td>{!! $user->status == 1 ? status(_lang('Active'),'success') : status(_lang('In-Active'),'danger') !!}</td></tr>
						<tr>
                            <td>{{ _lang('Account Status') }}</td>
                            @if($user->is_dormant)
                                <td>{!! status(_lang('Dormant'),'danger') !!}</td>
                            @else
                                <td>{!! $user->account_status == 'Verified' ? status(_lang('Verified'),'success') : status(_lang($user->account_status ? $user->account_status : "Unverified"),'danger') !!}</td>
                            @endif
                        </tr>
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
		</div>
	</div>
</div>
@endsection


