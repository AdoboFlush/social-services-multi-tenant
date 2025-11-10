<div class="container">
    @if(count($social_service_assistance) == 0)
    No item to display.
    @endif
    @foreach ($social_service_assistance as $assistance)
    <div class="card row">
        <div class="card-body">
            <div class="ribbon-wrapper ribbon-lg">
                @php
                    $bg = $assistance->status == "Released" ? 'bg-warning': '';
                    $bg = $assistance->status == "Pending" ? 'bg-primary': $bg;
                    $bg = $assistance->status == "Approved" ? 'bg-success': $bg;
                @endphp
                <div class="ribbon text-lg {{$bg}} ">
                    {{$assistance->status}}
                </div>
            </div>
            <div class="col-12"><strong>{{$assistance->request_type}} </strong> </div>
            <hr />
            <div class="col-12 mt text-sm">Purpose</div>
            <div class="col-12"><strong>{{$assistance->purpose_text ? $assistance->purpose_text : 'N/A'}} </strong></div>
            <div class="col-12 mt text-sm">Amount</div>
            <div class="col-12"><strong>{{$assistance->amount ? $assistance->amount : 'N/A'}} </strong></div>
            <div class="col-12 mt text-sm">Remarks</div>
            <div class="col-12"><strong>{{$assistance->remarks ? $assistance->remarks : 'N/A'}} </strong></div>
            <div class="col-12 mt text-sm">File Date</div>
            <div class="col-12"><strong>{{$assistance->file_date ? $assistance->file_date : 'N/A'}} </strong></div>
            <div class="col-12 mt text-sm">Approved Date</div>
            <div class="col-12"><strong>{{$assistance->processed_date ? $assistance->processed_date : 'N/A'}} </strong></div>
            <div class="col-12 mt text-sm">Release Date</div>
            <div class="col-12"><strong>{{$assistance->release_date ? $assistance->release_date : 'N/A'}} </strong></div>
        </div>
    </div>
    @endforeach
</div>