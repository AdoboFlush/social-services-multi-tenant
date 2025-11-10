@extends('layouts.app')

@section('content')
    <div class="sb-page-header pb-2 sb-page-header-dark bg-gradient-primary-to-secondary">
        <div class="container-fluid">
            <div class="sb-page-header-content py-3">
                <h1 class="sb-page-header-title">
                    <div class="sb-page-header-icon"><i data-feather="shield"></i></div>
                    <span>{{ _lang('Master Password') }}</span>
                </h1>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title panel-title">{{ _lang('Master Password') }}</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <label>{{ _lang('Master Password allows you to set a deeper protection to your account. Aside from the 2-step email verification, this feature will allow you to further confirm account access before proceeding with a transaction. The Master Password will be required prior to using Oriental Wallet services such as Internal Transfer, approving of Payment Request, Currency Exchange, and Withdrawal.') }}</label>
                        </div>
                    </div>
                    @if(isset(Auth::user()->security->status) && Auth::user()->security->status && Auth::user()->security->password)
                        <div class="row my-3">
                            <div class="col">
                                <button type="button" class="btn btn-primary" id="resetPassword">
                                    {{ _lang('Reset Password') }}
                                </button>
                            </div>
                        </div>
                        @php
                            $datetime = [
                                'year' => Auth::user()->security->updated_at->format('Y'),
                                'month' => Auth::user()->security->updated_at->format('m'),
                                'day' => Auth::user()->security->updated_at->format('d'),
                                'time' => Auth::user()->security->updated_at->format('H:i:s')
                            ]
                        @endphp
                        <label>{!! _lang("Your password has been updated on {year}-{month}-{day} {time}",$datetime) !!} </label>
                    @elseif(isset(Auth::user()->security->status) && Auth::user()->security->status)
                    <form method="POST" class="validate" autocomplete="off" action="{{ url('user/security_settings') }}" id="form_security">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12 col-lg-6 mt-3 max-w-500">
                                <label for="password" class="control-label">{{ _lang('Password') }}<span class="required"> *</span></label>
                                <div class="input-group show-hide-password">
                                    <input id="password" type="password" class="form-control" name="password" required>
                                    <div class="input-group-append">
                                        <div class="input-group-text cursor-pointer btn-show-hide-password">
                                            <i data-feather="eye-off"></i>
                                        </div>
                                    </div>
                                </div>
                                <span class="v-error" id="password_error"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-lg-6 mt-3 max-w-500">
                                <label for="password_confirmation" class="control-label">{{ _lang('Confirm Password') }}<span class="required"> *</span></label>
                                <div class="input-group show-hide-password">
                                    <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
                                    <div class="input-group-append">
                                        <div class="input-group-text cursor-pointer btn-show-hide-password">
                                            <i data-feather="eye-off"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="button" class="btn btn-primary" id="createPassword">
                                    {{ _lang('Create Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                    @else
                    <div class="row my-3">
                        <div class="col">
                            <label>{{ _lang("Please contact us at merchant@orientalwallet.com to have this feature enabled to your account.") }}</label>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if(Auth::user()->security->id)
        @include('backend.security.modal.reset')
    @endif
@endsection

@section('js-script')
    <script type="text/javascript">
        @if(session('message'))
        $(window).on('load', function() {
            var title = '{{ _lang("Master Password") }}';
            var body = '{{ session('message') }}';
            $('#main_modal .modal-title').html(title);
            $('#main_modal .modal-body').html(body);
            $('#main_modal').modal('show');
        });
        @endif

        function sendTwoFactor(){
            var title = "{{ _lang('Two Factor Authentication') }}";
            var data = {
                'two_fa_type' : 'security_password',
                'password' : $('input[name="password"]').val()
            };
            $.ajax({
                url: '{{ url('user/generate_code') }}',
                method: 'POST',
                data: data,
                beforeSend: function(){
                    $("#preloader").css("display","block");
                },success: function(data){
                    $("#reset_password_modal").modal('hide');
                    $("#preloader").css("display","none");
                    $('#main_modal .modal-title').html(title);
                    $('#main_modal .modal-body').html(data);
                    $('#main_modal').modal('show');
                },
                error: function (request, status, error) {
                    var error = JSON.parse(request.responseText);
                    setTimeout(function(){
                        $(window).scrollTop(0);
                    }, 500);
                    $("#preloader").css("display","none");
                    $('#alert-error').removeClass('d-none').find('strong').html(error.message)
                }
            });
        }
        $("#createPassword").on('click',function(e){
            e.preventDefault();
            var data = $('#form_security').serialize();
            $.ajax({
                url: "{{ url('user/security_settings') }}",
                method: 'POST',
                data: data,
                beforeSend: function(){
                    $("#preloader").css("display","block");
                },success: function(data){
                    if(data.status){
                        $("#password_error").hide();
                        sendTwoFactor()
                    }else{
                        $("#preloader").css("display","none");
                        $("#password_error").html(data.error);
                        toastr.error(data.error)
                    }
                },
                error: function (request, status, error) {
                    console.log(request.responseText);
                    $("#preloader").css("display","none");
                }
            });
        });
        $("#resetPassword").on('click',function(e){
            $("#reset_password_modal").modal('show');
            $("#form_security").trigger("reset");
        });

    </script>
@endsection