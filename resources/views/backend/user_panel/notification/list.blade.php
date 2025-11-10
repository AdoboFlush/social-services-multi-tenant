@extends('layouts.app')

@section('content')
    <div class="sb-page-header pb-2 sb-page-header-dark bg-gradient-primary-to-secondary">
        <div class="container-fluid">
            <div class="sb-page-header-content py-3">
                <h1 class="sb-page-header-title">
                    <div class="sb-page-header-icon"><i data-feather="bell"></i></div>
                    <span>{{ _lang('Announcements') }}</span>
                </h1>
            </div>
        </div>
    </div>

    <div class="container-fluid d-md-block">
        <div class="row">
            <div class="col-12">
                <div class="card no-export">
                    <div class="card-body">
                        @foreach($notifications as $notif)
                        <div class="announcement-item">
                            <h4>{{ $notif->notification->title }}</h4>
                            <p><small>{{ \Carbon\Carbon::parse($notif->notification->published_at)->format('M d, Y h:i A') }}</small></p>

                            <div class="my-4">
                               {!! $notif->notification->content !!}
                            </div>
                            <hr>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>




@endsection

<style type="text/css">
    table td,
    table th {
        font-size: 12px;
    }
</style>

