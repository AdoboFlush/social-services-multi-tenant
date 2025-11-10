@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">ID System</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item active">ID System</li>
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

                <li class="nav-item"><a class="nav-link {{ Request::is('id_system/members') || Request::is('id_system/members/*') ? 'active' : ''}}" href="{{ url('id_system/members') }}"><i class="fa fa-users mr-2"></i>{{ _lang('Member Information') }}</a></li>
                <li class="nav-item"><a class="nav-link {{ Request::is('id_system/requests') || Request::is('id_system/requests/*') ? 'active' : ''}}" href="{{ url('id_system/requests') }}"><i class="fa fa-id-card mr-2"></i>{{ _lang('ID Processing') }}</a></li>
                @can('id_template_view')
                <li class="nav-item"><a class="nav-link {{ Request::is('id_system/templates') || Request::is('id_system/templates/*') ? 'active' : ''}}" href="{{ url('id_system/templates') }}"><i class="fa fa-th mr-2"></i>{{ _lang('ID Templates') }}</a></li>
                @endcan
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