@extends('layouts.login')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                <div class="card-header"><h5 class="card-title">{{ _lang('Create Password') }}</h5></div>
                    <div class="card-body">

                        @if(Session::has('errors'))
                            @foreach ($errors->all() as $error)
                                <div class="alert alert-danger">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <strong>{{ $error }}</strong>
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
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>`

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">{{ _lang('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ _lang('Confirm Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary btn-login">
                                        {{ _lang('Create Password') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@if (session('status'))
    <div id="successModal" class="modal  fade in" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h3>{{ _lang('Forgot Password') }}</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>
                        {!! _lang("Your password has been reset. Click <a href='{url}'>here</a> to login.",["url"=>url('/login')]) !!}
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">{{ _lang("OK") }}</button>
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