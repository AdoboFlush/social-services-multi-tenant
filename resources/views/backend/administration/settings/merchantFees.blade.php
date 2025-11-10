@extends("backend.administration.settings._layout")

@section("form")
    <div id="fee" class="tab-pane active">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title panel-title">{{ _lang('Fee Settings') }}</h4>
                <form method="post" autocomplete="off" action="{{ url()->current() }}" enctype="multipart/form-data" id="settings-form">
                    @csrf
                    <table class="table table-borderless mt-4 border">
                        <thead>
                        <tr>
                            <th class="text-center border-bottom border-right">Internal Transfer</th>
                            <th colspan="6" class="border-bottom"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="text-center">
                            <td class="border-right"></td>
                            <td>Fee Application</td>
                            <td>Amount</td>
                            <td>Percentage</td>
                            <td>Currency</td>
                            <td>Min Value</td>
                            <td>Max Value</td>
                        </tr>
                        @foreach($fees["internal_transfer"] as $fee)
                            <tr>
                                <td class="border-right"><input class="form-control border-0 text-center text-capitalize" type="text" value="{!! str_replace('to','&#x2192;',$fee->account_type) !!}" tabindex="-1"></td>
                                <td class="applied-to">
                                    <select name="fees[{{ $fee->id }}][applied_to]" class="form-control select2">
                                        <option @if($fee->applied_to == "receiver") selected @endif>receiver</option>
                                        <option @if($fee->applied_to == "sender") selected @endif>sender</option>
                                    </select>
                                </td>
                                <td><input name="fees[{{ $fee->id }}][amount]" class="form-control text-center numeric float-only" type="text" value="{{ $fee->amount }}"></td>
                                <td><input name="fees[{{ $fee->id }}][percentage]" class="form-control text-center numeric float-only" type="text" value="{{ $fee->percentage }}"></td>
                                <td>
                                    <select name="fees[{{ $fee->id }}][currency]" class="form-control select2">
                                        <option selected value="">ALL</option>
                                        @foreach($currencies as $currency)
                                            <option @if($fee->currency == $currency->name) selected @endif>{{ $currency->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input name="fees[{{ $fee->id }}][minimum_amount]" class="form-control text-center float-only" type="text" value="{{ $fee->minimum_amount }}"></td>
                                <td><input name="fees[{{ $fee->id }}][maximum_amount]" class="form-control text-center float-only" type="text" value="{{ $fee->maximum_amount }}"></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-primary" id="save">{{ _lang('Save Settings') }}</button>
                </form>
            </div>
        </div>
    </div>
    <style>
        .applied-to .select2-selection__rendered::before {
            content: "Apply to ";
        }
        .select2-selection__rendered {
            text-align: center;
        }
    </style>
    @include('backend/administration/settings/_confirmation-modal')
@endsection

@section('js-script')
<script type="text/javascript">
    $(function(){
        $(".float-only").on("blur",function(){
            $(this).val(addZeroes($(this).val()));
        });
        $("#save").on("click",function(){
            $('#confirmation-modal').modal('show');
        });
        $("#confirm").on("click",function(){
            $.ajax({
                url: $('#settings-form').attr('action'),
                method: $('#settings-form').attr('method'),
                data: $('#settings-form').serialize(),
                beforeSend: function(){
                    $("#preloader").css("display","block");
                },success: function(data){
                    setTimeout(function(){
                        if(data['result'] == "success"){
                            toastr.success(data.message);
                        } else {
                            $.each( data['message'], function( i, val ) {
                                toastr.error(val);
                            });
                        }
                        $('.modal').modal('hide');
                        $("#preloader").css("display","none");
                    }, 500);
                },
                error: function (request, status, error) {
                    setTimeout(function(){
                        $('.modal').modal('hide');
                        $("#preloader").css("display","none");
                        toastr.error(data)
                    }, 500);
                }
            });
        })
    });
</script>
@endsection
