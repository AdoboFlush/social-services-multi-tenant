<div class="container">
    <div class="">
        @if(count($event["past"]) == 0 && count($event["ongoing"]) == 0 && count($event["upcoming"]) == 0)
            No item to display.
        @endif
        @if(count($event["ongoing"]) > 0)
            <h5>Ongoing</h5>
            @foreach ($event["ongoing"] as $item)
            <blockquote class="row quote-danger" >
                <div class="col-12 mt text-xs">Event</div>
                <div class="col-12"><strong>{{$item["name"] ? $item["name"] : 'N/A'}} </strong></div>
                <div class="col-12 mt text-xs">Description</div>
                <div class="col-12"><strong>{{$item["description"] ? $item["description"] : 'N/A'}} </strong></div>            
                <div class="col-12 mt text-xs">Venue</div>
                <div class="col-12"><strong>{{$item["venue"] ? $item["venue"] : 'N/A'}} </strong></div>           
                <div class="col-12 mt text-xs">Start</div>
                <div class="col-12"><strong>{{$item["start_at"] ? $item["start_at"] : 'N/A'}} </strong></div>
                <div class="col-12 mt text-xs">End</div>
                <div class="col-12"><strong>{{$item["end_at"] ? $item["end_at"] : 'N/A'}} </strong></div>           
            </blockquote>
            <hr></hr>
            @endforeach 
        @endif

        @if(count($event["upcoming"]) > 0)
            <h5>Upcoming</h5>
            @foreach ($event["upcoming"] as $item)
            <blockquote class="row quote-warning">
                <div class="col-12 mt text-xs">Event</div>
                <div class="col-12"><strong>{{$item["name"] ? $item["name"] : 'N/A'}} </strong></div>
                <div class="col-12 mt text-xs">Description</div>
                <div class="col-12"><strong>{{$item["description"] ? $item["description"] : 'N/A'}} </strong></div>            
                <div class="col-12 mt text-xs">Venue</div>
                <div class="col-12"><strong>{{$item["venue"] ? $item["venue"] : 'N/A'}} </strong></div>           
                <div class="col-12 mt text-xs">Start</div>
                <div class="col-12"><strong>{{$item["start_at"] ? $item["start_at"] : 'N/A'}} </strong></div>
                <div class="col-12 mt text-xs">End</div>
                <div class="col-12"><strong>{{$item["end_at"] ? $item["end_at"] : 'N/A'}} </strong></div>                    
            </blockquote>
            <hr></hr>
            @endforeach 
        @endif

        @if(count($event["past"]) > 0)
            <h5>Past</h5>
            @foreach ($event["past"] as $item)
            <blockquote class="row quote-secondary">
                <div class="col-12 mt text-xs">Event</div>
                <div class="col-12"><strong>{{$item["name"] ? $item["name"] : 'N/A'}} </strong></div>
                <div class="col-12 mt text-xs">Description</div>
                <div class="col-12"><strong>{{$item["description"] ? $item["description"] : 'N/A'}} </strong></div>            
                <div class="col-12 mt text-xs">Venue</div>
                <div class="col-12"><strong>{{$item["venue"] ? $item["venue"] : 'N/A'}} </strong></div>           
                <div class="col-12 mt text-xs">Start</div>
                <div class="col-12"><strong>{{$item["start_at"] ? $item["start_at"] : 'N/A'}} </strong></div>
                <div class="col-12 mt text-xs">End</div>
                <div class="col-12"><strong>{{$item["end_at"] ? $item["end_at"] : 'N/A'}} </strong></div>             
            </blockquote>
            <hr></hr>
            @endforeach 
        @endif
        
    </div>
</div>
