@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Poll Management</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item active">Poll Management</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
@endsection

@section('content')

<style>
    .highlight-field {
        background-color: yellow;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('poll.elections.overview') ? 'active' : '' }}" href="{{ route('poll.elections.overview', [], false) }}"><i class="fa fa-poll mr-2"></i>{{ _lang('Overview') }}</a></li>

                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('poll.elections.index') ? 'active' : '' }}" href="{{ route('poll.elections.index', [], false) }}"><i class="fa fa-poll mr-2"></i>{{ _lang('Elections') }}</a></li>

                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('poll.candidates.index') ? 'active' : '' }}" href="{{ route('poll.candidates.index', [], false) }}"><i class="fa fa-poll mr-2"></i>{{ _lang('Candidates') }}</a></li>

                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('poll.watchers.index') ? 'active' : '' }}" href="{{ route('poll.watchers.index', [], false) }}"><i class="fa fa-th mr-2"></i>{{ _lang('Watchers') }}</a></li>

                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('poll.entries.index') ? 'active' : '' }}" href="{{ route('poll.entries.index', [], false) }}"><i class="fa fa-poll mr-2"></i>{{ _lang('Entries') }}</a></li>

                    <li class="nav-item"><a class="nav-link {{ request()->is('poll/reports/*') ? 'active' : '' }}" href="{{ route('poll.reports.candidates_per_brgy', [], false) }}"><i class="fa fa-chart-bar mr-2"></i>{{ _lang('Reports') }}</a></li>
                </ul>

                <div class="card-header">
                    @yield('card-header')
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        @yield('tab-content')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection