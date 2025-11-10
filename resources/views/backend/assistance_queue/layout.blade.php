@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Queueing System</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item active">Queueing System</li>
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
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('assistance-queue.index') ? 'active' : '' }}" href="{{ route('assistance-queue.index', [], false) }}"><i class="fa fa-poll mr-2"></i>{{ _lang('Queue List') }}</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('assistance-queue.table') ? 'active' : '' }}" href="{{ route('assistance-queue.table', [], false) }}"><i class="fa fa-poll mr-2"></i>{{ _lang('Queue Table') }}</a></li>
                </ul>
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