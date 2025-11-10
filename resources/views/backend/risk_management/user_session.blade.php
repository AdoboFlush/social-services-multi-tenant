@extends('layouts.app')

@section('content')
<div class="sb-page-header pb-2 sb-page-header-dark bg-gradient-primary-to-secondary">
	<div class="container-fluid">
		<div class="sb-page-header-content py-3">
			<h1 class="sb-page-header-title">
				<div class="sb-page-header-icon"><i data-feather="alert-octagon"></i></div>
				<span>User Session</span>
			</h1>
		</div>
	</div>
</div>

<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<table class="table">
						<thead>
                            <tr>
                                <th>{{ _lang('Account Number') }}</th>
                                <th>{{ _lang('Name') }}</th>
                                <th>{{ _lang('Account Type') }}</th>
                                <th>{{ _lang('IP Address') }}</th>
                                <th>{{ _lang('Device') }}</th>
                                <th>{{ _lang('Last Activity') }}</th>
                            </tr>
						</thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td class='account_number'>{{ $user->account_number }}</td>
                                    <td class='name'>{{ $user->full_name }}</td>
                                    <td class='account_type'>{{ ucwords($user->account_type) }}</td>
                                    <td class='ip_address'>{{ $user->session->ip_address }}</td>
                                    <td class='device'>{{ $user->session->user_agent }}</td>
                                    <td class='last_activity'>{{ $user->session->last_activity_time_ago }}</td>
                                </tr>
                            @endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection