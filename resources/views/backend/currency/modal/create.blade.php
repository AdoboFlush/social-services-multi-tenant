<form method="post" autocomplete="off" action="{{route('currency.store')}}" enctype="multipart/form-data">

	{{ csrf_field() }}

	

	<div class="col-md-12">

	  <div class="form-group">

		<label class="control-label">{{ _lang('Currency') }}</label>						
		<select class="form-control select2" name="name" id="name" required>
			<option value="">{{ _lang('Select Currency') }}</option>
			@foreach($currencies as $currency )
			<option value="{{ $currency }}">{{ $currency }}</option>
			@endforeach
		</select>

	  </div>

	</div>
				

	<div class="col-md-12">

	  <div class="form-group">

	    <button type="reset" class="btn btn-danger">{{ _lang('Reset') }}</button>

		<button type="submit" class="btn btn-primary">{{ _lang('Save') }}</button>

	  </div>

	</div>

</form>

