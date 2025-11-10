@extends('layouts.app')

@section('content')
    <div class="sb-page-header pb-2 sb-page-header-dark bg-gradient-primary-to-secondary">
        <div class="container-fluid">
            <div class="sb-page-header-content py-3">
                <h1 class="sb-page-header-title">
                    <div class="sb-page-header-icon"><i data-feather="mail"></i></div>
                    <span>{{ _lang('Create a Ticket') }}</span>
                </h1>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title panel-title">{{ _lang('Create a Ticket') }}</h4>
                        <form enctype="multipart/form-data" method="post" id="createTicketForm" autocomplete="off"
                            action="{{ url('user/ticket/create') }}">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-12 l-none">
                                    <div class="note-container">
                                        <h6 class="p-2">{{ _lang('IMPORTANT:') }}</h6>
                                        <ol class="pl-4 pr-3" type="a">
                                            <li class="mb-2">
                                                {{ _lang('Please refrain from making inquiries regarding the same topic. Duplicate entries will be closed, and response to your question will be sent to the original ticket.') }}
                                            </li>
                                            <li class="mb-2">
                                                {{ _lang('If you have any corrections to your inquiry. please use the same thread. However, we would appreciate it if you could wait for our response first to avoid miscommunication.') }}
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group max-w-500">
                                        <label class="control-label"
                                            for="department">{{ _lang('Select Department') }}</label>
                                        <select class="form-control" name="department" id="department" required>
                                            <option value="">{{ _lang('Select Department') }}</option>
                                            <option value="customer_support">{{ _lang('Customer Support') }}</option>
                                            <option value="banking_support">{{ _lang('Banking Support') }}</option>
                                            <option value="technical_support">{{ _lang('Technical Support') }}</option>
                                            <option value="others">{{ _lang('Others') }}</option>
                                        </select>
                                        <span class="err-message">{{ _lang('Department is required.') }}</span>
                                    </div>
                                    <div class="form-group max-w-500">
                                        <label class="control-label" for="subject">{{ _lang('Subject') }}</label>
                                        <select class="form-control" name="subject" onchange="checkSubject(this.value);"
                                            id="subject" required>
                                            <option value="">{{ _lang('Select Subject') }}</option>
                                            <option value="deposit">{{ _lang('Deposit') }}</option>
                                            <option value="wire_transfer_deposit_request">
                                                {{ _lang('Wire Transfer Deposit Request') }}</option>
                                            <option value="withdrawal">{{ _lang('Withdrawal') }}</option>
                                            <option value="internal_transfer">{{ _lang('Internal Transfer') }}</option>
                                            <option value="payment_request">{{ _lang('Payment Request') }}</option>
                                            <option value="currency_exchange">{{ _lang('Currency Exchange') }}</option>
                                            <option value="card_top-up">{{ _lang('Card Top-up') }}</option>
                                            <option value="kyc_verification">{{ _lang('KYC Verification') }}</option>
                                            <option value="update_user_information_request">
                                                {{ _lang('Update User Information Request') }}</option>
                                            <option value="others">{{ _lang('Others') }}</option>
                                        </select>
                                        <input class="form-control mt-3" type="text" name="others" id="others"
                                            placeholder="{{ _lang('Others - Please enter the subject') }}"
                                            style='display:none;' />
                                        <span class="err-message">{{ _lang('Subject is required.') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6 d-none d-md-block">
                                    <div class="note-container">
                                        <h6 class="p-2">{{ _lang('IMPORTANT:') }}</h6>
                                        <ol class="pl-4 pr-3" type="a">
                                            <li class="mb-2">
                                                {{ _lang('Please refrain from making inquiries regarding the same topic. Duplicate entries will be closed, and response to your question will be sent to the original ticket.') }}
                                            </li>
                                            <li class="mb-2">
                                                {{ _lang('If you have any corrections to your inquiry. please use the same thread. However, we would appreciate it if you could wait for our response first to avoid miscommunication.') }}
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="message">{{ _lang('Message') }}</label>
                                <textarea class="form-control summernote-simple" id="message" rows="10" name="message" required></textarea>
                                <span class="err-message">{{ _lang('Message is required.') }}</span>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="attachment">{{ _lang('Attachment') }}</label>
                                <input type="file" class="form-control-file attachment mb-2" name="attachment[]">
                                <a class="d-inline-block" href="javaScript:void(0)" id="add-more">+ add more attachment</a>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-primary custom-btn-block"
                                    id="createTicket">{{ _lang('Send Ticket') }}</button>
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
        $(function() {
            $("#createTicket").on('click', function(event) {
                var validated = validateFields($('#createTicketForm'));
                if (validated) {
                    $("#preloader").css("display", "block");
                    $("#createTicketForm").submit();
                }
            });
            $("#add-more").on("click", function() {
                $("#add-more").before(
                    '<input type="file" class="form-control-file attachment mb-2" name="attachment[]">');
                if ($('.attachment').length == 5) {
                    $("#add-more").remove();
                }
            });
        });
        @if ($errors->ticket && $errors->ticket->all())
            toastr.error("{{ _lang('Error Occurred, Please try again !') }}");
        @endif

        @if (Session::has('ticket_error_response'))
            toastr.error("{{ session('ticket_error_response') }}");
        @endif

        @if (Session::has('ticket_response'))
            $("#main_modal .modal-title").text("{{ _lang('Ticket Created') }}");
            $("#main_modal .modal-body").text("{{ session('ticket_response') }}");
            $("#main_modal").modal("show");
        @endif

        function checkSubject(val) {
            var element = document.getElementById('others');
            if (val == 'others')
                element.style.display = 'block';
            else
                element.style.display = 'none';
            element.value = '';
        }
    </script>
@endsection
