<form method="post" class="ajax-submit" autocomplete="off" action="{{ route('role.update', ['id'=>$id], false) }}" enctype="multipart/form-data">
	{{ csrf_field() }}
	<input name="_method" type="hidden" value="PUT">	


	<div class="col-md-12">

	  <div class="form-group">

		<label class="control-label">{{ _lang('Role Name') }}</label>						
		<input type="text" name="name" value="{{ $role->name }}" class="form-control" required>

	  </div>

	</div>

	<div class="col-md-12">
		<div class="form-group">
			<label class="control-label">{{ _lang('Permissions') }}</label>
			<select name="permission[]" class="form-control select2" multiple="multiple" required>
            @foreach($permission as $value)
                <option value="{{ $value->id }}"
                	@foreach ($role->permissions as $p)
                		@if ($value->id == $p->id)
                		selected="selected"
                		@break
                		@endif
                	@endforeach
                >{{ $value->name }}</option>
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
