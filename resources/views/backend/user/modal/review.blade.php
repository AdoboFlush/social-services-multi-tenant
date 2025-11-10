<table class="table table-bordered label table-sm">
	<tr>
        <th>{{ _lang('Date') }}</th>
        <th>{{ _lang('Email') }}</th>
        <th>{{ _lang('First Name') }}</th>
        <th>{{ _lang('Last Name') }}</th>
        <th>{{ _lang('Date of Birth') }}</th>
        <th>{{ _lang('Account Type') }}</th>
	    <th>{{ _lang('Phone') }}</th>
        <th>{{ _lang('Address') }}</th>
        <th>{{ _lang('City') }}</th>
		<th>{{ _lang('State') }}</th>
        <th>{{ _lang('Zip Code') }}</th>
        <th>{{ _lang('Country') }}</th>
        <th>{{ _lang('Language') }}</th>
		<th>{{ _lang('Error') }}</th>
    </tr>
	@foreach($data as $user)
    <tr>
        <td>{{ $user['date'] }}</td>
        <td class="text-break">{{ $user['email'] }}</td>
        <td>{{ $user['first_name'] }}</td>
        <td>{{ $user['last_name'] }}</td>
        <td>{{ $user['date_of_birth'] }}</td>
        <td>{{ $user['account_type'] }}</td>
        <td>{{ $user['phone'] }}</td>
        <td>{{ $user['address'] }}</td>
		<td>{{ $user['city'] }}</td>
		<td>{{ $user['state'] }}</td>
		<td>{{ $user['zip'] }}</td>
		<td>{{ $user['country_of_residence'] }}</td>
		<td>{{ ucwords($user['language']) }}</td>
		<td class="error">
            @foreach($user['errors'] as $error)
			- {{ $error }} <br />
			@endforeach
		</td>
    </tr>
    @endforeach
</table>
<div class="form-group float-right">
    <button type="button" onclick="importCsv()" class="btn btn-primary">{{ _lang('Extract') }}</button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ _lang('Cancel') }}</button>
</div>

<script>
    function importCsv() {
			
		if (!$.trim($('.error').text()) == ""){
			toastr.error("Cannot extract due to errors in the uploaded file.");
		} else {
			var form = document.getElementById('import_csv_form');
			var link  = $('#import_csv_form').attr('action') + "/users";
			var data = new FormData(form);
			$.ajax({
				url: link,
				method: 'POST',
				data: data,
				processData: false,
				contentType: false,
				beforeSend: function(){
					$("#preloader").css("display","block"); 
				},success: function(data){
					setTimeout(function(){
						$("#preloader").css("display","none"); 
						data.status ? toastr.success(data.message) : toastr.error(data.message);
						$('#main_modal').modal('hide');
					}, 500);
					$('input[name="csv_file"]').val('');
				},
				error: function (request, status, error) {
					setTimeout(function(){
						$("#preloader").css("display","none");
						$('#main_modal').modal('hide');
						toastr.error(data);
					}, 500); 
				}
			});
		}
    }
</script>