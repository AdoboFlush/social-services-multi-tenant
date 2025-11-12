@extends('layouts.login')

@section('content')

<div class="login-box">
    <div class="login-logo">
        <a href="#" class="h1"><img src="{{ asset('images/juan-connect-logo-black.png') }}" style="height:auto; width:220px;" /></a>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg"><strong>Member Information</strong></p>
            <form autocomplete="off" class="validate" action="{{ route('guest.register') }}" method="post">
                @csrf

                <div class="alert alert-warning">
                    Please remember the <b>Account number</b> shown here or take screenshot as this will be your username when you log in.
                </div>
                <label>Account number</label>
                <div class="input-group mb-3">
                    <input type="text" id="account_number" name="account_number" class="form-control{{ $errors->has('account_number') ? ' is-invalid' : '' }}" value="{{ $account_number ? $account_number : old('account_number') }}" disabled autocomplete="account_number" />
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

                <label>Password *</label>
                <div class="input-group mb-3">
                    <input type="password" id="password" name="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ _lang('Password') }}" value="{{ old('password') }}" required autocomplete="new-password">
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

                <label>Confirm password *</label>
                <div class="input-group mb-3">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control{{ $errors->has('confirm_password') ? ' is-invalid' : '' }}" placeholder="{{ _lang('Confirm password') }}" value="{{ old('confirm_password') }}" required autocomplete="new-password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    @if ($errors->has('confirm_password'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('confirm_password') }}</strong>
                    </span>
                    @endif
                </div>

                <div class="input-group">
                    <input type="hidden" name="member_code" id="member_code" class="form-control" value="{{ isset($_GET['code']) ? $_GET['code'] : '' }}" required />
                    @if ($errors->has('member_code'))
                    <span class="alert alert-danger" style="font-size:12px;">
                        <strong> {{ $errors->first('member_code') }}</strong>
                    </span>
                    @endif
                </div>

                <label>First name *</label>
                <div class="input-group mb-3">
                    <input type="text" id="first_name" name="first_name" placeholder="{{ _lang('Enter your first name') }}" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" value="{{ old('first_name') }}" required>
                    @if ($errors->has('first_name'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('first_name') }}</strong>
                    </span>
                    @endif
                </div>

                <label>Middle name</label>
                <div class="input-group mb-3">
                    <input type="text" id="middle_name" name="middle_name" class="form-control{{ $errors->has('middle_name') ? ' is-invalid' : '' }}" placeholder="{{ _lang('Enter middle name') }}" value="{{ old('middle_name') }}">
                    @if ($errors->has('middle_name'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('middle_name') }}</strong>
                    </span>
                    @endif
                </div>

                <label>Last name *</label>
                <div class="input-group mb-3">
                    <input type="text" id="last_name" name="last_name" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" placeholder="{{ _lang('Enter your last name') }}" value="{{ old('last_name') }}" required>
                    @if ($errors->has('last_name'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('last_name') }}</strong>
                    </span>
                    @endif
                </div>

                <label>Date of birth *</label>
                <div class="input-group mb-3">
                    <input type="text" id="birth_date" name="birth_date" class="datepicker form-control{{ $errors->has('birth_date') ? ' is-invalid' : '' }}" placeholder="{{ _lang('Enter your date of birth') }}" value="{{ old('birth_date') }}" required>
                    @if ($errors->has('birth_date'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('birth_date') }}</strong>
                    </span>
                    @endif
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-login btn-block">
                        {{ _lang('Submit') }}
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>

@endsection