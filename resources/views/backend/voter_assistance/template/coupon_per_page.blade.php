<style>
    .paper-area {
        background-color:#fff; 
        overflow-y:auto;
        width: 2480px;
        height: 3508px;
        padding-left:200px;
        padding-top:100px;
    }
    
    .id_background {
        width: 750px;
        height: auto;
    }
    
    .card-area {
        position: relative;
        display: inline-block;
        margin-right: 1cm;
        margin-bottom: 1cm;
        border: 1px solid #000;
    }
    
    .card-text {
        z-index:1000; 
        font-weight:1000; 
        position: absolute;
    }
    
    .card-image {
        z-index:1000;
        position: absolute;
    }
    
</style>

<div class="paper-area" data-file-name="{{$file_name}}" data-last-page={{$last_page}}>
    @foreach($voters as $voter)     
        <div class="card-area" data-file-name="{{$voter->full_name}}.png" id="id-canvass-{{$voter->id}}" data-id="{{$voter->id}}">
            <div id="id-markup-{{$voter->id}}">
                <img class="id_background" src="{{ asset('images/id_canvass.png') }}" alt="img">
                <div id="qr" class="card-image" style="top: 25%; left: 50%; transform: translate(-50%, -50%); width: 200px; height: 200px; font-size: 16px; font-weight: 1000;">
                    {!! QrCode::size(200)->generate(Crypt::encryptString("{$assistance_event->id}|{$voter->id}")) !!}
                </div>
                <div id="full_name" class="card-text" style="top: 300px; left: 200px; height: 24px; font-size: 16px; font-weight: 1000;">Name: {{ $voter->full_name }}</div>
                <div id="birth_date" class="card-text" style="top: 340px; left: 200px; height: 24px; font-size: 16px; font-weight: 1000;">Birth Date: {{ $voter->birth_date }}</div>
                <div id="precinct" class="card-text" style="top: 380px; left: 200px; height: 24px; font-size: 16px; font-weight: 1000;">Precinct: {{ $voter->precinct }}</div>
                <div id="brgy" class="card-text" style="top: 420px; left: 200px; height: 24px; font-size: 16px; font-weight: 1000;">Barangay: {{ $voter->brgy }}</div>
            </div>
        </div>
    @endforeach
</div>
