@php $limit = 2; @endphp
@for($i=0; $i<count($ticket->conversations) && $i<$limit; $i++) @if(!($ticket->conversations[$i]->message == "--INTERNAL_MESSAGE_SOLVED--" && Auth::user()->user_type == "user"))
        <div class="row conversation px-4 py-2">
            <div class="col-12 d-block d-md-none p-0 font-size-normal">
                @if(Auth::user()->user_type == "admin")
                <p class="px-2">{{ ucwords($ticket->conversations[$i]->sender->first_name) }} {{ ucwords($ticket->conversations[$i]->sender->last_name) }} <small>({{ toWords($ticket->conversations[$i]->sender->user_type == "user" ? _lang("You") : $ticket->conversations[$i]->department) }})</small></p>
                @else
                @if($ticket->conversations[$i]->sender->user_type == "user")
                <p class="px-2">{{ ucwords($ticket->conversations[$i]->sender->first_name) }} {{ ucwords($ticket->conversations[$i]->sender->last_name) }} <small>({{ _lang("You") }})</small></p>
                @else
                <p class="px-2">{{ toWords($ticket->conversations[$i]->department) }}</p>
                @endif
                @endif
                <div class="mx-2 mx-md-0 p-2 card message-container">
                    @if($ticket->conversations[$i]->message == "--INTERNAL_MESSAGE_SOLVED--")
                    {{ _lang("Updated ticket status to Solved") }}
                    @else
                    <span class="font-size-normal">{!! $ticket->conversations[$i]->message !!}</span>
                    @endif
                    <div class="py-1">
                        @if($ticket->conversations[$i]->attachment)
                        <p class="font-weight-bold mt-2">{{ _lang("Attachment/s:") }}</p>
                        @if(is_array(json_decode($ticket->conversations[$i]->attachment)))
                        @foreach(json_decode($ticket->conversations[$i]->attachment) as $attachment)
                        @if(Storage::disk('s3')->exists("uploads/tickets/attachments/".$attachment))
                        <a href='{{ Storage::disk('s3')->url("uploads/tickets/attachments/".$attachment) }}' target='_blank'>{{ $attachment }}</a><br />
                        @else
                        <a href='{{ asset('uploads/tickets/'.$attachment) }}' target='_blank'>{{ $attachment }}</a><br />
                        @endif
                        @endforeach
                        @else
                        <a href='{{ asset('uploads/tickets/'.$ticket->conversations[$i]->attachment) }}' target='_blank'>{{ $ticket->conversations[$i]->attachment }}</a><br />
                        @endif
                        @endif
                    </div>
                </div>
                <p class="px-2 text-muted text-right font-size-normal">
                    <small>{{ _lang("Posted on:") }} {{ $ticket->conversations[$i]->created_at }} </small>
                </p>
            </div>
            @if(Auth::user()->user_type == "admin")
            <div class="col-3 d-none d-md-block border p-3 bg-gray">
                <h5 class="m-0 font-weight-normal">{{ ucwords($ticket->conversations[$i]->sender->first_name) }} {{ ucwords($ticket->conversations[$i]->sender->last_name) }}</h5>
                <small>{{ toWords($ticket->conversations[$i]->sender->user_type == "user" ? _lang("User") : $ticket->conversations[$i]->department) }}</small>
            </div>
            @else
            <div class="col-3 d-none d-md-block border p-3 bg-gray">
                @if($ticket->conversations[$i]->sender->user_type == "user")
                <h5 class="m-0 font-weight-normal">{{ ucwords($ticket->conversations[$i]->sender->first_name) }} {{ ucwords($ticket->conversations[$i]->sender->last_name) }}</h5>
                <small>{{ toWords($ticket->conversations[$i]->sender->user_type == "user" ? _lang("User") : $ticket->conversations[$i]->department) }}</small>
                @else
                <h5 class="m-0 font-weight-normal">{{ toWords($ticket->conversations[$i]->department) }}</h5>
                @endif
            </div>
            @endif
            <div class="col-9 d-none d-md-block border p-0 min-height-130">
                <div class="border-bottom px-3 py-1 message-container">
                    <p class="text-muted"><small>{{ _lang("Posted on:") }} {{ $ticket->conversations[$i]->created_at }} @if($ticket->conversations[$i]->message == "--INTERNAL_MESSAGE_SOLVED--") {{ _lang("Updated ticket status to Solved") }} @endif</small></p>
                </div>
                <div class="px-3 py-1">
                    @if($ticket->conversations[$i]->message != "--INTERNAL_MESSAGE_SOLVED--")
                    <p>{!! $ticket->conversations[$i]->message !!}</p>
                    @if($ticket->conversations[$i]->attachment)
                    <p class="font-weight-bold mt-2">{{ _lang("Attachment/s:") }}</p>
                    @if(is_array(json_decode($ticket->conversations[$i]->attachment)))
                    @foreach(json_decode($ticket->conversations[$i]->attachment) as $attachment)
                    @if(Storage::disk('s3')->exists("uploads/tickets/attachments/".$attachment))
                    <a href='{{ Storage::disk('s3')->url("uploads/tickets/attachments/".$attachment) }}' target='_blank'>{{ $attachment }}</a><br />
                    @else
                    <a href='{{ asset('uploads/tickets/'.$attachment) }}' target='_blank'>{{ $attachment }}</a><br />
                    @endif
                    @endforeach
                    @else
                    <a href='{{ asset('uploads/tickets/'.$ticket->conversations[$i]->attachment) }}' target='_blank'>{{ $ticket->conversations[$i]->attachment }}</a><br />
                    @endif
                    @endif
                    @endif
                </div>
            </div>
        </div>
        @else
        @php $limit++; @endphp
        @endif
        @endfor
        @if(count($ticket->conversations) > $limit)
        <div class="collapse px-1" id="showMore">
            <div class="row card px-2 no-box-shadow">
                @for($i=$limit; $i<count($ticket->conversations); $i++)
                    @if(!($ticket->conversations[$i]->message == "--INTERNAL_MESSAGE_SOLVED--" && Auth::user()->user_type == "user"))
                    <div class="row conversation px-4 py-2">
                        <div class="col-12 d-block d-md-none p-0 font-size-normal message-container">
                            @if(Auth::user()->user_type == "admin")
                            <p class="px-2">{{ ucwords($ticket->conversations[$i]->sender->first_name) }} {{ ucwords($ticket->conversations[$i]->sender->last_name) }} <small>({{ toWords($ticket->conversations[$i]->sender->user_type == "user" ? _lang("You") : $ticket->conversations[$i]->department) }})</small></p>
                            @else
                            @if($ticket->conversations[$i]->sender->user_type == "user")
                            here<p class="px-2">{{ ucwords($ticket->conversations[$i]->sender->first_name) }} {{ ucwords($ticket->conversations[$i]->sender->last_name) }} <small>({{ _lang("You") }})</small></p>
                            @else
                            <p class="px-2">{{ toWords($ticket->conversations[$i]->department) }}</p>
                            @endif
                            @endif
                            <div class="mx-2 mx-md-0 p-2 card message-container">
                                @if($ticket->conversations[$i]->message == "--INTERNAL_MESSAGE_SOLVED--")
                                {{ _lang("Updated ticket status to Solved") }}
                                @else
                                <span class="font-size-normal">{!! $ticket->conversations[$i]->message !!}</span>
                                @endif
                                <div class="py-1">
                                    @if($ticket->conversations[$i]->attachment)
                                    <p class="font-weight-bold mt-2">{{ _lang("Attachment/s:") }}</p>
                                    @if(is_array(json_decode($ticket->conversations[$i]->attachment)))
                                    @foreach(json_decode($ticket->conversations[$i]->attachment) as $attachment)
                                    @if(Storage::disk('s3')->exists("uploads/tickets/attachments/".$attachment))
                                    <a href='{{ Storage::disk('s3')->url("uploads/tickets/attachments/".$attachment) }}' target='_blank'>{{ $attachment }}</a><br />
                                    @else
                                    <a href='{{ asset('uploads/tickets/'.$attachment) }}' target='_blank'>{{ $attachment }}</a><br />
                                    @endif
                                    @endforeach
                                    @else
                                    <a href='{{ asset('uploads/tickets/'.$ticket->conversations[$i]->attachment) }}' target='_blank'>{{ $ticket->conversations[$i]->attachment }}</a><br />
                                    @endif
                                    @endif
                                </div>
                            </div>
                            <p class="px-2 text-muted text-right font-size-normal"><small>{{ _lang("Posted on:") }} {{ $ticket->conversations[$i]->created_at }}</small></p>
                        </div>
                        @if(Auth::user()->user_type == "admin")
                        <div class="col-3 d-none d-md-block border p-3 bg-gray">
                            <h5 class="m-0 font-weight-normal">{{ ucwords($ticket->conversations[$i]->sender->first_name) }} {{ ucwords($ticket->conversations[$i]->sender->last_name) }}</h5>
                            <small>{{ toWords($ticket->conversations[$i]->sender->user_type == "user" ? _lang("User") : $ticket->conversations[$i]->department) }}</small>
                        </div>
                        @else
                        <div class="col-3 d-none d-md-block border p-3 bg-gray">
                            @if($ticket->conversations[$i]->sender->user_type == "user")
                            <h5 class="m-0 font-weight-normal">{{ ucwords($ticket->conversations[$i]->sender->first_name) }} {{ ucwords($ticket->conversations[$i]->sender->last_name) }}</h5>
                            <small>{{ toWords($ticket->conversations[$i]->sender->user_type == "user" ? _lang("User") : $ticket->conversations[$i]->department) }}</small>
                            @else
                            <h5 class="m-0 font-weight-normal">{{ toWords($ticket->conversations[$i]->department) }}</h5>
                            @endif
                        </div>
                        @endif
                        <div class="col-9 d-none d-md-block border p-0 min-height-130">
                            <div class="border-bottom px-3 py-1">
                                <p class="text-muted"><small>{{ _lang("Posted on:") }} {{ $ticket->conversations[$i]->created_at }} @if($ticket->conversations[$i]->message == "--INTERNAL_MESSAGE_SOLVED--") {{ _lang("Updated ticket status to Solved") }} @endif</small></p>
                            </div>
                            <div class="px-3 py-1 message-container">
                                @if($ticket->conversations[$i]->message != "--INTERNAL_MESSAGE_SOLVED--")
                                <p>{!! $ticket->conversations[$i]->message !!}</p>
                                @if($ticket->conversations[$i]->attachment)
                                <p class="font-weight-bold mt-2">{{ _lang("Attachment/s:") }}</p>
                                @if(is_array(json_decode($ticket->conversations[$i]->attachment)))
                                @foreach(json_decode($ticket->conversations[$i]->attachment) as $attachment)
                                @if(Storage::disk('s3')->exists("uploads/tickets/attachments/".$attachment))
                                <a href='{{ Storage::disk('s3')->url("uploads/tickets/attachments/".$attachment) }}' target='_blank'>{{ $attachment }}</a><br />
                                @else
                                <a href='{{ asset('uploads/tickets/'.$attachment) }}' target='_blank'>{{ $attachment }}</a><br />
                                @endif
                                @endforeach
                                @else
                                <a href='{{ asset('uploads/tickets/'.$ticket->conversations[$i]->attachment) }}' target='_blank'>{{ $ticket->conversations[$i]->attachment }}</a><br />
                                @endif
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    @endfor
            </div>
        </div>
        <div class="row px-4">
            <div class="col-12 text-center p-2 mt-3 bg-gray" data-toggle="collapse" data-target="#showMore" aria-expanded="false" type="button" id="showMoreButton">
                <span class="down">
                    <p class="align-middle font-italic font-weight-bold ">{{ _lang("Click here to view previous conversations") }}</p>
                    <i class="fa fa-angle-double-down"></i>
                </span>
                <span class="up d-none">
                    <p class="align-middle font-italic font-weight-bold ">{{ _lang("Click here to hide previous conversations") }}</p>
                    <i class="fa fa-angle-double-up"></i>
                </span>
            </div>
        </div>
        @endif
