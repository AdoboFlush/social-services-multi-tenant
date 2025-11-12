@extends('layouts.login')

@section('content')

<div class="login-box">
    <div class="login-logo">
        <a href="#" class="h1"><img src="{{ asset('images/juan-connect-logo-black.png') }}" style="height:auto; width:220px;" /></a>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <p><strong>Please enter your member code:</strong></p>
            <p>If you are registered already, click <a href="{{ route("guest.login")}}">Login</a>.</p>
            <form autocomplete="off" class="form-signin validate" action="{{ route('check.code') }}" method="post">

                @csrf

                <div class="input-group mb-3">
                    <input type="text" id="member_code" name="member_code" class="form-control{{ $errors->has('member_code') ? ' is-invalid' : '' }}" placeholder="{{ _lang('Enter your code here') }}" required autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-code"></span>
                        </div>
                    </div>
                    @if ($errors->has('member_code'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('member_code') }}</strong>
                    </span>
                    @endif
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-login btn-block">
                            {{ _lang('Enter') }}
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection