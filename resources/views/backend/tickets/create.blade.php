@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">{{ _lang('Create a Ticket') }}</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item active">{{ _lang('Create a Ticket') }}</li>
      </ol>
    </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <form enctype="multipart/form-data" method="post" id="createTicketForm" autocomplete="off" action="{{ url('admin/ticket/create') }}">
            {{ csrf_field() }}
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-6">
                        <label class="control-label" for="user_id">{{ _lang('To') }}</label>
                        <input type="text" class="form-control" id="account_number" name="account_number" required>
                        <div id="check-user" class="d-none">
                            <div>Name: <span id="user_name" class="font-weight-bold"></span></div>
                            <div>Email: <span id="user_email" class="font-weight-bold"></span></div>
                        </div>
                        <div id="err-user-not-found" class="text-danger d-none">User not found.</div>
                        <span class="err-message">{{ _lang('User is required.') }}</span>
                    </div>
                    <div class="form-group col-6">
                        <label class="control-label" for="priority">{{ _lang('Priority') }}</label>
                        <select class="select2 form-control" name="priority" id="priority" required>
                            <option value="">Select Priority</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                        <span class="err-message">{{ _lang('Priority is required.') }}</span>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-6">
                        <label class="control-label" for="department">{{ _lang('Department') }}</label>
                        <select class="select2 form-control" name="department" id="department" required>
                            <option value="">Select Department</option>
                            <option value="customer_support">Customer Support</option>
                            <option value="banking_support">Banking Support</option>
                            <option value="technical_support">Technical Support</option>
                            <option value="others">Others</option>
                        </select>
                        <span class="err-message">{{ _lang('Department is required.') }}</span>
                    </div>
                    <div class="form-group col-6">
                        <label class="control-label" for="status">{{ _lang('Status') }}</label>
                        <select class="select2 form-control" name="status" id="status" required>
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="on-hold">On-hold</option>
                            <option value="solved">Solved</option>
                        </select>
                        <span class="err-message">{{ _lang('Status is required.') }}</span>
                    </div>
                </div>
                <div class="form-row"><div class="form-group col-6">
                        <label class="control-label" for="subject">{{ _lang('Subject') }}</label>
                        <select class="select2 form-control" name="subject" onchange="checkSubject(this.value);" id="subject" required>
                            <option value="">Select Subject</option>
                            <option value="deposit">Deposit</option>
                            <option value="wire_transfer_deposit_request">Wire Transfer Deposit Request</option>
                            <option value="withdrawal">Withdrawal</option>
                            <option value="internal_transfer">Internal Transfer</option>
                            <option value="payment_request">Payment Request</option>
                            <option value="currency_exchange">Currency Exchange</option>
                            <option value="card_top-up">Card Top-up</option>
                            <option value="kyc_verification">KYC Verification</option>
                            <option value="update_user_information_request">Update User Information Request</option>
                            <option value="others">Others</option>
                        </select>
                        <input class="form-control mt-3" type="text" name="others" id="others" placeholder="{{ _lang('Others - Please enter the subject') }}" style='display:none;'/>
                        <span class="err-message">{{ _lang('Subject is required.') }}</span>
                    </div>
                    <div class="form-group col-6">
                        <label class="control-label" for="tag">{{ _lang('Ticket Tag') }}</label>
                        <select class="select2 form-control" name="tag" id="tag" required>
                            <option value="">Select Ticket Tag</option>
                            <option value="money_related">Money-related</option>
                            <option value="technical">Technical</option>
                            <option value="others">Others</option>
                        </select>
                        <span class="err-message">{{ _lang('Status is required.') }}</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label" for="message">{{ _lang('Message') }}</label>
                    <textarea class="form-control summernote-simple" id="message" rows="10" name="message" required></textarea>
                    <span class="err-message">{{ _lang('Message is required.') }}</span>
                </div>
                <div class="form-group row">
                    <label class="control-label col-12 col-sm-4 col-md-3 py-2" for="canned_id">{{ _lang('Apply Canned Action') }}</label>
                    <div class="col-4" id="language-container">
                        <select class="form-control select2" name="canned_message" id="canned_message">
                            <option value="" data-message="">{{ _lang('Select Canned Action') }}</option>
                            @if(isset($templates) )
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}" data-message="{{ $template->message }}">{{ $template->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-1">
                        @php $languages = array("en","jp") @endphp
                        <select class="form-control p-0 pl-1" name="language" id="language">
                            @foreach($languages as $key => $language)
                                <option value="{{ $language }}" @if($key == 0) selected @endif>{{ strtoupper($language) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-secondary" id="insert">Insert</button>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-12 col-sm-4 col-md-3 py-2" for="attachment">{{ _lang('Attachment') }}</label>
                    <div class="col-12 col-sm-8">
                        <input type="file" accept="image/*,.csv,application/pdf,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" class="form-control-file attachment mb-2" name="attachment[]">
                        <a class="d-inline-block" href="javaScript:void(0)" id="add-more">+ add more attachment</a>
                    </div>
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-primary custom-btn-block" id="createTicket">Send Ticket</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js-script')
    <script type="text/javascript">
        $(function(){
            $("#createTicket").on('click',function(event){
                var validated = validateFields($('#createTicketForm'));
                if(validated) {
                    $("#preloader").css("display","block");
                    $("#createTicketForm").submit();
                }
            });
            $("#insert").on('click', function(){
                var previous = $('.summernote-simple').summernote('code');
                var message = $("#canned_message").find(":selected").data("message");
                previous = previous == "<p><br></p>" ? "" : previous + "<br/>";
                if(message){
                    $("#createTicketForm .summernote-simple").summernote('code',previous + message);
                }
            });

            $("#language").on('change', function(){
                var lang = $(this).val();
                var url =  '{{ url('admin/get-canned-messages') }}'+'/'+lang;
                $.ajax({
                    url: url,
                    beforeSend: function(){
                        $("#preloader").css("display","block");
                    },success: function(data){
                        setTimeout(function(){
                            $("#canned_message").html(data).select2({theme: "classic"});
                            $("#preloader").css("display","none");
                        }, 500);

                    }
                });
                $("#canned-message-form #language").val($(this).data("lang"));
                $("#canned-message-form-modal").modal("show");
            });

            $("#add-more").on("click",function(){
                $("#add-more").before('<input type="file" accept="image/*,.csv,application/pdf,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" class="form-control-file attachment mb-2" name="attachment[]">');
                if($('.attachment').length == 3){
                    $("#add-more").remove();
                }
            });
        });
        @if(Session::has("ticket_error_response"))
            toastr.error("{{ session("ticket_error_response") }}");
        @endif
        @if(Session::has("ticket_response"))
            toastr.success("{{ session("ticket_response") }}");
        @endif
        function checkSubject(val){
            var element=document.getElementById('others');
            if(val=='others')
                element.style.display='block';
            else
                element.style.display='none';
                element.value='';
        }

        $('#account_number').on('blur', function(){
		var search = $(this).val();
		if(search){
			return $.ajax({
				url: `/admin/dashboard/users?filter[account_number]=${search}`,
				method: 'GET',
				processData: false,
				contentType: false,
				success: function ({data}){
					var user_data = data[0]
					if(typeof user_data !== 'undefined'){
						var username = `${user_data.first_name} ${user_data.last_name}` 
						var email = user_data.email
						$('#check-user').removeClass('d-none')
						$('#err-user-not-found').addClass('d-none')
						$('#user_name').text(username)
						$('#user_email').text(email)
					}else{
						$('#err-user-not-found').removeClass('d-none');
						$('#check-user').addClass('d-none')
						$('#user_name').text('')
						$('#user_email').text('')
						$('#account_number').val('')
					}
				}
			})
		}
	})

    </script>
@endsection