@extends('layouts.app')

@php
$filter_source = request()->get('source');
@endphp

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
@endsection

@section('content')

<div class="container-fluid">
    <div class="row">
        @foreach(request()->attributes->get('tenant_children') as $tenant)
        <div class="col-md-3">
            <div class="card card-widget widget-user">
                <div class="widget-user-header bg-olive">
                    <h3 class="widget-user-username">{{$tenant['name']}}</h3>
                    <h5 class="widget-user-desc">{{$tenant['description']}}</h5>
                </div>
                <div class="widget-user-image">
                    <img class="img-circle elevation-2" src="{{ asset('images/juan-connect-favicon.png') }}" alt="User Avatar" style="background-color: #e0e0e0; display: block;">
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-sm-4 border-right">
                            <div class="description-block">
                                <h5 class="description-header">3,200</h5>
                                <span class="description-text">Registered Voters</span>
                            </div>
                        </div>
                        <div class="col-sm-4 border-right">
                            <div class="description-block">
                                <h5 class="description-header">13,000</h5>
                                <span class="description-text">Assistances Released</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="description-block">
                                <h5 class="description-header">35</h5>
                                <span class="description-text">Pending Assistances</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-12">
                            <a class="btn btn-block btn-default" target="_blank" href="{{$tenant['domains'][0]}}"><i class="fa fa-link mr-2"></i> Go to {{$tenant['name']}} Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.widget-user -->
        </div>
        @endforeach
    </div>
</div>

@endsection


@section('js-script')
<script src="{{ asset('adminLTE/plugins/chart.js/Chart.min.js') }}"></script>

@endsection