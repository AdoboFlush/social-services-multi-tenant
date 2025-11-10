@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">{{ _lang('Roles and Permission') }}</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item active">{{ _lang('Roles and Permission') }}</li>
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
					<ul class="nav nav-tabs">
						<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#permissions">{{ _lang('Permissions') }}</a></li>
						<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#roles">{{ _lang('Roles') }}</a></li>
					</ul>
					<div class="tab-content">
						<div id="permissions" class="tab-pane active">
							<div class="card">
								<div class="card-body">
									<!-- <div class="col-md-12 float-right mb-2" style="z-index:10;">
										<button class="btn btn-primary btn-sm float-right ajax-modal" data-title="{{ _lang('Add Permission') }}" data-href="{{ route('permission.create', [], false) }}">{{ _lang('Add New Permission') }}</button>
									</div>								 -->
						            <div class="col-md-12">
					                    <div class="table-responsive">
					                      <table id="permission" aria-busy="false" aria-colcount="5" class="data-table table data-table table-striped">
					                          <thead role="rowgroup" class="">
					                              <tr role="row">
					                                  <th>Permissions</th>                    
					                                  <th></th>
					                              </tr>
					                          </thead>
					                          <tbody role="rowgroup" class="">
					                          	@foreach($permissions as $p)
				                              	<tr v-for="role in roles">
				                                  	<td width="15%" class='name'>{{ $p->name }}</td>
					                                <td class="no-padding text-right">
					                                	<!-- <form action="{{ action('PermissionController@destroy', $p->id) }}" method="post">
															{{ csrf_field() }}
															<input name="_method" type="hidden" value="DELETE">
															<button class="btn btn-danger" type="submit"><i class="mdi mdi-delete"></i> {{ _lang('Delete') }}</button>
														</form>  -->
					                                </td>
				                              	</tr>
				                              	@endforeach
					                          </tbody>
					                      </table>
					                  </div>
					                </div>
								</div>
							</div>
						</div> <!--End Deposit-->
						
						<div id="roles" class="tab-pane">
							<div class="card">
								<div class="card-body">
									<div class="col-md-12 float-right mb-2" style="z-index:10;">
										<button class="btn btn-primary btn-sm float-right ajax-modal" data-title="{{ _lang('Add Permission') }}" data-href="{{route('role.create', [], false)}}">{{ _lang('Add New Role') }}</button>
									</div>
									<div class="col-md-12">
										<div class="table-responsive">
											<table id="roles" aria-busy="false" aria-colcount="5" class="data-table table data-table table-striped">
					                          <thead role="rowgroup" class="">
					                              <tr role="row">
					                                  <th width="15%">Role Name</th>
					                                  <th>Permissions</th>                    
					                                  <th width="15%"></th>
					                              </tr>
					                          </thead>
					                          <tbody role="rowgroup" class="">
					                          	@foreach ($roles as $role)
				                              	<tr id="row_{{ $role->id }}">
				                                  	<td class="name">{{ $role->name }}</td>
				                                 	<td class="permissions">
				                                 		@foreach ($role->permissions as $role_permission)
				                                    		<span class="badge badge-success">{{ $role_permission->name }}</span>
				                                    	@endforeach
				                                  	</td>
				                                  	<td class="no-padding text-right">
				                                  		<button data-title="{{ _lang('Edit Role') }}" data-href="{{ route('role.edit', ['id' => $role->id], false) }}" class="btn btn-success ajax-modal">Edit</button>
				                                    </td>
				                              	</tr>
					                            @endforeach
					                          </tbody>
					                      </table>
										</div>
									</div>
								</div>
							</div>
						</div> <!--End Withdraw-->						
	
					</div>  
				</div>
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
