@extends('layouts.login')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <?php if (isset($is_expired) && !empty($is_expired)) { ?>
                        <div class="card-body">
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <strong>{{ $is_expired }}</strong>
                            </div>
                        </div>
                    <?php } else { ?>
                    <div class="card-header"><h5 class="card-title">{{ _lang('Reset Password') }}</h5></div>
                    <div class="card-body">

                        @if(Session::has('errors'))
                            @foreach ($errors->all() as $error)
                                <div class="alert alert-danger">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <strong>{{ _lang($error) }}</strong>
                                </div>
                            @endforeach
                        @endif

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ _lang('Verification Code') }}</label>

                                <div class="col-md-6">
                                    <input id="verification_code" type="text" class="form-control"  name="verification_code" value="" required>
                                    <input id="email" type="hidden" class="form-control"  name="email" value="{{ $email }}" required>
                                    @error('verification_code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ _lang($message) }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>`

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">{{ _lang('Password') }}</label>

                                <div class="col-md-6">
                                    <div class="input-group show-hide-password">
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                        <div class="input-group-append">
                                            <div class="input-group-text text-secondary cursor-pointer btn-show-hide-password">
                                                <i data-feather="eye-off"></i>
                                            </div>
                                        </div>
                                    </div>

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ _lang($message) }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ _lang('Confirm Password') }}</label>

                                <div class="col-md-6">
                                    <div class="input-group show-hide-password">
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                        <div class="input-group-append">
                                            <div class="input-group-text text-secondary cursor-pointer btn-show-hide-password">
                                                <i data-feather="eye-off"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary btn-login">
                                        {{ _lang('Reset Password') }}
                                    </button>
                                </div>
                            </div>
                            <div class="form-group row mt-3">
                                <div class="col-md-6 offset-md-4 text-white-on-dark">
                                    <a class="text-primary text-underline" href="{{ url('login') }}">
                                        {{ _lang('Go to login page.') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php } ?>
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
                        {!! _lang("Your password has been reset. Click <a href='{url}'>here</a> to login.",["url"=>url('/login')]) !!}
                    </p>
                </div>
            </div>
        </div>
@endif
@endsection

@section('js-script')
    <script>
        @if (session('status'))
        $('#successModal').modal("show");
        @endif
    </script>
@endsection