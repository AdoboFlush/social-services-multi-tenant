@extends('layouts.login')
@section('content')

<div class="register-box">
<div class="card card-outline card-primary">
    <div class="card-header text-center">
        <a href="../../index2.html" class="h1"><b>Standard</b>App</a>
    </div>
    <div class="card-body">
    <p class="login-box-msg">{{ _lang('Create Your Account Now') }}</p>

    <form method="POST" class="form-signin" autocomplete="off" action="{{ route('register', [], false) }}" id="registration-form">
        @csrf
        <input type="hidden" name="language" value="{{ $language }}">

        <div class="form-group row">
            <div class="col-md-12">
                <input id="first_name" type="text" placeholder="{{ _lang('First Name') }}" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }} alphabet-only" name="first_name" value="{{ old('first_name') }}" required autofocus>
                @if ($errors->has('first_name'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('first_name') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-12">
                <input id="last_name" type="text" placeholder="{{ _lang('Last Name') }}" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }} alphabet-only" name="last_name" value="{{ old('last_name') }}" required autofocus>

                @if ($errors->has('last_name'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('last_name') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-12">
                <input id="email" type="email" placeholder="{{ _lang('E-Mail Address') }}" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>

                @if ($errors->has('email'))
                    <span class="invalid-feedback">
                        <strong>{{ _lang($errors->first('email')) }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-12">
                <div class="input-group show-hide-password">
                    <input id="password" type="password" placeholder="{{ _lang('Password') }}" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                    <div class="input-group-append">
                        <div class="input-group-text cursor-pointer btn-show-hide-password">
                            <i data-feather="eye-off"></i>
                        </div>
                    </div>
                </div>
                @if ($errors->has('password'))
                    <span class="invalid-feedback">
                        <strong>{{ _lang($errors->first('password')) }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-12">
                <div class="input-group show-hide-password">
                    <input id="password-confirm" type="password" class="form-control" placeholder="{{ _lang('Confirm Password') }}" name="password_confirmation" required>
                    <div class="input-group-append">
                        <div class="input-group-text cursor-pointer btn-show-hide-password">
                            <i data-feather="eye-off"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-5 pr-1">
                <select class="select2 form-control pl-2 {{ $errors->has('date_of_birth') ? 'is-invalid' : '' }}" id="month" name="month" required>
                    <option value="">{{ _lang("Month") }}</option>
                    <option value="1" @if(old('month') == 1) selected @endif>{{ _lang("January") }}</option>
                    <option value="2" @if(old('month') == 2) selected @endif>{{ _lang("February") }}</option>
                    <option value="3" @if(old('month') == 3) selected @endif>{{ _lang("March") }}</option>
                    <option value="4" @if(old('month') == 4) selected @endif>{{ _lang("April") }}</option>
                    <option value="5" @if(old('month') == 5) selected @endif>{{ _lang("May") }}</option>
                    <option value="6" @if(old('month') == 6) selected @endif>{{ _lang("June") }}</option>
                    <option value="7" @if(old('month') == 7) selected @endif>{{ _lang("July") }}</option>
                    <option value="8" @if(old('month') == 8) selected @endif>{{ _lang("August") }}</option>
                    <option value="9" @if(old('month') == 9) selected @endif>{{ _lang("September") }}</option>
                    <option value="10" @if(old('month') == 10) selected @endif>{{ _lang("October") }}</option>
                    <option value="11" @if(old('month') == 11) selected @endif>{{ _lang("November") }}</option>
                    <option value="12" @if(old('month') == 12) selected @endif>{{ _lang("December") }}</option>
                </select>
            </div>
            <div class="col-3 px-1">
                <select class="select2 form-control pl-2 {{ $errors->has('date_of_birth') ? 'is-invalid' : '' }}" id="day" name="day" required>
                    <option value="">{{ _lang('Day') }}</option>
                </select>
            </div>
            <div class="col-4 pl-1">
                <select class="select2 form-control pl-2 {{ $errors->has('date_of_birth') ? 'is-invalid' : '' }}" id="year" name="year" required></select>
            </div>
            <div class="col-md-12">
                <input class="form-control {{ $errors->has('date_of_birth') ? 'is-invalid' : '' }}" type="hidden" id="date_of_birth" name="date_of_birth">
                @if ($errors->has('date_of_birth'))
                    <span class="invalid-feedback">
                        <strong>{{ _lang($errors->first('date_of_birth')) }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-12">
                <select id="country_of_residence" class="form-control select2{{ $errors->has('country_of_residence') ? ' is-invalid' : '' }}" name="country_of_residence" required>
                   <option value="">{{ _lang('Country') }}</option>
                   @foreach ($countries as $country)
                       <option value="{{ $country->name }}" @if(old('country_of_residence') == $country->name) selected @endif>{{ $country->name }}</option>
                   @endforeach
                </select>

                @if ($errors->has('country_of_residence'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('country_of_residence') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-12">
                <input type="hidden" name="termsValue" @if(old('termsValue')) value="{{ old('termsValue') }}" @endif hidden>
                <div class="form-check registration-newsletter">
                    <input class="form-check-input {{ $errors->has('terms_conditions') ? 'is-invalid' : '' }}" type="checkbox" id="terms_conditions_checkbox" name="terms_conditions">
                    <label for="terms_conditions_checkbox">
                        @php
                            if(isset($_COOKIE['selected_lang']) && $_COOKIE['selected_lang'] == 'english'){
                                $terms_url = 'https://orientalwallet.com/home/terms-and-conditions/';
                                $policy_url = 'https://orientalwallet.com/home/cookie-privacy-policy/';
                            }elseif(isset($_COOKIE['selected_lang']) && $_COOKIE['selected_lang'] == 'japanese'){
                                $terms_url = 'https://orientalwallet.com/jp/terms-and-conditions/';
                                $policy_url = 'https://orientalwallet.com/jp-cookie-privacy-policy/';
                            }else{
                                $terms_url = 'https://orientalwallet.com/home/terms-and-conditions/';
                                $policy_url = 'https://orientalwallet.com/home/cookie-privacy-policy/';
                            }
                        @endphp
                        {!! _lang("I agree to Oriental Wallet's <u><a href='{terms_url}' target='terms_tab_1'>Terms & Conditions</a></u> and <u><a href='{policy_url}' target='terms_tab_2'>Privacy Policy.</a></u>",
                            [
                                'terms_url' => $terms_url,
                                'policy_url' => $policy_url,
                            ])
                        !!}
                    </label>
                    <span class="invalid-feedback fs-10">
                        <strong>{{ _lang('Please indicate that you have read and agree to the Terms and Conditions and Privacy Policy.') }}</strong>
                    </span>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-12">
                <input type="hidden" name="newsletterValue" @if(old('newsletterValue')) value="{{ old('newsletterValue') }}" @endif hidden>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="receive_updates_checkbox" name="newsletter">
                    <label for="receive_updates_checkbox">
                        {{ _lang("I agree to receive email news letters about Oriental Wallet's products and services.") }}
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-12">
                <div class="form-group{{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }}">
                        {!! app('captcha')->display() !!}
                        @if ($errors->has('g-recaptcha-response'))
                            <span class="invalid-feedback d-block  text-center">
                                <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                            </span>
                        @endif
                </div>
            </div>
        </div>

        <input type="hidden" name="ref" value="{{ isset($_GET['ref']) ? $_GET['ref'] : '' }}"/>

        <div class="form-group row mt-5">
            <div class="col-md-12 text-center">
                <a class="btn btn-primary color-primary" id="btn-create">{{ _lang('Create My Account') }}</a>
            </div>
        </div>

        <div class="form-group row mt-5 ">
            <div class="col-md-12 text-center text-white-on-dark">
                {{ _lang('Already have an account?') }}
                <a href="{{ url('login') }}">
                    {{ _lang('Log in here') }}
                </a>
            </div>
        </div>
        <!-- Login Modal -->
        <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">{{ _lang('Registration') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    {{ _lang("Are you sure you want to create an account without receiving news and important announcements?") }}
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger btn-login" id="btn-create-confirm">OK</button>
                </div>
            </div>
            </div>
        </div>

    </form>

    <!-- /.form-box -->
</div><!-- /.card -->
</div>
<!-- /.register-box -->

@if (!empty($user))
    <!-- Registration Success Modal -->
    <div id="successModal" class="modal  fade in" role="dialog">
        <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
            <h3>{{ _lang('Registration Successful') }}</h3>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            <p> {{ _lang("You have successfully created your account. An email has been sent to {email} with instruction on how to verify your email.",['email' => $user->email]) }}</p>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal"> {{ _lang("Close") }}</button>
            </div>
        </div>
    </div>

@endif

@endsection

@section('js-script')

{!! NoCaptcha::renderJs() !!}

<script>
    $(function(){

        var year = $("#year").val() ? $("#year").val() : $("#year option:eq(1)").val();
        var month = $("#month").val();
        var oldDay = '{{ old('day') }}';
        var oldYear = '{{ old('year') }}';
        let newsletter = '{{ old('newsletterValue') }}';
        let terms = '{{ old('termsValue') }}';

        if(newsletter == 'false'){
            $('#receive_updates_checkbox').prop('checked', false);
            terms = false;
            
            $('#terms_conditions_checkbox').prop('checked', false);
            newsletter = false;
        }else{
            $('#receive_updates_checkbox').prop('checked', true);
            newsletter = true;

            $('#terms_conditions_checkbox').prop('checked', true);
            terms = true;
        }

        if(month){
            var totalDays = new Date(year, month, 0).getDate();
            var days = "<option value=''>{{ _lang('Day') }}</option>";
            for(var i=1; i<=totalDays; i++){
                if(oldDay == i){
                    days += "<option selected value='" + i + "'>"+i+"</option>";
                } else
                    days += "<option value='" + i + "'>"+i+"</option>";
            }
            $("#day").html(days);
            if(oldYear)
                $('#year').find('option[value=' +oldYear+']').attr("selected",true);

        }

        $("#month").on("change",function(){
            var year = $("#year").val() ? $("#year").val() : $("#year option:eq(1)").val();
            var month = $(this).val();
            var totalDays = new Date(year, month, 0).getDate();
            var days = "<option value=''>{{ _lang('Day') }}</option>";
            for(var i=1; i<=totalDays; i++){
                days += "<option value='" + i + "'>"+i+"</option>";
            }
            $("#day").html(days);
        });

        $("#year").on("change",function(){
            var year = $(this).val();
            var month = $("#month").val();
            var totalDays = new Date(year, month, 0).getDate();
            if($("#day option:last").val() != totalDays){
                var days = "<option value=''>{{ _lang('Day') }}</option>";
                for(var i=1; i<=totalDays; i++){
                    days += "<option value='" + i + "'>"+i+"</option>";
                }
                $("#day").html(days);
            }
        });

        $("#registration-form").on("submit",function(e){
            var year = $("#year").val();
            var month = $("#month").val();
            var day = $("#day").val();
            if(year && month && day){
                $("#date_of_birth").val(year+"-"+month+"-"+day);
            } else {
                e.preventDefault(e);
            }

        });

        $("#btn-create").on("click", function(event){
            let formIsValid  = document.querySelector('form').reportValidity()
            if(formIsValid && terms){
                if($('#receive_updates_checkbox').is(':checked')){
                    $('#registration-form').submit();
                }else{
                    $('#loginModal').modal("toggle")
                }
            }

            if(!terms){
                $('.registration-newsletter span').addClass('d-block');
            }
        });

        $("#btn-create-confirm").on("click", function(event){
            let formIsValid  = document.querySelector('form').reportValidity()
            if(formIsValid){
                if(terms){
                    $('#registration-form').submit();
                }else{
                    event.preventDefault();
                    $('.registration-newsletter span').addClass('d-block');
                }
            }else{
                this.reportValidity();
            }

            $('#loginModal').modal("hide");
        });

        $('#receive_updates_checkbox').on('click', function(){
            newsletter = !newsletter;
            $('input[name=newsletterValue]').val(newsletter)
        })

        $('#terms_conditions_checkbox').on('click', function(){
            terms = !terms;
            $('input[name=termsValue]').val(terms)
            if(terms){
                $('.registration-newsletter span').removeClass('d-block');
            }else{
                $('.registration-newsletter span').addClass('d-block');
            }
        })

    });
<?php 
    if(isset($user)): 
?>
    $('#successModal').modal("show");
<?php 
    unset($user);
    endif; 
?>
</script>
@endsection
