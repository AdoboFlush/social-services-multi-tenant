@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">Reports</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item active">Reports</li>
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
        <div class="col-md-12">
            <div class="card">
              <ul class="nav nav-tabs mb-3">
                <li class="nav-item"><a class="nav-link {{ Request::is('reports/social_services/overview')  ? 'active' : ''}}" href="{{ url('reports/social_services/overview') }}">{{ _lang('Social Services Overview') }}</a></li>

                <li class="nav-item"><a class="nav-link {{ Request::is('reports/social_services/beneficiaries')  ? 'active' : ''}}" href="{{ url('reports/social_services/beneficiaries') }}">{{ _lang('Social Services Beneficiaries') }}</a></li>
              </ul>
                <div class="card-header">
                  @yield('card-header')
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        @yield('tab-content')
                    </div>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
        </div>
    </div>
</div>
@endsection