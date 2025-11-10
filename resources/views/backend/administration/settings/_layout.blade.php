@extends('layouts.app')

@section('content')

    <div class="sb-page-header pb-10 sb-page-header-dark bg-gradient-primary-to-secondary">
        <div class="container-fluid">
            <div class="sb-page-header-content py-5">
                <h1 class="sb-page-header-title">
                    <div class="sb-page-header-icon"><i data-feather="settings"></i></div>
                    <span>{{ _lang('Settings') }}</span>
                </h1>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-n10">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs">
                            <li class="nav-item"><a class="nav-link {{ (request()->is('admin/administration/settings/general*')) ? 'active disabled' : '' }}" href="{{ url("admin/administration/settings/general") }}">{{ _lang('General Settings') }}</a></li>
                            {{--<li class="nav-item"><a class="nav-link {{ (request()->is('admin/administration/settings/banking*')) ? 'active disabled' : '' }}" href="{{ url("admin/administration/settings/banking") }}">{{ _lang('Banking Settings') }}</a></li>--}}
                            @can('settings_fees_view')
                            <li class="nav-item"><a class="nav-link {{ (request()->is('admin/administration/settings/fees*')) ? 'active disabled' : '' }}" href="{{ url("admin/administration/settings/fees") }}">{{ _lang('Fees Settings') }}</a></li>
                            <li class="nav-item"><a class="nav-link {{ (request()->is('admin/administration/settings/merchant-fees*')) ? 'active disabled' : '' }}" href="{{ url("admin/administration/settings/merchant-fees") }}">{{ _lang('Merchant Fees Settings') }}</a></li>
                            @endcan
                            {{--<li class="nav-item"><a class="nav-link {{ (request()->is('admin/administration/settings/email*')) ? 'active disabled' : '' }}" href="{{ url("admin/administration/settings/email") }}">{{ _lang('Email Settings') }}</a></li>--}}
                            {{--<li class="nav-item"><a class="nav-link {{ (request()->is('admin/administration/settings/sms*')) ? 'active disabled' : '' }}" href="{{ url("admin/administration/settings/sms") }}">{{ _lang('SMS Settings') }}</a></li>--}}
                            {{--<li class="nav-item"><a class="nav-link {{ (request()->is('admin/administration/settings/logo*')) ? 'active disabled' : '' }}" href="{{ url("admin/administration/settings/logo") }}">{{ _lang('Logo') }}</a></li>--}}
                            <li class="nav-item"><a class="nav-link {{ (request()->is('admin/administration/settings/notes*')) ? 'active disabled' : '' }}" href="{{ url("admin/administration/settings/notes") }}">{{ _lang('Note') }}</a></li>
                            <li class="nav-item"><a class="nav-link {{ (request()->is('admin/administration/settings/maintenance*')) ? 'active disabled' : '' }}" href="{{ url("admin/administration/settings/maintenance") }}">{{ _lang('Maintenance') }}</a></li>
                            @can('maintenance_service_view')
                            <li class="nav-item"><a class="nav-link {{ (request()->is('admin/administration/settings/service_maintenance*')) ? 'active disabled' : '' }}" href="{{ url("admin/administration/settings/service_maintenance") }}">{{ _lang('Payment Service Maintenance') }}</a></li>
                            @endcan
                        </ul>
                        <div class="tab-content">
                            @yield("form")
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

