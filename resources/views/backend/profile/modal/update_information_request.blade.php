<form id='change-of-dob-name-form' method="post" autocomplete="off" action="{{url('profile/send_request')}}" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('First Name') }}</label>
                <input type="text" class="form-control" name="first_name">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Last Name') }}</label>
                <input type="text" class="form-control" name="last_name">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group mb-0">
                <label class="control-label">{{ _lang('Date of Birth') }}</label>
            </div>
        </div>
        <div class="col-5 pr-1">
            <select class="select2 form-control pl-2 {{ $errors->has('date_of_birth') ? 'is-invalid' : '' }}" id="month" name="month">
                <option value="">{{ _lang("Month") }}</option>
                <option value="1" @if(old('month') == 1) selected @endif>{{ _lang("January") }}</option>
                <option value="2" @if(old('month') == 2) selected @endif>{{ _lang("February") }}</option>
                <option value="3" @if(old('month') == 3) selected @endif>{{ _lang("March") }}</option>
                <option value="4" @if(old('month') == 4) selected @endif>{{ _lang("April") }}</option>
                <option value="5" @if(old('month') == 5) selected @endif>{{ _lang("May") }}</option>
                <option value="6" @if(old('month') == 6) selected @endif>{{ _lang("June") }}</option>
                <option value="7" @if(old('month') == 7) selected @endif>{{ _lang("July") }}</option>
                <option value="8" @if(old('month') == 8) selected @endif>{{ _lang("August") }}</option>
                <option value="9" @if(old('month') == 9) selected @endif>{{ _lang("September") }}</option>
                <option value="10" @if(old('month') == 10) selected @endif>{{ _lang("October") }}</option>
                <option value="11" @if(old('month') == 11) selected @endif>{{ _lang("November") }}</option>
                <option value="12" @if(old('month') == 12) selected @endif>{{ _lang("December") }}</option>
            </select>
        </div>
        <div class="col-3 px-1">
            <select class="select2 form-control pl-2 {{ $errors->has('date_of_birth') ? 'is-invalid' : '' }}" id="day" name="day">
                <option value="">{{ _lang('Day') }}</option>
            </select>
        </div>
        <div class="col-4 pl-1">
            <select class="select2 form-control pl-2 {{ $errors->has('date_of_birth') ? 'is-invalid' : '' }}" id="year" name="year"></select>
        </div>
        <div class="col-md-12">
            <input class="form-control {{ $errors->has('date_of_birth') ? 'is-invalid' : '' }}" type="hidden" id="date_of_birth" name="date_of_birth">
            @if ($errors->has('date_of_birth'))
                <span class="invalid-feedback">
                    <strong>{{ _lang($errors->first('date_of_birth')) }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mt-3">
            <div class="form-group text-right">
                <button type="submit" class="btn btn-primary">{{ _lang('Continue') }}</button>
                <button type="reset" class="btn btn-danger" data-dismiss="modal">{{ _lang('Close') }}</button>
            </div>
        </div>
    </div>

</form>

@section('js-script')
    @parent
    <script type="text/javascript">
        $(function(){

            var year = $("#year").val() ? $("#year").val() : $("#year option:eq(1)").val();
            var month = $("#month").val();
            var oldDay = '{{ old('day') }}';
            var oldYear = '{{ old('year') }}';

            if(month){
                var totalDays = new Date(year, month, 0).getDate();
                var days = "<option value=''>{{ _lang('Day') }}</option>";
                for(var i=1; i<=totalDays; i++){
                    if(oldDay == i){
                        days += "<option selected value='" + i + "'>"+i+"</option>";
                    } else
                        days += "<option value='" + i + "'>"+i+"</option>";
                }
                $("#day").html(days);
                if(oldYear)
                    $('#year').find('option[value=' +oldYear+']').attr("selected",true);

            }

            $("#month").on("change",function(){
                var year = $("#year").val() ? $("#year").val() : $("#year option:eq(1)").val();
                var month = $(this).val();
                var totalDays = new Date(year, month, 0).getDate();
                var days = "<option value=''>{{ _lang('Day') }}</option>";
                for(var i=1; i<=totalDays; i++){
                    days += "<option value='" + i + "'>"+i+"</option>";
                }
                $("#day").html(days);
            });

            $("#year").on("change",function(){
                var year = $(this).val();
                var month = $("#month").val();
                var totalDays = new Date(year, month, 0).getDate();
                if($("#day option:last").val() != totalDays){
                    var days = "<option value=''>{{ _lang('Day') }}</option>";
                    for(var i=1; i<=totalDays; i++){
                        days += "<option value='" + i + "'>"+i+"</option>";
                    }
                    $("#day").html(days);
                }
            });

            $("#change-of-dob-name-form").on("submit",function(e){
                e.preventDefault();

                $(".alert").hide();
                var year = $("#year").val();
                var month = $("#month").val();
                var day = $("#day").val();

                if((!year && !month && !day) || (year && month && day)){
                    $("#date_of_birth").val(year+"-"+month+"-"+day);
                    var link = $(this).attr("action");
                    var formData = new FormData(this);

                    $.ajax({
                        method: 'POST',
                        url: link,
                        data:  formData,
                        mimeType:"multipart/form-data",
                        contentType: false,
                        cache: false,
                        processData:false,
                        beforeSend: function(){
                            $("#preloader").css("display","block");
                        },success: function(data){
                            var jsonData = JSON.parse(data);
                            setTimeout(function(){
                                if(jsonData['result'] == "success"){
                                    $("#preloader").css("display","none");
                                    $(".alert-success").text(jsonData['message']).show();
                                } else {
                                    $("#preloader").css("display","none");
                                    $(".alert-danger").text(jsonData['message']).show();
                                }
                            }, 500);
                        },
                        error: function (request, status, error) {
                            setTimeout(function(){
                                $("#preloader").css("display","none");
                                toastr.error("An Error Occurred ");
                            }, 500);
                        }
                    });
                } else {
                    $(".alert-danger").text('{{ _lang("Invalid date of birth") }}').show();
                }
            });
        });
    </script>
@endsection