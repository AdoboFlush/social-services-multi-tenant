@extends('layouts.app')
@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">{{ _lang('Ticket ID:') }} {{ $ticket->id }}</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item active">{{ _lang('Ticket ID:') }} {{ $ticket->id }}</li>
      </ol>
    </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
@endsection

@section('content')
<div class="container-fluid">
    <div class="row card pb-4">
        <div class="col-12 mb-3">
            <div class="d-none d-md-block">
                <div class="card-body">
                    <h4 class="card-title panel-title">{{ _lang('Ticket ID:') }} {{ $ticket->id }}</h4>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{{ _lang("Subject") }}</th>
                            <th>{{ _lang("Customer") }}</th>
                            <th>{{ _lang("Country") }}</th>
                            <th>{{ _lang("Date Ticket Created") }}</th>
                            <th>{{ _lang("Date Updated") }}</th>
                            <th>{{ _lang("Department") }}</th>
                            <th>{{ _lang("Operator") }}</th>
                            <th>{{ _lang("Priority") }}</th>
                            <th>{{ _lang("Ticket Tag") }}</th>
                            <th>{{ _lang("Status") }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr id="{{ $ticket->id }}">
                            <td>{{ ucwords(str_replace("_", " ", $ticket->subject)) }}</td>
                            <td>{{ $ticket->user->account_number }} {{ ucwords($ticket->user->first_name) }} {{ ucwords($ticket->user->last_name) }}</td>
                            <td>{{ ucwords($ticket->user->user_information->country_of_residence) }}</td>
                            <td>{{ $ticket->created_at }}</td>
                            <td>{{ $ticket->updated_at }}</td>
                            <td>{{ ucwords(str_replace("_", " ", $ticket->department)) }}</td>
                            <td>{{ ucwords($ticket->conversations->first()->sender->first_name) }}</td>
                            <td>{{ ucwords($ticket->priority) }}</td>
                            <td>{{ ucwords(str_replace("_", " ", $ticket->tag)) }}</td>
                            <td>{{ ucwords($ticket->status) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card p-2 my-2 d-block d-md-none">
                <div class="row mb-2">
                    <div class="col-12">
                        <h4 class="card-title panel-title">{{ _lang('Ticket ID:') }} {{ $ticket->id }}</h4>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-6">
                        <strong>{{ _lang('Date Ticket Created') }}:</strong>
                        {{ $ticket->created_at }}
                    </div>
                    <div class="col-6">
                        <strong>{{ _lang('Date Updated') }}:</strong>
                        {{ $ticket->updated_at }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-12">
                        <strong>{{ _lang('Subject') }}:</strong>
                        {{ ucwords(str_replace("_", " ", $ticket->subject)) }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-12">
                        <strong>{{ _lang('Department') }}:</strong>
                        {{ ucwords(str_replace("_", " ", $ticket->department)) }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-12">
                        <strong>{{ _lang('Status') }}:</strong>
                        {{ ucwords($ticket->status) }}
                    </div>
                </div>
            </div>
        </div>
        @if($ticket->status != "archived")
        <div class="col-12 reply-button-container mb-4">
            <button class="btn btn-primary custom-btn-block px-4" data-toggle="collapse" data-target="#reply-container" aria-expanded="false" type="button">{{ _lang("Reply") }}</button>
        </div>
        <div class="col-12">
            <div class="collapse" id="reply-container">
                <form enctype="multipart/form-data" method="post" id="createTicketForm" autocomplete="off" action="{{ url('admin/ticket/edit/'.$ticket->id) }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="ticket_action" id="ticket_action" value="create">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-6">
                                <label class="control-label" for="department">{{ _lang('Department') }}</label>
                                <select class="form-control" name="department" id="department" required>
                                    <option {{ $ticket->department == "customer_support" ? "selected" : "" }} value="customer_support">Customer Support</option>
                                    <option {{ $ticket->department == "banking_support"  ? "selected" : "" }} value="banking_support">Banking Support</option>
                                    <option {{ $ticket->department == "technical_support"  ? "selected" : "" }} value="technical_support">Technical Support</option>
                                    <option {{ $ticket->department == "others"  ? "selected" : "" }} value="others">Others</option>
                                </select>
                                <span class="err-message">{{ _lang('Department is required.') }}</span>
                            </div>
                            <div class="form-group col-6">
                                <label class="control-label" for="status">{{ _lang('Status') }}</label>
                                <select class="form-control" name="status" id="status" required>
                                    <option {{ $ticket->status == "pending" ? "selected" : "" }} value="pending">Pending</option>
                                    <option {{ $ticket->status == "on-hold" ? "selected" : "" }} value="on-hold">On-hold</option>
                                    <option {{ $ticket->status == "solved" ? "selected" : "" }} value="solved">Solved</option>
                                </select>
                                <span class="err-message">{{ _lang('Status is required.') }}</span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-6">
                                <label class="control-label" for="priority">{{ _lang('Priority') }}</label>
                                <select class="form-control" name="priority" id="priority" required>
                                    <option {{ $ticket->priority  == "low" ? "selected" : "" }} value="low">Low</option>
                                    <option {{ $ticket->priority  == "medium" ? "selected" : "" }} value="medium">Medium</option>
                                    <option {{ $ticket->priority  == "high" ? "selected" : "" }} value="high">High</option>
                                </select>
                                <span class="err-message">{{ _lang('Priority is required.') }}</span>
                            </div>
                            <div class="form-group col-6">
                                <label class="control-label" for="tag">{{ _lang('Ticket Tag') }}</label>
                                <select class="select2 form-control" name="tag" id="tag">
                                    <option value="">Select Ticket Tag</option>
                                    <option {{ $ticket->tag == "kyc_related" ? "selected" : "" }} value="kyc_related">KYC-related</option>
                                    <option {{ $ticket->tag == "card_related" ? "selected" : "" }} value="card_related">Card-related</option>
                                    <option {{ $ticket->tag == "deposit" ? "selected" : "" }} value="deposit">Deposit</option>
                                    <option {{ $ticket->tag == "withdrawal" ? "selected" : "" }} value="withdrawal">Withdrawal</option>
                                    <option {{ $ticket->tag == "money_related" ? "selected" : "" }} value="money_related">Money-related</option>
                                    <option {{ $ticket->tag == "technical" ? "selected" : "" }} value="technical">Technical</option>
                                    <option {{ $ticket->tag == "risk_related" ? "selected" : "" }} value="risk_related">Risk-related</option>
                                    <option {{ $ticket->tag == "others" ? "selected" : "" }} value="others">Others</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="subject">{{ _lang('Subject') }}</label>
                            <select class="form-control" name="subject" onchange="checkSubject(this.value);" id="subject" required>
                                <option {{ $ticket->subject  == "deposit" ? "selected" : "" }} value="deposit">Deposit</option>
                                <option {{ $ticket->subject  == "wire_transfer_deposit_request" ? "selected" : "" }} value="wire_transfer_deposit_request">Wire Transfer Deposit Request</option>
                                <option {{ $ticket->subject  == "withdrawal" ? "selected" : "" }} value="withdrawal">Withdrawal</option>
                                <option {{ $ticket->subject  == "internal_transfer" ? "selected" : "" }} value="internal_transfer">Internal Transfer</option>
                                <option {{ $ticket->subject  == "payment_request" ? "selected" : "" }} value="payment_request">Payment Request</option>
                                <option {{ $ticket->subject  == "currency_exchange" ? "selected" : "" }} value="currency_exchange">Currency Exchange</option>
                                <option {{ $ticket->subject  == "card_top-up" ? "selected" : "" }} value="currency_exchange">Card Top-up</option>
                                <option {{ $ticket->subject  == "kyc_verification" ? "selected" : "" }} value="kyc_verification">KYC Verification</option>
                                <option {{ $ticket->subject  == "update_user_information_request" ? "selected" : "" }} value="update_user_information_request">{{ _lang("Update User Information Request") }}</option>
                            @if(!in_array($ticket->subject,$subjects))
                                <option selected value="others">Others</option>
                                @endif
                            </select>
                            <input class="form-control mt-3" type="text" name="others" id="others" placeholder="{{ _lang('Others - Please enter the subject') }}" @if(in_array($ticket->subject,$subjects)) style='display:none;' @else value="{{ $ticket->subject }} @endif"/>
                            <span class="err-message">{{ _lang('Subject is required.') }}</span>
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
                            <div class="col-2 py-1">
                                <button type="button" class="btn btn-secondary" id="insert">Insert</button>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-sm-4 col-md-3 py-2" for="attachment">{{ _lang('Attachment') }}</label>
                            <div class="col-4">
                                <input type="file" accept="image/*,.csv,application/pdf,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" class="form-control-file attachment mb-2" name="attachment[]">
                                <a class="d-inline-block" href="javaScript:void(0)" id="add-more">+ add more attachment</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-primary custom-btn-block" id="createTicket">Send Ticket</button>
                            <button type="button" class="btn btn-secondary custom-btn-block" id="updateTicket">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif
        @include("backend.tickets.conversation")
    </div>
</div>
@endsection

@section('js-script')
    <script type="text/javascript">
        $(function(){
            $("#updateTicket").on('click',function(){
                $("#ticket_action").val("update");
                $("#preloader").css("display","block");
                $("#createTicketForm").submit();
            });
            $("#createTicket").on('click',function(){
                $("#ticket_action").val("create");
                if($("#status").val() == "solved"){
                    $("textarea#message").removeAttr("required");
                } else {
                    $("textarea#message").attr("required",true);
                }
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
            $(".reply-button-container button").on('click',function(){
                $(this).hide();
            });
            $("#showMoreButton").on('click',function(){
                if($(".up").hasClass("d-none")){
                    $(".up").removeClass("d-none");
                    $(".down").addClass("d-none");
                } else {
                    $(".down").removeClass("d-none");
                    $(".up").addClass("d-none");
                }
            });
        });
        function checkSubject(val){
            var element=document.getElementById('others');
            if(val=='others')
                element.style.display='block';
            else
                element.style.display='none';
            element.value='';
        }
        @if(Session::has("ticket_error_response"))
            toastr.error("{{ session("ticket_error_response") }}");
        @endif

        @if(Session::has("ticket_response"))
        toastr.success("{{ session("ticket_response") }}");
        @endif
    </script>
@endsection