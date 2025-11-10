@extends('layouts.login')

@section('content')

<div class="login-box" style="width:100%; max-width:460px; margin:auto;">
    <!-- /.login-logo -->
    <div class="card card-outline card-success">
        <div class="card-header text-center" style="display: flex; justify-content: center; align-items: center; flex-wrap: nowrap; gap: 10px;">
            <a href="#" class="h1">
                <img src="{{ asset('images/juan-connect-logo-black.png') }}" style="max-height:80px; max-width:100%; height:auto; width:auto;" />
            </a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Sign in to start your session</p>
            
            @if ($errors->has('closed_email'))
                <div class="alert alert-danger">
                    <small><i class="mdi mdi-information-outline"></i>{!! _lang($errors->first('closed_email'),['url' => $errors->first('url')]) !!}</small>
                </div>
            @elseif(Session::has("error"))
                <div class="alert alert-danger alert-small">{{ session("error") }}</div>
            @endif
            
            <form method="POST" autocomplete="off" class="form-signin validate" action="{{ Request::fullUrl() }}">

                @csrf

                <div class="input-group mb-3">
                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="{{ _lang('Email') }}" required autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    @if ($errors->has('email'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="input-group mb-3">
                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="{{ _lang('Password') }}" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    @if ($errors->has('password'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" name="remember" class="" id="remember" {{ old('remember') ? 'checked' : '' }}>
							<label for="remember">{{ _lang('Remember Me') }}</label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-login btn-block">
                            {{ _lang('Login') }}
                        </button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            {{-- <p class="mb-1">
                <a href="{{ route('password.request') }}">
                    {{ _lang('Forgot Password?') }}
                </a>
            </p>
            <p class="mb-0">
                @if(get_option('allow_singup','yes') == 'yes')
                    <a class="text-center" href="{{ url('register') }}">
                        {{ _lang('Open an Account') }}
                    </a>
                @endif
            </p> --}}

        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<!-- /.login-box -->
@endsection