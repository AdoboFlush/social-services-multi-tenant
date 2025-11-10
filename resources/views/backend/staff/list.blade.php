@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">{{ _lang('Staff List') }}</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
	  <li class="breadcrumb-item active">Staffs</li>
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
					<div class="float-right mb-2">
						<button class="btn btn-primary btn-sm float-right ajax-modal" data-title="{{ _lang('Add User') }}" data-href="{{ route('staffs.create', [], false) }}">{{ _lang('Add New') }}</button>
					</div>
					<div class="table-responsive">
					<table class="table table-bordered table-sm table-striped data-table">
						<thead>
						<tr>
							<th>{{ _lang('Name') }}</th>
							<th>{{ _lang('Email') }}</th>
							<th>{{ _lang('User Access') }}</th>
							<th>{{ _lang('User Type') }}</th>
							<th>{{ _lang('Status') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
						</tr>
						</thead>
						<tbody>
						
						@foreach($users as $user)
							<tr id="row_{{ $user->id }}">
								<td class='name'>{{ $user->first_name.' '.$user->last_name }}</td>
								<td class='email'>{{ $user->email }}</td>
								<td class='user_access'>{{ !empty($user->user_access) ? ucwords($user->user_access) : ucwords($user->user_type) }}</td>
								<td class='user_type'>{{ $user->user_type }}</td>
								<td class='status'>{!! $user->status == 1 ? status(_lang('Active'),'success') : status(_lang('In-Active'),'danger') !!}</td>					
								<td class="text-center">
									<div class="dropdown">
									<button class="btn btn-primary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									{{ _lang('Action') }}
									</button>
									<form action="{{ route('staffs.destroy', ['id' => $user['id']], false) }}" method="post">
										{{ csrf_field() }}
										<input name="_method" type="hidden" value="DELETE">
										
										<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
											<button data-href="{{ route('staffs.edit', ['id' => $user['id']], false) }}" data-title="{{ _lang('Update User') }}" class="dropdown-item dropdown-edit ajax-modal"><i class="mdi mdi-pencil"></i> {{ _lang('Edit') }}</button>
											<button data-href="{{ route('staffs.show', ['id' => $user['id']], false) }}" data-title="{{ _lang('View User') }}" class="dropdown-item dropdown-view ajax-modal"><i class="mdi mdi-eye"></i> {{ _lang('View') }}</button>
											<!-- <button class="btn-remove dropdown-item" type="submit"><i class="mdi mdi-delete"></i> {{ _lang('Delete') }}</button> -->
										</div>
									</form>
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
</div>

@endsection


@section('js-script')

<script>

$(document).ready(function(){
	$(".data-table").DataTable({
		'orderCellsTop': false,
		'searchable': false,
	});
});

</script>
@endsection


