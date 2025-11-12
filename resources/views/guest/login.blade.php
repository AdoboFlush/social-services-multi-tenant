@extends('layouts.login')

@section('content')

<div class="login-box">
    <div class="login-logo">
        <a href="#" class="h1"><img src="{{ asset('images/juan-connect-logo-black.png') }}" style="height:auto; width:220px;" /></a>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Sign in to start your session</p>
            <form method="POST" autocomplete="off" class="form-signin validate" action="{{ route('guest.login') }}">
                @csrf

                <div class="input-group mb-3">
                    <input type="text" id="account_number" name="account_number" class="form-control{{ $errors->has('account_number') ? ' is-invalid' : '' }}" placeholder="{{ _lang('AA-XXXXXXXXXX') }}" value="{{ old('account_number') }}" required autofocus />
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                    @if ($errors->has('account_number'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('account_number') }}</strong>
                    </span>
                    @endif
                </div>

                <div class="input-group mb-3">
                    <input type="password" id="password" name="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ _lang('Password') }}" required>
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
                    <div class="col-md-12">
                        <p>If not yet registered, click <a href="{{ route("guest.landing")}}">Register</a>.</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-login btn-block">
                            {{ _lang('Login') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection