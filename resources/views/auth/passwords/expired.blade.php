@extends('layouts.login')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                <div class="card-body text-center">
                    @if (isset($resent) && $resent)
                    <div class="alert alert-success" role="alert">
                       {{ _lang('A new link has been sent to your email address.') }}
                    </div>
                    @endif
                    {{ _lang('Your Create Password link has expired.') }}
                    {{ _lang('To generate a new one, please click ') }} <a href="/password/resend/{{ $email }}">here</a>.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection