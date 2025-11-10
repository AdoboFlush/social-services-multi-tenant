@extends('layouts.login')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-signin my-5">
                <div class="card-body">
                    @if(Session::has('errors'))
                        @foreach ($errors->all() as $error)
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <strong>{{ $error }}</strong>
                        </div>
                        @endforeach
                    @endif

                        <h5 class="card-title text-center">{{ _lang('Reset Password') }}</h5>
                    <form method="POST" class="form-signin" action="{{ route('password.email') }}" autocomplete="off">
                        @csrf

                        <div class="form-group row">
                            <div class="col-md-12">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" placeholder="{{ _lang('Enter your Email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
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

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-3">`
                                <button type="submit" class="btn btn-primary btn-login btn-block">
                                    {{ _lang('Send Password') }}
                                </button>
                            </div>
                        </div>
                        <div class="form-group row mt-3">
                            <div class="col-md-12 text-center text-white-on-dark">
                                <a class="text-primary text-underline" href="{{ url('login') }}">
                                    {{ _lang('Go to login page.') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="language-switcher">
    <a class="btn sb-btn-icon sb-btn-transparent-dark dropdown-toggle" id="navbarDropdownLanguages" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i data-feather="globe"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-right border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownLanguages">
        <a class="dropdown-item language-option" href="{{ url('language/english') }}" data-lang="english">{{ _lang('English') }}</a>
        <a class="dropdown-item language-option" href="{{ url('language/japanese') }}" data-lang="japanese">{{ _lang('Japanese') }}</a>
    </div>
</div>

@if (session('status'))
    <div id="successModal" class="modal  fade in" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h4>{{ _lang('Forgot Password') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>
                        {{ _lang(session('status')) }}
                    </p>
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-danger" data-dismiss="modal" href="{{ url('login') }}">{{ _lang("OK") }}</a>
                </div>
            </div>
        </div>
@endif

@endsection

@section('js-script')
    {!! NoCaptcha::renderJs() !!}
    <script>
        @if (session('status'))
        $('#successModal').modal("show");
        window.setTimeout(function() {
            window.location.href = '{{ url("login") }}';
        }, 10000);
        @endif
    </script>
@endsection