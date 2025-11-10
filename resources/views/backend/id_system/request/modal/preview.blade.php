<style>
.nowrap-text {
    white-space: nowrap;
}
</style>

<div class="container pb-5 mb-3" style="background-color:#d9d9d9; max-height: 700px; overflow-y:auto;">
    <div class="row mt-2 mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="float-left">
                        <h4> ID #: {{ $data->id_number }}</h4>
                    </div>
                    <div class="float-right">
                        <button id="print-front" class="btn btn-primary" data-file-name="{{ $data->member->full_name }}-all.png" type="button"><i class="fa fa-print mr-2"></i>Print Front</button>
                        <button id="print-back" class="btn btn-primary" data-file-name="{{ $data->member->full_name }}-all.png" type="button"><i class="fa fa-print mr-2"></i>Print Back</button>
                        <button id="download-front" class="btn btn-success" data-file-name="{{ date('Y-m-d')."_".$data->member->full_name }}-front.png" type="button"><i class="fa fa-download mr-2"></i>Download Front</button>
                        <button id="download-back" class="btn btn-warning" data-file-name="{{ date('Y-m-d')."_".$data->member->full_name }}-back.png" type="button"><i class="fa fa-download mr-2"></i>Download Back</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div id="id-output-all"></div>
            <div id="id-canvass-all">
                <div id="id-output-front"></div>
                <div id="id-canvass-front" class="mb-5" style="position: relative;">
                    <img id="id_front_background" src="{{ !empty($front_bg) ? asset('uploads/templates/'.$front_bg) : asset('images/id_canvass.png') }}" alt="img" style="width:750px;">
                    @foreach($front as $key => $fields)
                        @php
                            $addCss = '';
                            $width = 0;
                            if($key == 'bg') continue;
                            if(is_array($fields)){
                                foreach($fields as $name => $prop){
                                    if(in_array($key, ['profile_pic','signature','qr'])){
                                        $addCss .= "$name:$prop;";
                                        if($name == "width") {
                                            $width = intval(str_replace("px", "",$prop));
                                        }
                                    }else{
                                        if(!in_array($name, ['inset','width','height'])){
                                            $addCss .= "$name:$prop;";
                                        }
                                    }
                                }
                            }
                        @endphp

                        @php
                            // Add nowrap for text fields
                            $textFields = ['full_name','id_number','address','brgy','birth_date','contact_number','account_number','gender','affiliation','alliance','civil_status','religion','precinct','contact_person_full_name','contact_person_number','contact_person_address'];
                            $addCssNowrap = $addCss;
                            if (in_array($key, $textFields)) {
                                $addCssNowrap .= 'white-space:nowrap;';
                            }
                        @endphp

                        @php
                            $baseStyle = 'z-index:1000; position: absolute;';
                            $styleAttr = $baseStyle . ($addCss ? ' ' . $addCss : '');
                        @endphp
                        @if($key == 'profile_pic')
                            @if(!empty($data->profile_pic))
                                <div id="profile_pic" style="{{ $styleAttr }}">
                                    <img src="{{ asset('uploads/profile/'.$data->profile_pic) }}" alt="img" style="width:100%;">
                                </div>
                            @endif
                        @elseif($key == 'signature')
                            @if(!empty($data->signature))
                                <div id="signature" style="{{ $styleAttr }}">
                                    <img src="{{ asset('uploads/profile/'.$data->signature) }}" alt="img" style="width:100%;">
                                </div>
                            @endif
                        @elseif($key == 'qr')
                            <div id="qr" style="{{ $styleAttr }}">
                                {!! QrCode::size($width)->generate($data->id_qr_value) !!}
                            </div>
                        @elseif(in_array($key, ['full_name','id_number','address','brgy','birth_date','contact_number','account_number','gender','affiliation','alliance','civil_status','religion','precinct','contact_person_full_name','contact_person_number','contact_person_address']))
                            <div id="{{ $key }}" class="nowrap-text" style="{{ $styleAttr }} font-weight:1000;">
                                @switch($key)
                                    @case('full_name')
                                        {{ $data->name_on_id }}
                                        @break
                                    @case('id_number')
                                        {{ $data->id_number }}
                                        @break
                                    @case('address')
                                        {{ $data->member->address }}
                                        @break
                                    @case('brgy')
                                        {{ $data->member->brgy }}
                                        @break
                                    @case('birth_date')
                                        {{ $data->member->birth_date }}
                                        @break
                                    @case('contact_number')
                                        {{ $data->member->contact_number }}
                                        @break
                                    @case('account_number')
                                        {{ $data->member->account_number }}
                                        @break
                                    @case('gender')
                                        {{ $data->member->gender }}
                                        @break
                                    @case('affiliation')
                                        {{ $data->member->affiliation }}
                                        @break
                                    @case('alliance')
                                        {{ $data->member->alliance }}
                                        @break
                                    @case('civil_status')
                                        {{ $data->member->civil_status }}
                                        @break
                                    @case('religion')
                                        {{ $data->member->religion }}
                                        @break
                                    @case('precinct')
                                        {{ $data->member->precinct }}
                                        @break
                                    @case('contact_person_full_name')
                                        {{ $data->member->contact_person_full_name }}
                                        @break
                                    @case('contact_person_number')
                                        {{ $data->member->contact_person_number }}
                                        @break
                                    @case('contact_person_address')
                                        {{ $data->member->contact_person_address }}
                                        @break
                                @endswitch
                            </div>
                        @else
                            <div id="{{ $key }}" class="nowrap-text" style="{{ $styleAttr }} font-weight:1000;">[{{ $key }}]</div>
                        @endif
                        
                    @endforeach
                </div>

                <div id="id-output-back"></div>
                <div id="id-canvass-back" class="mb-5" style="position: relative;">
                    <img id="id_back_background" src="{{ !empty($back_bg) ? asset('uploads/templates/'.$back_bg) : asset('images/id_canvass.png') }}" alt="img" style="width:750px;">
                    
                    @foreach($back as $key => $fields)
                        @php
                            $addCss = '';
                            $width = 0;
                            if($key == 'bg') continue;
                            if(is_array($fields)){
                                foreach($fields as $name => $prop){
                                    if(in_array($key, ['profile_pic','signature','qr'])){
                                        $addCss .= "$name:$prop;";
                                        if($name == "width") {
                                            $width = intval(str_replace("px", "",$prop));
                                        }
                                    }else{
                                        if(!in_array($name, ['inset','width','height'])){
                                            $addCss .= "$name:$prop;";
                                        }
                                    }
                                }
                            }
                        @endphp

                        @php
                            $baseStyle = 'z-index:1000; position: absolute;';
                            $styleAttr = $baseStyle . ($addCss ? ' ' . $addCss : '');
                        @endphp
                        @if($key == 'profile_pic')
                            @if(!empty($data->profile_pic))
                                <div id="profile_pic" style="{{ $styleAttr }}">
                                    <img src="{{ asset('uploads/profile/'.$data->profile_pic) }}" alt="img" style="width:100%;">
                                </div>
                            @endif
                        @elseif($key == 'signature')
                            @if(!empty($data->signature))
                                <div id="signature" style="{{ $styleAttr }}">
                                    <img src="{{ asset('uploads/profile/'.$data->signature) }}" alt="img" style="width:100%;">
                                </div>
                            @endif
                        @elseif($key == 'qr')
                            <div id="qr" style="{{ $styleAttr }}">
                                {!! QrCode::size($width)->generate($data->id_qr_value) !!}
                            </div>
                        @elseif(in_array($key, ['full_name','id_number','address','brgy','birth_date','contact_number','account_number','gender','affiliation','alliance','civil_status','religion','precinct','contact_person_full_name','contact_person_number','contact_person_address']))
                            <div id="{{ $key }}" class="nowrap-text" style="{{ $styleAttr }} font-weight:1000;">
                                @switch($key)
                                    @case('full_name')
                                        {{ $data->name_on_id }}
                                        @break
                                    @case('id_number')
                                        {{ $data->id_number }}
                                        @break
                                    @case('address')
                                        {{ $data->member->address }}
                                        @break
                                    @case('brgy')
                                        {{ $data->member->brgy }}
                                        @break
                                    @case('birth_date')
                                        {{ $data->member->birth_date }}
                                        @break
                                    @case('contact_number')
                                        {{ $data->member->contact_number }}
                                        @break
                                    @case('account_number')
                                        {{ $data->member->account_number }}
                                        @break
                                    @case('gender')
                                        {{ $data->member->gender }}
                                        @break
                                    @case('affiliation')
                                        {{ $data->member->affiliation }}
                                        @break
                                    @case('alliance')
                                        {{ $data->member->alliance }}
                                        @break
                                    @case('civil_status')
                                        {{ $data->member->civil_status }}
                                        @break
                                    @case('religion')
                                        {{ $data->member->religion }}
                                        @break
                                    @case('precinct')
                                        {{ $data->member->precinct }}
                                        @break
                                    @case('contact_person_full_name')
                                        {{ $data->member->contact_person_full_name }}
                                        @break
                                    @case('contact_person_number')
                                        {{ $data->member->contact_person_number }}
                                        @break
                                    @case('contact_person_address')
                                        {{ $data->member->contact_person_address }}
                                        @break
                                @endswitch
                            </div>
                        @else
                            <div id="{{ $key }}" class="nowrap-text" style="{{ $styleAttr }} font-weight:1000;">[{{ $key }}]</div>
                        @endif
                        
                    @endforeach

                </div>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>