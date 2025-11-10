@extends('layouts.app')

@section('content')
<div class="sb-page-header pb-2 sb-page-header-dark bg-gradient-primary-to-secondary">
	<div class="container-fluid">
		<div class="sb-page-header-content py-3">
			<h1 class="sb-page-header-title">
				<div class="sb-page-header-icon"><i data-feather="bell"></i></div>
				<span>{{ _lang('Add New Advertisements') }}</span>
			</h1>
		</div>
	</div>
</div>

<div class="container-fluid mt-4">
	<div class="row">
		<div class="col-8">
		<div class="card">
			<div class="card-body">
			<h4 class="card-title panel-title">{{ _lang('Add New Advertisement') }}</h4>

			<div class="row">
				<div class="col-md-12">
				<form
                    method="post"
                    class="validate"
                    enctype="multipart/form-data"
                    autocomplete="off"
                    action="{{ route('advertisements.store') }}"
                >
					{{ csrf_field() }}
				    <div class="row mb-2">
						<div class="col-md-8">
							<div class="form-group">
								<label class="control-label">{{ _lang('Language') }}</label>
								<select id="language" name="language" class="form-control">
									<option {{ request()->lang == 'English' ? 'selected' : '' }} value="English">English</option>
									<option {{ request()->lang == 'Japanese' ? 'selected' : '' }} value="Japanese">Japanese</option>
								</select>
							</div>
						</div>
				    	<div class="col-md-8">
							<div class="form-group">
								<label class="control-label">{{ _lang('Title') }}</label>
								<input type="text" class="form-control" name="title" required />
								<span class="err-message">{{ _lang('Account Type is required.') }}</span>
							</div>
						</div>
				    	<div class="col-md-8">
							<div class="form-group">
								<label class="control-label">{{ _lang('Link') }}</label>
								<input type="url" class="form-control" name="link" />
							</div>
						</div>
				    	<div class="col-md-8">
							<div class="form-group">
								<label class="control-label">{{ _lang('Banner') }} <small>(Ideal size 1600x350)</small></label>
								<input type="file" accept="image/*" class="form-control" name="banner" />
							</div>
						</div>
                        <div class="col-md-8">
							<div class="form-group">
								<label class="control-label">{{ _lang('Sequence') }}</label>
								<select name="sequence" class="form-control">
                                    @foreach ($ordinals as $ordinal)
                                        <option {{ $next_sequence === $ordinal['sequence'] ? 'selected' : ''}} value="{{ $ordinal['sequence'] }}">
                                            {{ $ordinal['ordinal'] }}
                                        </option>
                                    @endforeach
                                </select>
							</div>
						</div>
                    </div>
					<div class="row mb-2">
				    	<div class="col-md-12 text-right">
							<button type="submit" class="btn btn-primary">{{ _lang('Save') }}</button>
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
	$(document).ready(function(){
		$('#language').change(function () {
			window.location.href = "/admin/advertisements/create?lang=" + $(this).val();
		});
	})
</script>
@endsection
