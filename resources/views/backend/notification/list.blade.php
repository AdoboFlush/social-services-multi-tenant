@extends('layouts.app')
@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">Notifications</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item active">Notifications</li>
      </ol>
    </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
@endsection

@section('content')
<div class="container-fluid">
    <section class="content">
        <div class="row">
          
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card card-success card-outline">
              <div class="card-header">
                <h3 class="card-title">Notification List</h3>
                <div class="card-tools">
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0">
                <div class="mailbox-controls">
                    <a href="{{url('notification/mark_all_as_read')}}" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-check"></i> Mark all as Read</a>
                    <a href="{{url('notifications?show=all')}}" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-eye"></i> Show All Notifications</a>
                </div>
                  <!-- /.float-right -->
                </div>
                <div class="table-responsive mailbox-messages">
                  <table class="table table-hover table-striped">
                    <tbody>

                        @if(Request::get('show') == 'all')

                            @foreach(Auth::user()->notifications as $notification)
                                @php
                                $title = $notification->data['title'];
                                $message = $notification->data['message'];
                                $redirect_url = isset($notification->data['redirect_url']) ? $notification->data['redirect_url'] : '#';
                                $notifClass = empty($notification->read_at) ? 'font-weight-bold' : '';
                                @endphp
                                <tr>
                                    <td class="mailbox-name"><a class="{{$notifClass}}" href="{{$redirect_url}}">{{ $title }}</a></td>
                                    <td class="mailbox-subject">{{ $message }}</td>
                                    <td class="mailbox-date">{{ \Carbon\Carbon::createFromTimeStamp(strtotime($notification->created_at))->diffForHumans() }}</td>
                                </tr>
                            @endforeach

                        @else
                            @foreach(Auth::user()->unreadNotifications as $notification)
                                @php
                                $title = $notification->data['title'];
                                $message = $notification->data['message'];
                                $redirect_url = isset($notification->data['redirect_url']) ? $notification->data['redirect_url'] : '#';
                                $notifClass = empty($notification->read_at) ? 'font-weight-bold' : '';
                                @endphp
                                <tr>
                                    <td class="mailbox-name"><a class="{{$notifClass}}" href="{{$redirect_url}}">{{ $title }}</a></td>
                                    <td class="mailbox-subject">{{ $message }}</td>
                                    <td class="mailbox-date">{{ \Carbon\Carbon::createFromTimeStamp(strtotime($notification->created_at))->diffForHumans() }}</td>
                                </tr>
                            @endforeach

                            @if(count(Auth::user()->unreadNotifications) <= 0)
                                <tr><td class="text-center" colspan="3">No unread notifications found.<td></tr>
                            @endif
                        @endif
                        
                    </tbody>
                  </table>
                  <!-- /.table -->
                </div>
                <!-- /.mail-box-messages -->
              </div>
              <!-- /.card-body -->
              <div class="card-footer p-0">
              </div>
            </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
</div>
@endsection
