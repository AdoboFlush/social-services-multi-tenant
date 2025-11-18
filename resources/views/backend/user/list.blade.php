@extends('layouts.app')


@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">{{_lang('User List')}}</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item active">User List</li>
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
					<div class="card-title">
						<a class="btn btn-primary btn-sm float-right" href="{{ route('users.create', [], false) }}">{{ _lang('Add New') }}</a>
					</div>
					<table class="table data-table">
						<thead>
						<tr>
							<th>{{ _lang('Date Registered') }}</th>
							<th>{{ _lang('Account Number') }}</th>
							<th>{{ _lang('User Type') }}</th>
							<th>{{ _lang('Name') }}</th>
							<th>{{ _lang('Email') }}</th>
							<th>{{ _lang('Address') }}</th>
							<th class="text-center">{{ _lang('Account Status') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
						</tr>
						</thead>
						<tbody>

						@foreach($users as $user)
							<tr id="row_{{ $user->id }}">
								<td class='date_registered'>{{ Carbon\Carbon::parse($user->created_at)->format('Y-m-d h:i:s A') }}</td>
								<td class='account_number'><a href="{{ route('users.edit',$user->account_number) }}" target="_blank" data-account_number="{{ $user->account_number }}" data-user_id="{{ $user->id }}" class="account_number_link">{{ $user->account_number }}</a></td>
								<td class='account_type'>{{ ucwords($user->user_type) }}</td>
								<td class='name'>{{ $user->full_name }}</td>
								<td class='email'>{{ $user->email }}</td>
								<td class='address'>{{ $user->user_information->address.", ".$user->user_information->city.", ".$user->user_information->state.", ".$user->user_information->country_of_residence }}</td>
                                @if($user->is_dormant)
                                    <td class='account_status text-center'>{!! status(_lang('Dormant'),'danger') !!}</td>
                                @else
                                    <td class='account_status text-center'>{!! $user->status == 1 ? status(_lang('Active'),'success') : status(_lang('In-active'),'danger') !!}</td>
								@endif
                                <td class="text-center">
									<div class="dropdown">
									<button class="btn btn-primary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									{{ _lang('Action') }}
									</button>
									
										<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
											<a href="{{ route('users.edit',$user->account_number) }}" class="dropdown-item dropdown-edit btn-edit" data-account_number="{{ $user->account_number }}" data-user_id="{{ $user->id }}"><i class="mdi mdi-pencil"></i> {{ _lang('Edit') }}</a>
											<button data-href="{{ action('UserController@show', $user['id']) }}" data-title="{{ _lang('View User') }}" class="dropdown-item dropdown-view ajax-modal"><i class="mdi mdi-eye"></i> {{ _lang('View') }}</button>
											<form action="{{ action('UserController@destroy', $user['id']) }}" method="post">
												{{ csrf_field() }}
												<input name="_method" type="hidden" value="DELETE">
												<button class="btn-remove dropdown-item" type="submit"><i class="mdi mdi-delete"></i> {{ _lang('Delete') }}</button>
											</form>
										</div>
									</div>
								</td>
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

@section('js-script')
    <script type="text/javascript">
        $(document).ready(function (){
            $(".btn-edit").on("click",function(){
                var params = {
                    log: "User Account Detail",
                    subject_type: "App\\User",
                    subject_id: $(this).data("user_id"),
                    description: "Viewed"
                };
                logActivity(params, $(this).data("url"));
            });
        });
    </script>
@endsection
