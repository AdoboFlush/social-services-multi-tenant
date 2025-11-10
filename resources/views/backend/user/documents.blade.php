@extends('layouts.app')

@section('content')
<div class="sb-page-header pb-2 sb-page-header-dark bg-gradient-primary-to-secondary">
	<div class="container-fluid">
		<div class="sb-page-header-content py-3">
			<h1 class="sb-page-header-title">
				<div class="sb-page-header-icon"><i data-feather="user"></i></div>
				<span>{{ _lang('User Documents') }}</span>
			</h1>
		</div>
	</div>
</div>

<div class="container-fluid mt-4">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<form method="post" autocomplete="off" action="{{ route('user.documents') }}">
						{{ csrf_field() }}
						<h4 class="card-title"><span class="panel-title">{{ _lang('User Documents') }}</span></h4>
						<div class="filterContainer filter ml-auto row">
							<div class="col">
								@foreach($statuses as $status)
								<div class="form-check text-left">
									<input class="form-check-input" type="checkbox" value="{{ $status }}" name="account_status">
									<label class="form-check-label">
										{{ $status }}
									</label>
								</div>
								@endforeach
							</div>
							<div class="col">
								<div class="form-check text-left">
									<input class="form-check-input" type="checkbox" value="w-check" name="kyc_status">
									<label class="form-check-label">
										WCheck
									</label>
								</div>
								<div class="form-check text-left">
									<input class="form-check-input" type="checkbox" value="unreviewed" name="kyc_status">
									<label class="form-check-label">
										Unreviewed
									</label>
								</div>
							</div>
							<div class="col">
								<div class="form-check text-left">
									<input class="form-check-input" type="checkbox" value="approved" name="kyc_status">
									<label class="form-check-label">
										Approved
									</label>
								</div>
								<div class="form-check text-left">
									<input class="form-check-input" type="checkbox" value="rejected" name="kyc_status">
									<label class="form-check-label">
										Rejected
									</label>
								</div>
								<div class="form-check text-left">
									<input class="form-check-input" type="checkbox" value="pending" name="kyc_status">
									<label class="form-check-label">
										Pending
									</label>
								</div>
							</div>
							<div class="col">
								<div class="form-check text-left">
									<input class="form-check-input" type="checkbox" value="card-approved" name="kyc_status">
									<label class="form-check-label">
										Card - Approved
									</label>
								</div>
								<div class="form-check text-left">
									<input class="form-check-input" type="checkbox" value="card-rejected" name="kyc_status">
									<label class="form-check-label">
										Card - Rejected
									</label>
								</div>
								<div class="form-check text-left">
									<input class="form-check-input" type="checkbox" value="card-pending" name="kyc_status">
									<label class="form-check-label">
										Card - Pending
									</label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="offset-6 col-md-6 ">
								<div class="button-container float-right mt-3">
									<button type="button" class="btn btn-primary" id="filter">Filter</button>
									<button type="button" class="btn btn-primary" id="new">Add New</button>
									<button type="button" class="btn btn-secondary" id="reset">Reset</button>
									@can("users_document_update")
									<div class="btn-group">
									  <button type="button" class="btn btn-success dropdown-toggle update-status" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" disabled="true">
									    {{ _lang('Action') }}
									  </button>
									  <div class="dropdown-menu">
									    <button class="dropdown-item document-status" data-value="{{ KycStatus::CHECK_A }}" type="button">{{ _lang('Update to Check-A') }}</button>
									    <button class="dropdown-item document-status" data-value="{{ KycStatus::CHECK_B }}" type="button">{{ _lang('Update to Check-B') }}</button>
									    <button class="dropdown-item document-status" data-value="{{ KycStatus::CHECK_C }}" type="button">{{ _lang('Update to Check-C') }}</button>
									    <button class="dropdown-item document-status" data-value="{{ KycStatus::CHECK_D }}" type="button">{{ _lang('Update to Check-D') }}</button>
									    <button class="dropdown-item document-status" data-value="{{ KycStatus::CHECK_E }}" type="button">{{ _lang('Update to Check-E') }}</button>
										<button class="dropdown-item document-status" data-value="{{ KycStatus::CHECK_F }}" type="button">{{ _lang('Update to Check-F') }}</button>
										<button class="dropdown-item document-status" data-value="{{ KycStatus::CHECK_G }}" type="button">{{ _lang('Update to Check-G') }}</button>
										<button class="dropdown-item document-status" data-value="{{ KycStatus::CHECK_H }}" type="button">{{ _lang('Update to Check-H') }}</button>
										<button class="dropdown-item document-status" data-value="{{ KycStatus::CHECK_I }}" type="button">{{ _lang('Update to Check-I') }}</button>
										<button class="dropdown-item document-status" data-value="{{ KycStatus::CHECK_J }}" type="button">{{ _lang('Update to Check-J') }}</button>
									  </div>
									</div>
									@endcan
								</div>
							</div>
						</div>
					</form>
					<div class="table-container">
						<div class="row my-2">
							<div class="offset-6 col-6 offset-xl-9 col-xl-3 text-right">
								<div class="input-group filter">
									<input type="text" id="search" class="form-control" placeholder="Search">
									<div class="input-group-append">
										<button class="btn btn-secondary" id="btn-search" type="button">Search</button>
									</div>
								</div>
							</div>
						</div>
						<div class="badge badge-info my-2 loader">
							<span class="text-light">Loading</span> <div class="spinner-border spinner-border-sm text-light" role="status"></div>
						</div>
						<form id="frm_document" method="POST" action="{{ route('user.documents_status') }}">
							{{ csrf_field() }}
							<input type="hidden" name="update_status">
							<div id="ajax-table"></div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@include('backend.user.modal.add-new')
@include('backend.user.modal.upload')
@endsection

@include("mustache.documents-table")

@section('js-script')
<script src="https://cdn.jsdelivr.net/npm/handlebars@latest/dist/handlebars.js"></script>
<script type="text/javascript">
    var MUSTACHE_TABLE_API = '/admin/users/documents/all';
    var MUSTACHE_EXPORT_API = '/admin/deposit/history/export';
    var MUSTACHE_TABLE_HEADERS =  {
        headers : [
            "Account Number",
            "Account Name",
            "Email",
			"KYC Status",
			"Account Status",
			"Date of Last Upload",
			"Date of Status Change"
        ]
    };
</script>
<script type="text/javascript" src="{{ asset('js/mustache-table.js?rand=' . \Carbon\Carbon::now()->format('YmdHis')) }}"></script>
<script type="text/javascript">
	$(document).ready(function (){
		$("#export").on("click",function(){
			gblExport(MUSTACHE_EXPORT_API, "OWL-Export-JP-vouchers.xls", $(this))
		});	

        $(".view_document").on("click",function(){
            var params = {
                log: "Documents",
                subject_type: "App\\User",
                subject_id: $(this).data("user_id"),
                description: "Viewed documents"
            };
            logActivity(params, $(this).data("url"));
        });

		$("#reset").on("click", function() {
			$("input:checkbox").prop('checked', false);
		});

		$("#new").on("click",function(){
            $("#account_number").val('');
            $("#new-document-modal").modal("show");
        });

        $('#next').on("click",function() {
            var acc_num = $("#account_number").val();
            if(acc_num){
                $.ajax({
                    url: '{{ route("users.search") }}',
                    method: 'POST',
                    data:{
                        "_token": "{{ csrf_token() }}",
                        "account_number": acc_num
                    },
                    beforeSend: function(){
                        $("#preloader").css("display","block");
                    },success: function(data){
                        setTimeout(function(){
                            if(data.success){
                                $(".account-detail").text(data.user.first_name + " " + data.user.last_name + " (" + data.user.account_number + ")");
                                $("#preloader").css("display","none");
                                $('.modal').modal('hide');
                                $('#import_document_modal').modal('show');
                                $('#upload_kyc_form').attr('action','documents/upload/'+data.user.id);
                            } else {
                                $("#preloader").css("display","none");
                                toastr.error(data.message);
                            }
                        }, 500);
                    },
                    error: function (request, status, error) {
                        setTimeout(function(){
                            $("#preloader").css("display","none");
                            toastr.error("An Error Occurred ");
                        }, 500);
                    }
                });
            } else {
                toastr.error("{{ _lang('Invalid account number') }}")
            }
        });

        @if(Session::has('document_success'))
            toastr.success("{{ session('document_success') }}")
        @endif

        $('.table-container').on('change', 'input[name="update_all"]', function(){
			updateCheckBoxes()
        })

		$('.table-container').on('change', 'input[name="update_checked[]"]', function(){
			var hasChecked = false;
			$('input[name="update_checked[]"]').each(function () {
				if (this.checked) {
					hasChecked = true
				}
			});
			$('.update-status').prop('disabled', !hasChecked);
		})

        $('.document-status').on('click', function(){
			var dataVal = $(this).data('value')
			$('input[name="update_status"]').val(dataVal)
			$('#frm_document').submit()
        })

        function updateCheckBoxes() {
			var checked = $('input[name="update_all"]').prop('checked')
			$('.update-status').prop('disabled', !checked)
			$('input[name="update_checked[]"]:visible').prop('checked', checked);
        }
    });
</script>
@endsection