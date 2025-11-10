@extends('backend.id_system.layout')

@section('card-header')
<h3 class="card-title">Create a Template</h3>
@endsection

@section('tab-content')

<form method="post" class="validate" style="width:100%;" autocomplete="off" action="{{ url('id_system/templates/store') }}" enctype="multipart/form-data">

    {{ csrf_field() }}
    
    <input type="hidden" name="bg_front" id="bg_front" value="" />
    <input type="hidden" name="bg_back" id="bg_back" value="" />

    @if(Session::has('success'))
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success">{{ session('success') }}</div>
            </div>
        </div>
    @endif

    @if(Session::has('warning'))
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning">{{ session('warning') }}</div>
            </div>
        </div>
    @endif
    
    @if(Session::has('error'))
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-danger">{{ session('error') }}</div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Template Name') }}</label>
                <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" required />
                <span class="err-message">{{ _lang('Template Name is required.') }}</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Remarks') }}</label>
                <input type="text" class="form-control" name="remarks" id="remarks" value="{{ old('remarks') }}" />
                <span class="err-message">{{ _lang('Remarks is required.') }}</span>
            </div>
        </div>

        <div class="col-md-6">
            <span><input type="checkbox" name="allowed_user_create" id="allowed_user_create" class="mr-2"  {{  old('allowed_user_create') == 1 ? 'checked' : '' }}/>Allow Member to Create</span>
            <span class="err-message">{{ _lang('This is required.') }}</span>
        </div>

        <div class="col-md-6">
            <span><input type="checkbox" name="allowed_user_update" id="allowed_user_update" class="mr-2"  {{  old('allowed_user_update') == 1 ? 'checked' : '' }}/>Allow Member to Update</span>
            <span class="err-message">{{ _lang('This is required.') }}</span>
        </div>

    </div>

    <hr></hr>

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a class="nav-link active" href="#front" data-toggle="tab">Front</a></li>
        <li class="nav-item"><a class="nav-link" href="#back" data-toggle="tab">Back</a></li>
    </ul>
    
    <div class="tab-content">
        <div class="tab-pane active" id="front">

            <div class="row">  

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label" for="file_front">Upload ID card image (750x475) :</label>
                        <input type="file" name="file_front" placeholder="Choose image" id="file_front" />
                    </div>
                </div>
        
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">{{ _lang('Data Field') }}</label>
                        <div class="input-group">
                            <select class="form-control select2" name="data_field_front" id="data_field_front">
                                <option value="">Please select data field</option>
                                <option value="full_name">Full Name</option>
                                <option value="account_number">Account Number</option>
                                <option value="id_number">ID Number</option>
                                <option value="brgy">Barangay</option>
                                <option value="gender">Gender</option>
                                <option value="address">Address</option>
                                <option value="birth_date">Birth Date</option>
                                <option value="precinct">Precinct</option>
                                <option value="profile_pic">Profile Picture</option>
                                <option value="qr">QR code</option>
                                <option value="signature">Signature</option>
                                <option value="alliance">Alliance</option>
                                <option value="affiliation">Affiliation</option>
                                <option value="civil_status">Civil Status</option>
                                <option value="religion">Religion</option>
                                <option value="contact_number">Contact Number</option>
                                <option value="remarks">Remarks</option>
                                <option value="contact_person_full_name">Contact Person Full Name</option>
                                <option value="contact_person_number">Contact Person Number</option>
                                <option value="contact_person_address">Contact Person Address</option>
                            </select>
                            <span class="err-message">{{ _lang('Data Field is required') }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">---</label>
                        <input class="input-group-text add_data_field" type="button" value="Add" />
                    </div>
                </div>
        
            </div>

            <div class="row">
                
                <div class="col-md-8 pt-5 mb-5" style="background-color:#d9d9d9; height: 619px; display: flex; justify-content: center;">
                    <div class="id-canvass-front mb-5" style="position: relative;">
                        <img id="id_front_background" src="{{ asset('images/id_canvass.png') }}" alt="img" style="width:750px;">
                    </div>
                </div>
                <div class="col-md-4">
        
                    <div class="ml-2 pl-1 pr-1 pt-1" style="height:619px; overflow-y:auto; background-color:#d9d9d9;">
                        <div class="card card-olive">
                            <div class="card-header">
                                <i class="fa fa-cog"></i> Field Properties
                            </div>
                            <div class="card-body">
                                <div id="field_container_front">
                                </div>
                            </div>
                        </div>
                    </div>
        
                </div>
            </div>

        </div>
        <div class="tab-pane" id="back">

            <div class="row">    
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label" for="file_back">Upload ID card image (750x475):</label>
                        <input type="file" name="file_back" placeholder="Choose image" id="file_back" />
                    </div>
                </div>
        
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">{{ _lang('Data Field') }}</label>
                        <div class="input-group">
                            <select class="form-control select2" name="data_field_back" id="data_field_back">
                                <option value="">Please select data field</option>
                                <option value="full_name">Full Name</option>
                                <option value="account_number">Account Number</option>
                                <option value="id_number">ID Number</option>
                                <option value="brgy">Barangay</option>
                                <option value="gender">Gender</option>
                                <option value="address">Address</option>
                                <option value="birth_date">Birth Date</option>
                                <option value="precinct">Precinct</option>
                                <option value="profile_pic">Profile Picture</option>
                                <option value="qr">QR code</option>
                                <option value="signature">Signature</option>
                                <option value="alliance">Alliance</option>
                                <option value="affiliation">Affiliation</option>
                                <option value="civil_status">Civil Status</option>
                                <option value="religion">Religion</option>
                                <option value="contact_number">Contact Number</option>
                                <option value="remarks">Remarks</option>
                                <option value="contact_person_full_name">Contact Person Full Name</option>
                                <option value="contact_person_number">Contact Person Number</option>
                                <option value="contact_person_address">Contact Person Address</option>
                            </select>
                            <span class="err-message">{{ _lang('Data Field is required') }}</span>
                        </div>
        
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">---</label>
                        <input class="input-group-text add_data_field" type="button" value="Add" />
                    </div>
                </div>
        
            </div>
            
            <div class="row">
                <div class="col-md-8 pt-5 mb-5" style="background-color:#d9d9d9; height: 619px; display: flex; justify-content: center;">
                    <div class="id-canvass-back mb-5" style="position: relative;">
                        <img id="id_back_background" src="{{ asset('images/id_canvass.png') }}" alt="img" style="width:750px;">
                    </div>
                </div>
                <div class="col-md-4">

                    <div class="ml-2 pl-1 pr-1 pt-1" style="height:619px; overflow-y:auto; background-color:#d9d9d9;">
                        <div class="card card-olive">
                            <div class="card-header">
                                <i class="fa fa-cog"></i> Field Properties
                            </div>
                            <div class="card-body">
                                <div id="field_container_back">
                                </div>
                            </div>
                        </div>
                    </div>
        
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <button type="button" id="save" class="btn btn-primary">{{ _lang('Save Template') }}</button>					
            </div>
        </div>
    </div>

</form>

@endsection

@section('js-script')

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>

<script>

$(document).ready(function(){
    
    $('#file_front').change(function(){
        let reader = new FileReader();
        reader.onload = (e) => { 
            let src =  e.target.result;
            $('#id_front_background_pic').attr('value', src);  
            $('#id_front_background').attr('src', src);   
        }
        reader.readAsDataURL(this.files[0]); 
    });

    $('#file_back').change(function(){
        let reader = new FileReader();
        reader.onload = (e) => { 
            let src =  e.target.result;
            $('#id_back_background_pic').attr('value', src);  
            $('#id_back_background').attr('src', src);   
        }
        reader.readAsDataURL(this.files[0]); 
    });

    $('.nav-link').on('click', function(e){
        $('.select2').select2({width: '100%'});
    });

    $(document).on('click', '.field-draggable', function(e){
        
        let orientation = $(this).parents('.tab-pane').attr('id');
        let type = $(this).data('type');
        let field = $(this).attr('id');
        $('#field_container_' + orientation).html('');
        let card_template = '<div class="card template_element" data-field="'+field+'">';
        card_template += '<div class="card-body">';
        card_template += '<h4> <span style="font-size:18px; font-weight:1000;">['+field+']</span> <span class="float-right"><button type="button" class="btn btn-danger remove_element"><i class="fa fa-trash"></i></button></span></h4>';
        
        if(type == 'image'){

            let current_height = $(this).css('height').replace('px','');
            let current_width = $(this).css('width').replace('px','');

            card_template += '<div class="row mt-2">';
            card_template += '<div class="col-md-6">';
            card_template += '<label>Height (px):</label>';
            card_template += '<input type="number" value="'+current_height+'" class="form-control control-height" />';
            card_template += '</div>';
            card_template += '<div class="col-md-6">';
            card_template += '<label>Width (px):</label>';
            card_template += '<input type="number" value="'+current_width+'" class="form-control control-width" />';
            card_template += '</div>';
            card_template += '</div>'; 

        }else{

            let current_font_size = $(this).css('font-size').replace('px','');
            let current_font_color = $(this).css('color');
            let current_font_weight = $(this).css('font-weight');

            card_template += '<div class="row mt-2">';

            card_template += '<div class="col-md-4 mt-2">';
            card_template += '<label>Font Size (px):</label>';
            card_template += '<input type="number" value="'+current_font_size+'" class="form-control control-font-size" />';
            card_template += '</div>';

            card_template += '<div class="col-md-4 mt-2">';
            card_template += '<label>Color:</label>';
            card_template += '<input type="text" value="'+current_font_color+'" class="form-control field-colorpicker colorpicker-element control-font-color" data-colorpicker-id="1" data-original-title="" title="">';
            card_template += '</div>';

            card_template += '<div class="col-md-4 mt-2">';
            card_template += '<label>Font weight</label>';
            card_template += '<input type="number" value="'+current_font_weight+'" class="form-control control-font-weight" />';
            card_template += '</div>';

            card_template += '</div>';

        }

        card_template += '<div class="row mt-2">';
        card_template += '<div class="col-md-12">';
        card_template += '<button type="button" data-field="'+field+'" class="btn btn-warning btn-block mt-3 btn-apply"><i class="fa fa-check"></i> Apply Changes </div>';
        card_template += '</div>';
        card_template += '</div>'; 

        card_template += '</div>';
        $('#field_container_' + orientation).html(card_template);
        $('.field-colorpicker').colorpicker()

    });

    $(document).on('click', '.btn-apply', function(e){

        let field = $(this).data('field');
        let value = '';
        let type = $('#'+field).data('type');
        let current_top = $('#'+field).css('top');
        let current_left = $('#'+field).css('left');
        $('#'+field).css('inset', '');
        if(type != 'image'){
            $('#'+field).css('width', '');
        }
        $('#'+field).css('top', current_top);
        $('#'+field).css('left', current_left);

        if($('.control-font-size')){
            value = $('.control-font-size').val();
            $('#'+field).css('font-size', value + 'px');
        }
        if($('.control-font-weight')){
            value = $('.control-font-weight').val();
            $('#'+field).css('font-weight', value);
        }
        if($('.control-font-color')){
            value = $('.control-font-color').val();
            $('#'+field).css('color', value);
        }
        if($('.control-width')){
            value = $('.control-width').val();
            $('#'+field).css('width', value);
        }
        if($('.control-height')){
            value = $('.control-height').val();
            $('#'+field).css('height', value);
        }
    });

    $('#save').on('click', function(e){
        
        let template_json = { 'front': {}, 'back': {} };
        let bg_front = '';
        let bg_back = '';

        if(validateFields($("form"))){
            return 0;
        }

        $('.id-canvass-front').children().each(function(index, dom){
            let dom_id = $(dom).attr('id');
            if(dom_id == 'id_front_background'){
                bg_front = $(dom).attr('src');
            }else{
                template_json['front'][dom_id] = $(dom).css(['top','left','width','height','font-size', 'font-weight', 'color']);
            }
        });

        $('.id-canvass-back').children().each(function(index, dom){
            let dom_id = $(dom).attr('id');
            if(dom_id == 'id_back_background'){
                bg_back = $(dom).attr('src');
            }else{
                template_json['back'][dom_id] = $(dom).css(['top','left','width','height','font-size', 'font-weight', 'color']);
            }
        });

        let properties_json = JSON.stringify(template_json);

        $('#bg_front').val(bg_front);
        $('#bg_back').val(bg_back);

        $("<input />").attr("type", "hidden")
            .attr("name", "properties_json")
            .attr("value", properties_json)
            .prependTo("form");

        $('form').submit();

    });

    $('.add_data_field').on('click', function(e){

        let type = $(this).parents('.tab-pane').attr('id');
        let field = $('#data_field_'+type+' option:selected').val();
        if(field.length <= 0){
            return 0;
        }        
        let div_field = ''; 

        switch(field){
            case 'profile_pic':
                div_field = '<div class="field-draggable" data-type="image" id="'+field+'" style="z-index:1000; font-weight:1000; position: absolute; width:100px;height:100px; top:10px;left:10px;"><img src="{{ asset('images/faces/face4.jpg') }}" alt="Profile Pic" style="width:100%;"></div>';
            break;
            case 'signature':
                div_field = '<div class="field-draggable" data-type="image" id="'+field+'" style="z-index:1000; font-weight:1000; position: absolute; width:250px;height:70px; top:10px;left:10px;"><img src="{{ asset('images/sample_signature.png') }}" alt="Signature Pic" style="width:100%;"></div>';
            break;
            case 'qr':
                div_field = '<div class="field-draggable" data-type="image" id="'+field+'" style="z-index:1000; font-weight:1000; position: absolute; width:100px;height:100px; top:10px;left:10px;"><img src="{{ asset('images/sample_qr.png') }}" alt="QR Pic" style="width:100%;"></div>';
            break;
            default:
                div_field = '<div class="field-draggable" data-type="string" id="'+field+'" style="z-index:1000; font-weight:1000; position: absolute; top:10px;left:10px;">['+field+']</div>';
        }
        
        if($('#'+field).length <= 0){
            toastr.success(field + " successfully added")
            $('.id-canvass-' + type).append(div_field);
        }else{
            toastr.error(field + " already exists or used")
        }

        $(".field-draggable").draggable();

    });

    $(document).on('click', '.template_element .element_position', function(e){

        let field = $(this).parents('.template_element').data('field');
        let direction = $(this).data('direction');
        let top = $('#'+field).css('top');
        top = parseInt(top.replace('px', ''));
        let left = $('#'+field).css('left');
        left = parseInt(left.replace('px', ''));
        let steps = parseInt($(this).parents('.template_element').find('.element_position_steps').val());
        let new_value = 0;

        switch(direction){
            case 'up':
                new_value = top - steps;
                $('#'+field).css('top', new_value + 'px');
            break;
            case 'down':
                new_value = top + steps;
                $('#'+field).css('top', new_value + 'px');
            break;
            case 'left':
                new_value = left - steps;
                $('#'+field).css('left', new_value + 'px');
            break;
            case 'right':
                new_value = left + steps;
                $('#'+field).css('left', new_value + 'px');
            break;
            default:
        }

    });

    $(document).on('click', '.template_element .remove_element', function(e){
        let field = $(this).parents('.template_element').data('field');
        $('#'+field).remove();
        $(this).parents('.template_element').remove();
    });

    $(document).on('click', '.field-draggable', function(e){
        $(".highlight-field").removeClass("highlight-field"); // reset 
        $(this).addClass("highlight-field");
    });

});

</script>

@endsection