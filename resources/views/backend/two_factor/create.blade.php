
<form id="form_code" method="post" autocomplete="off" action="{{ url('user/verify_code') }}">
	{{ csrf_field() }}
	<div class="col-md-12 err_code" style="display: none">
		<div class="alert alert-danger">
			<span>{{ _lang('Invalid code.') }}</span>
		</div>
	</div>
	<div class="col-md-12">
		<p id="message"  class="text-muted"></p>
		<div class="form-group">
			<label class="control-label">{{ _lang('Your action requires 2-step authentication. Please check your email with verification code sent to {email}.',['email'=>Auth::user()->email]) }}</label>
			<input type="text" class="form-control" name="code" id="code" required placeholder="{{ _lang("Code") }}">
			<span class="err-message">{{ _lang('Code is required.') }}</span>
        </div>
	</div>
	<div class="col-md-12 d-none d-md-block">
        <div class="alert alert-danger" role="alert" style="display: none;"></div>
        <div class="form-group text-right">
			<input type="button" value="{{ _lang('Verify') }}" class="btn btn-primary"/>

            <a href="javascript:;" class="btn btn-danger" data-dismiss="modal">
                {{ _lang('Back') }}
            </a>
		</div>
	</div>
	<div class="col-md-12 d-sm-block d-md-none">
        <div class="alert alert-danger" role="alert" style="display: none;"></div>
        <div class="form-group text-right">
			<input type="button" class="btn btn-primary btn-block mb-2" value="{{ _lang('Verify') }}">

            <a href="javascript:;" class="btn btn-danger btn-block" data-dismiss="modal">
                {{ _lang('Back') }}
            </a>
		</div>
	</div>
</form>
<form id="form_data" method="post" autocomplete="off" action="{{ $action }}">
	{{ csrf_field() }}
	@php
		foreach($request as $key => $value) {
	@endphp
	<input type="hidden" name="{{ $key }}" value="{{ $value }}">
	@php
		}		
	@endphp
</form>
<script type="text/javascript">
	$(document).ready(function(){
		$('input[type="button"]').on('click', function(e){
			e.preventDefault();
			var validateResult = validateFields($('#form_code'));
			if (validateResult) {
				var link  = $('#form_code').attr('action');
				var title = "{{ _lang('Two Factor Authentication') }}";
				 $.ajax({
					 url: link,
					 method: 'POST',
					 data: $('#form_code').serialize(),
					 beforeSend: function(){
					 	$("#preloader").css("display","block");
					 },success: function(data){
						$("#preloader").css("display","block");
						$('#main_modal').modal('hide');
						//$(".dropify").dropify();
						$('#form_data').submit();
					 },
					  error: function (request, status, error) {
						$("#preloader").css("display","none");
					  	$('.err_code').css("display","block");
					  }
				 });
			}

			return false;
		});
	})
</script>