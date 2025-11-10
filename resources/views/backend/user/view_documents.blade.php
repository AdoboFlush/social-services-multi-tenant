@extends('layouts.app')

@section('content')
    @if(Session::has('document_success'))
        <div class="alert alert-success">
            <span>{{ session('document_success') }}</span>
        </div>
    @endif
<div class="sb-page-header pb-2 sb-page-header-dark bg-gradient-primary-to-secondary">
	<div class="container-fluid">
		<div class="sb-page-header-content py-3">
			<h1 class="sb-page-header-title">
				<div class="sb-page-header-icon"><i data-feather="user"></i></div>
				<span>{{ _lang('Documents of').' '.$user->first_name.' '.$user->last_name }}</span>
			</h1>
		</div>
	</div>
</div>

<div class="container-fluid mt-4">
	<div class="row mb-4">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<form method="post" class="form-horizontal" autocomplete="off" action="{{ route('user.update',$user->id) }}" enctype="multipart/form-data">
						{{ csrf_field() }}
						<div class="row">
						<div class="col-6">
							@php
								$account_status = is_null($user->account_status) ? 'Unverified' : $user->account_status;
								if ($user->is_dormant ) {
									$account_status = 'Dormant';

								}
							@endphp
							<table class="table table-bordered">
								<tr><td>{{ _lang('Account Number') }}</td><td><a href="{{ route('users.edit',$user->account_number) }}" target="_blank" data-account_number="{{ $user->account_number }}" data-user_id="{{ $user->id }}" class="account_number_link">{{ $user->account_number }}</a></td></tr>
								<tr><td>{{ _lang('Account Type') }}</td><td>{{ $user->account_type }}</td></tr>
								<tr><td>{{ _lang('First Name') }}</td><td>{{ $user->first_name }}</td></tr>
								<tr><td>{{ _lang('Last Name') }}</td><td>{{ $user->last_name }}</td></tr>
								<tr><td>{{ _lang('Email') }}</td><td>{{ $user->email }}</td></tr>
								<tr><td>{{ _lang('Date of Birth') }}</td><td>{{ $user->user_information->date_of_birth }}</td></tr>
								<tr><td>{{ _lang('Account Status') }}</td><td>{{ $account_status }}</td></tr>
								<tr><td>{{ _lang('KYC Status') }}</td>
									<td class="kyc-status p-0">
										<select class="form-control" name="kyc_status">
											<option></option>
											@foreach (KycStatus::all() as $status)
												<option
													value="{{ $status->name }}"
													@if( $user->isKycStatusIs($status->name) )
													{{ "selected" }}
													@endif
												>
													{{ $status->name }}
												</option>
											@endforeach
										</select>
									</td>
								</tr>
							</table>
							<div>
								<button type="submit" class="btn btn-primary btn-sm">Update</button>
								<a href="javascript:;" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#update_confirm_modal">Verify</a>
							</div>
						</div>
						<div class="col-6">
							<table class="table table-bordered">
								<tr><td>{{ _lang('Address') }}</td><td>{{ $user->user_information->address }}</td></tr>
								<tr><td>{{ _lang('City') }}</td><td>{{ $user->user_information->city }}</td></tr>
								<tr><td>{{ _lang('State') }}</td><td>{{ $user->user_information->state }}</td></tr>
								<tr><td>{{ _lang('Zip') }}</td><td>{{ $user->user_information->zip }}</td></tr>
								<tr><td>{{ _lang('Country') }}</td><td>{{ $user->user_information->country_of_residence }}</td></tr>
								<tr><td>{{ _lang('Phone') }}</td><td>{{ $user->phone }}</td></tr>
							</table>
							<div class="form-group">
								<label class="control-label" for="remarks">Remarks</label>
								<textarea class="form-control" name="kyc_remarks" id="remarks" rows="3">{{ $user->kyc_remarks }}</textarea>
							</div>
						</div>
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col">
			<div class="card">
				<div class="card-body table-responsive-sm">
					<div class="form-check">
						<input type="checkbox" class="form-check-input" id="select-all">
						<label class="form-check-label" for="">Select All</label>
					</div>
					<form method="post" id="kyc-multiple-action-form" autocomplete="off" action="{{ route('user.update.document',$user->id) }}">
						<table class="table table-bordered">
							<thead>
							<tr>
								<th>{{ _lang('Document Name') }}</th>
                                <th>{{ _lang('Document File') }}</th>
                                <th>{{ _lang('Upload Date') }}</th>
								<th>{{ _lang('Status') }}</th>
							</tr>
							</thead>
							<tbody>
							@foreach($documents->where('status','!=','deleted') as $document)
								<tr>
									<td class="align-middle">
										<div class="form-check">
											<input type="checkbox" class="form-check-input document-check" data-status="{{ $document->status }}" data-id="{{ $document->id }}" data-file="{{ $document->document }}" data-href="{{ asset('uploads/documents/'.$document->document ) }}">
											<label class="form-check-label" for="exampleCheck1">{{ $document->document_name }}</label>
										</div>
									</td>
									<td class="align-middle"><a class="doc_file" target="_blank" href="{{ Storage::disk('s3')->url('uploads/documents/' . $document->document) }}">{{ $document->document }}</a></td>
                                    <td>{{ $document->created_at }}</td>
                                    <td class="p-0 kyc-doc-status">
										<select class="form-control document-select" name="{{ $document->id }}" disabled>
											<option value="unreviewed"  {{ $document->status == "unreviewed" || "" ? "selected" : "disabled" }}>Unreviewed</option>
											<option value="approved" {{ $document->status == "approved" ? "selected" : "" }}>Approved</option>
											<option value="rejected" {{ $document->status == "rejected" ? "selected" : "" }}>Rejected</option>
											<option value="pending" {{ $document->status == "pending" ? "selected" : "" }}>Pending</option>
										</select>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					<div class="mr-2 float-left kyc-select-container">
						<select class="form-control" id="status_all" name="status_all">
							<option selected value="0">Select Status</option>
							<option value="unreviewed">Unreviewed</option>
							<option value="approved">Approved</option>
							<option value="rejected">Rejected</option>
							<option value="pending">Pending</option>
							<option value="all-kyc">All KYC</option>
							<option value="all-checked-kyc">All Checked KYC</option>
						</select>
					</div>
					<div class="mr-2 float-left kyc-select-container">
						<select class="form-control" id="function_all" name="function_all">
							<option selected value="0">Select Function</option>
							<option class="approve" value="approved">Approve</option>
							<option class="download" value="download">Download</option>
							<option class="delete" value="deleted">Delete</option>
						</select>
					</div>
					<div class="float-left">
						<button type="button" class="btn btn-primary" id="update-multiple" disabled>Update</button>
						<button type="button" class="btn btn-secondary" id="upload-multiple">Upload Kyc</button>
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@include('backend.user.modal.confirmation')
@include('backend.user.modal.update-confirm', ['user_id' => $user->id]);
@include('backend.user.modal.upload')
@endsection

@section('js-script')
	<script type="text/javascript">
		$(document).ready(function(){
			@if(Session::has('varified_success'))
				toastr.success("{{ session('varified_success') }}");
			@elseif(Session::has('varified_fail'))
				toastr.success("{{ session('varified_fail') }}");
			@endif
			if(sessionStorage.getItem("status")){
				sessionStorage.getItem("status") ? toastr.success(sessionStorage.getItem("message")) : toastr.error(sessionStorage.getItem("message"));
			}
			sessionStorage.clear();
			$('.document-check').on('change',function(){
				let id = $(this).data('id');
				if ($(this).is(":checked")) {
					$("#kyc-multiple-action-form select[name='"+ id + "']").prop('disabled', false);
				} else {
					$("#kyc-multiple-action-form select[name='"+ id + "']").prop('disabled', 'disabled');
				}
			});

			$('.form-check-input').on('change',function(){
				updateStatusAllButton();
			});

			$('#select-all').on('click',function (){
				$('input:checkbox').not(this).prop('checked', this.checked);
				$("#kyc-multiple-action-form select.document-select").prop('disabled', this.checked ? false : "disabled");
			});
			$('#update-multiple').on('click',function(e){
				var error = 0;
				var message = "";
				var action = ""
				var status = $('#status_all').val();
				if($('#status_all').val() != 0){
					if($('#function_all').val() == 0){
						toastr.error("Select a function");
						error = 1;
					}
				}
				if(!error && ($('#function_all').val() == "deleted" || $('#function_all').val() == "approved" || $('#function_all').val() == "0")){
					switch ($('#function_all').val()){
						case "deleted":
							message = "{{ _lang('Are you sure you want to delete all the Rejected KYC documents?') }}";
							break;
						case "approved":
							message = "{!! _lang('Are you sure you want to change all the " + status + " KYC documents to Approved?') !!}";
							break;
						default:
							message = "{{ _lang('Are you sure you want to update the document/s?') }}";
							break;
					}
					action = "update";
				} else if(!error && ($('#function_all').val() == "download")){
					action = "download";
					switch ($('#status_all').val()){
						case "unreviewed":
							message = "Are you sure you want to download all the Unreviewed KYC documents?";
							break;
						case "approved":
							message = "Are you sure you want to download all the Approved KYC documents?";
							break;
						case "pending":
							message = "Are you sure you want to download all the Pending KYC documents?";
							break;
						case "all-kyc":
							message = "Are you sure you want to download all the Pending KYC documents?";
							break;
						case "all-checked-kyc":
							message = "Are you sure you want to download all the selected KYC documents?";
							break;
						default:
							message = "Are you sure you want to download the document/s?";
							break;
					}
				}
				if(!error){
					$("#confirm_modal #btn-submit").attr('data-action',action);
					$("#confirm_modal #modal-message").text(message);
					$("#confirm_modal").modal('show');
				}
			})

			$('#upload-multiple').on('click',function(){
				$('#upload_kyc_form').attr('action','upload/' + '{{ $user->id }}');
				$('#import_document_modal').modal('show');
			});
			$('#status_all').on('change',function (){
				updateStatusAllButton();
				$('#function_all').prop('selectedIndex',0);
				switch($(this).val()){
					case "unreviewed":
					case "pending":
						$('#function_all option').show();
						$('#function_all option.delete').hide();
						break;
					case "approved":
					case "all-kyc":
					case "all-checked-kyc":
						$('#function_all option').hide();
						$('#function_all option.download').show();
						break
					case "rejected":
						$('#function_all option').hide();
						$('#function_all option.delete').show();
						break;
				}
			});

			$("#btn-submit").on('click',function(){
				$("#confirm_modal").modal('hide');
				if($(this).data('action')=="update"){
					updateStatus();
				} else if($(this).data('action')=="download"){
					download();
				}
			});

			$('#review_csv').on('click', function(e) {
				var has_file_uploaded = false;
				$("input[type='file']").each(function (i, obj) {
					if ($(obj).val()) {
						has_file_uploaded = true;
						return false;
					}
				});
				if (has_file_uploaded) {
					$("#preloader").css("display","block");
					$("#upload_kyc_form").submit();
				} else {
					e.preventDefault();
					toastr.error("Proof of ID or Proof of Address is required.");
				}
			});

			function updateStatusAllButton() {
				if ($('.document-check').is(":checked") || $('#status_all').val() != 0) {
					$('#update-multiple').prop('disabled', false);
				} else {
					$('#select-all').prop('checked', false);
					$('#update-multiple').prop('disabled', true);
				}
			}
			function updateStatus() {
				var link  = $('#kyc-multiple-action-form').attr('action');
				var data = $('#kyc-multiple-action-form').serialize();
				$.ajax({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					url: link,
					method: 'POST',
					data: data,
					beforeSend: function () {
						$("#preloader").css("display", "block");
					}, success: function (data) {
						setTimeout(function () {
							if (data.status) {
								sessionStorage.setItem("status", data.status);
								sessionStorage.setItem("message", data.message);
								location.reload();
							} else {
								$("#preloader").css("display", "none");
								data.status ? toastr.success(data.message) : toastr.error(data.message);
							}
						}, 500);
					},
					error: function (request, status, error) {
						setTimeout(function () {
							$("#preloader").css("display", "none");
							toastr.error(data)
						}, 500);
					}
				});
			}

			function download() {
				$('.document-check').each(function (i, obj) {
					if (($('#status_all').val() == "all-kyc" || ($(obj).is(":checked") && $('#status_all').val() == "all-checked-kyc")) ||
							($('#status_all').val() == "pending" && $(obj).data('status') == "pending") ||
							($('#status_all').val() == "approved" && $(obj).data('status') == "approved") ||
							($('#status_all').val() == "unreviewed" && $(obj).data('status') == "unreviewed")
					) {
						downloadObject(obj);
					}
					function downloadObject(obj){
						var a = document.createElement('a');
						var url = $(obj).data('href');
						var file = $(obj).data('file');
						a.href = url;
						a.download = file;
						document.body.append(a);
						a.click();
						a.remove();
						window.URL.revokeObjectURL(url);
					}
				});
			}
		});
	</script>
@endsection
