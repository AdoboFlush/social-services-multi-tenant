@extends('layouts.default')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8  text-center">
                <br /><br /><br /><br /><br />
                <img src="{{ asset('images/logos/oriental-logo-white.jpg') }}" style="width:40%">
            </div>

            <div class="col-md-6">
                <div class="card card-signin my-5">
                    <div class="card-body text-center">
                        <div class="well" style="background-color:#f7f5f5;">
                            <div class="row">
                                <div class="col-3" style="vertical-align: middle;">
                                    <br />
                                    <img src="{{ asset('images/ok-48.png') }}">
                                </div>
                                <div class="col-9 text-left">
                                    <br />
                                    {{ _lang('Account is now Verified.') }} <br />
                                    <a href="{{ url('login') }}">
                                        {{ _lang('Go to login page.') }}
                                    </a>
                                    <br /> <br />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
