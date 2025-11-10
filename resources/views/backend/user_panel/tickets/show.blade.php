@extends('layouts.app')

@section('content')
    <div class="sb-page-header pb-2 sb-page-header-dark bg-gradient-primary-to-secondary">
        <div class="container-fluid">
            <div class="sb-page-header-content py-3">
                <h1 class="sb-page-header-title">
                    <div class="sb-page-header-icon"><i data-feather="mail"></i></div>
                    <span>{{ _lang('My Tickets') }}</span>
                </h1>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-4">
        <div class="row card pb-4">
            <div class="col-12 mb-3">
                <div class="d-none d-md-block">
                    <div class="card-body">
                        <h4 class="card-title panel-title">{{ _lang('Ticket ID:') }} {{ $ticket->id }}</h4>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>{{ _lang("Subject") }}</th>
                                <th>{{ _lang("Department") }}</th>
                                <th>{{ _lang("Date Ticket Created") }}</th>
                                <th>{{ _lang("Date Updated") }}</th>
                                <th>{{ _lang("Status") }}</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr id="{{ $ticket->id }}">
                                    <td>{{ toWords($ticket->subject) }}</td>
                                    <td>{{ toWords($ticket->department) }}</td>
                                    <td>{{ $ticket->created_at }}</td>
                                    <td>{{ $ticket->updated_at }}</td>
                                    <td>{{ ticketTranslate($ticket->status) }}</td>
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
                            {{ toWords($ticket->subject) }}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-12">
                            <strong>{{ _lang('Department') }}:</strong>
                            {{ toWords($ticket->department) }}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-12">
                            <strong>{{ _lang('Status') }}:</strong>
                            {{ _lang(ucwords($ticket->status)) }}
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
                    <form enctype="multipart/form-data" method="post" id="createTicketForm" autocomplete="off" action="{{ url('user/ticket/edit/'.$ticket->id) }}">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label class="control-label" for="message">{{ _lang('Message') }}</label>
                            <textarea class="form-control summernote-simple" id="message" rows="3" name="message" required></textarea>
                            <span class="err-message">{{ _lang('Message is required.') }}</span>
                        </div>
                        <div class="form-group">
                            <input type="file" class="form-control-file attachment mb-2" name="attachment[]">
                            <a class="d-inline-block" href="javaScript:void(0)" id="add-more">+ add more attachment</a>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-primary custom-btn-block" id="createTicket">{{ _lang("Send") }}</button>
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

            @if(Session::has("ticket_error_response"))
                toastr.error("{{ session("ticket_error_response") }}");
            @endif

            $("#createTicket").on('click',function(event){
                var validated = validateFields($('#createTicketForm'));
                if(validated) {
                    $("#preloader").css("display","block");
                    $("#createTicketForm").submit();
                }
            });
            $("#add-more").on("click",function(){
                $("#add-more").before('<input type="file" class="form-control-file attachment mb-2" name="attachment[]">');
                if($('.attachment').length == 5){
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
    </script>
@endsection