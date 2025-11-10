@extends('mail.ticketLayout')

@if(!empty($content_top) && $content_top)
    @section('content-top')
        <div>{{ _lang("Dear Mr/Ms {name},", ['name' => $data->first_name . " " . $data->last_name . " " . $data->account_number]) }}</div><br/>
    @endsection
@endif

@section('content')
    <div style="font-size: 14px; line-height: 1.2; color: #333; font-family: 'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; mso-line-height-alt: NaNpx;">
        {!! $message !!}
    </div>
@endsection

@if(!empty($content_bottom) && $content_bottom)
    @section('content-bottom')
        <div>{{ _lang("Thank you for your continued patronage. We are committed to providing our customers with high-quality service.") }}</div>
        <br />
        <div style="font-weight: 700; font-style: italic; color: #ff0000;">{{ _lang("Warning! This message was sent from an unmonitored address. Please do not respond to this e-mail.") }}</div>
    @endsection
@endif