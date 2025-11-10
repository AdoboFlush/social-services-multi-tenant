@extends('layouts.app')

@section('content')
<div class="sb-page-header pb-2 sb-page-header-dark bg-gradient-primary-to-secondary">
	<div class="container-fluid">
		<div class="sb-page-header-content py-3">
			<h1 class="sb-page-header-title">
				<div class="sb-page-header-icon"><i data-feather="dollar-sign"></i></div>
				<span>{{ _lang('Currency List') }}</span>
			</h1>
		</div>
	</div>
</div>

<div class="container-fluid mt-4">
	<div class="row">
		<div class="col-12">
			<div class="card no-export">
				<div class="card-body">
					<h4 class="card-title"><span class="panel-title">{{ _lang('Currency List') }}</span>
                        @can('currencies_add')
						<button class="btn btn-primary btn-sm float-right ajax-modal" data-title="{{ _lang('Add Currency') }}" data-href="{{route('currency.create')}}">{{ _lang('Add New') }}</button>
					    @endcan
                    </h4>
					<table class="table data-table">
						<thead>
							<tr>
								<th>{{ _lang('Name') }}</th>
								<th class="text-center">{{ _lang('Action') }}</th>
							</tr>
						</thead>
						<tbody>

							@foreach($currencys as $currency)
							<tr id="row_{{ $currency->id }}">
								<td width="80%" class='name'>{{ $currency->name }}</td>								
								<td class="text-center">
									<div class="dropdown">
										<form action="{{ action('CurrencyController@destroy', $currency['id']) }}" method="post">
											{{ csrf_field() }}
											<input name="_method" type="hidden" value="DELETE">
                                            @can('currencies_delete')
											<button class="btn btn-danger" type="submit"><i class="mdi mdi-delete"></i> {{ _lang('Delete') }}</button>
										    @endcan
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

@endsection


