@extends('layouts.app')

@section('content')
    <div class="sb-page-header pb-2 sb-page-header-dark bg-gradient-primary-to-secondary">
        <div class="container-fluid">
            <div class="sb-page-header-content py-3">
                <h1 class="sb-page-header-title">
                    <div class="sb-page-header-icon"><i data-feather="mail"></i></div>
                    <span>{{ _lang('My Tickets') }}</span>
                </h1>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                @foreach($tickets as $ticket)
                <div class="card p-2 my-2 d-block d-md-none">
                    <div class="row mb-2">
                        <div class="col-12">
                            <strong>{{ _lang('Ticket ID') }}:</strong>
                            <a @if(!$ticket->conversations->first()->is_seen) class="font-weight-bold" @endif href="{{ url("user/ticket/show/".$ticket->id) }}">{{ $ticket->id }}</a>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            <strong>{{ _lang('Date Ticket Created') }}:</strong>
                            {{ $ticket->created_at }}
                        </div>
                        <div class="col-6">
                            <strong>{{ _lang('Date Updated') }}:</strong>
                            {{ $ticket->updated_at }}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-12">
                            <strong>{{ _lang('Subject') }}:</strong>
                            {{ toWords($ticket->subject) }}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-12">
                            <strong>{{ _lang('Department') }}:</strong>
                            {{ toWords($ticket->department) }}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-12">
                            <strong>{{ _lang('Status') }}:</strong>
                            {{ ticketTranslate($ticket->status) }}
                        </div>
                    </div>
                </div>
                @endforeach
                <div class="card d-none d-md-block">
                    <div class="card-body">
                        <h4 class="card-title panel-title">{{ _lang('My Tickets') }}</h4>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>{{ _lang("Ticket ID") }}</th>
                                <th>{{ _lang("Subject") }}</th>
                                <th>{{ _lang("Department") }}</th>
                                <th>{{ _lang("Date Ticket Created") }}</th>
                                <th>{{ _lang("Date Updated") }}</th>
                                <th>{{ _lang("Status") }}</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($tickets as $ticket)
                                <tr id="{{ $ticket->id }}" @if(!$ticket->conversations->first()->is_seen) class="font-weight-bold" @endif>
                                    <td><a href="{{ url("user/ticket/show/".$ticket->id) }}">{{ $ticket->id }}</a></td>
                                    <td>{{ toWords($ticket->subject) }}</td>
                                    <td>{{ toWords($ticket->department) }}</td>
                                    <td>{{ $ticket->created_at }}</td>
                                    <td>{{ $ticket->updated_at }}</td>
                                    <td>{{ ticketTranslate($ticket->status) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="paginator float-right mt-2">
                        @include('pagination.default', ['paginator' => $tickets])
                </div>
            </div>
        </div>
    </div>
@endsection
