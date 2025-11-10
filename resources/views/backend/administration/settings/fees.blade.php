@extends("backend.administration.settings._layout")

@section("form")
    <div id="fee" class="tab-pane active">
        @if(isset($user) && $user)
        <div class="row my-5">
            <div class="col-5">
                <table class="table table-bordered">
                    <tr><td>{{ _lang('Account Number') }}</td><td><a href="{{ route('users.edit',$user->account_number) }}" target="_blank" data-account_number="{{ $user->account_number }}" data-user_id="{{ $user->id }}" class="account_number_link">{{ $user->account_number }}</a></td></tr>
                    <tr><td>{{ _lang('Account Name') }}</td><td>{{ $user->first_name }} {{ $user->last_name }}</td></tr>
                    <tr><td>{{ _lang('Email') }}</td><td>{{ $user->email }}</td></tr>
                    <tr><td>{{ _lang('Account Status') }}</td><td>{{ is_null($user->account_status) ? 'Unverified' : $user->account_status }}</td></tr>
                </table>
            </div>
            <div class="col-5">
                <form class="appsvan-submit" method="post" autocomplete="off" action="{{ route("merchant.fee.update",$user->id) }}" enctype="multipart/form-data" id="fee-remarks-form">
                    @csrf
                    <div class="form-group mb-4">
                        <label class="control-label" for="remarks">Remarks</label>
                        <textarea class="form-control" name="fee_remarks" id="remarks" rows="3">{{ $user->fee_remarks }}</textarea>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-update">Update</button>
                    </div>
                </form>
            </div>
        </div>
        @endif
        <div class="card">
            <div class="card-body">
                <h4 class="card-title panel-title">@if($user) {{ _lang('Merchant Fee Settings') }} @else {{ _lang('Fee Settings') }} @endif</h4>
                <form method="post" autocomplete="off" action="{{ url()->current() }}" enctype="multipart/form-data" id="settings-form">
                    @csrf
                    <table class="table table-borderless mt-4 border">
                        <thead>
                            <tr>
                                <th class="text-center border-bottom border-right">Withdrawal</th>
                                <th colspan="5" class="border-bottom border-right text-center">Business</th>
                                <th colspan="5" class="border-bottom text-center">Personal</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center">
                                    <td class="border-right"></td>
                                    @for($i=0; $i<2; $i++)
                                        <td>Amount</td>
                                        <td>Percentage</td>
                                        <td>Currency</td>
                                        <td>Min Value</td>
                                        <td class="border-right">Max Value</td>
                                    @endfor
                                </tr>
                                @foreach (\App\Fee::FEE_WITHDRAWAL_TYPE as $key => $withdrawalService)
                                    @if(isset($fees[$withdrawalService]) && $fees[$withdrawalService])
                                    <tr>
                                        <td class="border-right"><input class="form-control border-0 text-center text-capitalize p-0" type="text" value="{{ $key }}" tabindex="-1"></td>
                                        @foreach(\App\Account::TYPES as $accountType)
                                            @if(isset($fees[$withdrawalService][$accountType]))
                                            <td>
                                                <input type="hidden" name="fees[{{ $fees[$withdrawalService][$accountType]->id }}][applied_to]" value="self">
                                                <input name="fees[{{ $fees[$withdrawalService][$accountType]->id }}][amount]" class="form-control text-center numeric float-only" type="text" value="{{ $fees[$withdrawalService][$accountType]->amount }}">
                                            </td>
                                            <td><input name="fees[{{ $fees[$withdrawalService][$accountType]->id }}][percentage]" class="form-control text-center numeric float-only" type="text" value="{{ $fees[$withdrawalService][$accountType]->percentage }}"></td>
                                            <td>
                                                <select name="fees[{{ $fees[$withdrawalService][$accountType]->id }}][currency]" class="form-control select2">
                                                    <option selected value="">ALL</option>
                                                    @foreach($currencies as $currency)
                                                        <option @if($fees[$withdrawalService][$accountType]->currency == $currency->name) selected @endif>{{ $currency->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input name="fees[{{ $fees[$withdrawalService][$accountType]->id }}][minimum_amount]" class="form-control text-center float-only" type="text" value="{{ $fees[$withdrawalService][$accountType]->minimum_amount }}"></td>
                                            <td class="border-right"><input name="fees[{{ $fees[$withdrawalService][$accountType]->id }}][maximum_amount]" class="form-control text-center float-only" type="text" value="{{ $fees[$withdrawalService][$accountType]->maximum_amount }}"></td>
                                            @else
                                                @for($i = 1; $i <5; $i++)
                                                <td><input class="form-control text-center numeric" type="text" readonly></td>
                                                @endfor
                                                <td class="border-right"><input class="form-control text-center numeric" type="text" readonly></td>
                                            @endif
                                        @endforeach
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                    </table>
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
                    <table class="table table-borderless mt-4 border">
                        <thead>
                        <tr>
                            <th class="text-center border-bottom border-right">Payment Request</th>
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
                        @foreach($fees["payment_request"] as $fee)
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

                    <table class="table table-borderless mt-4 border">
                        <thead>
                            <tr>
                                <th class="text-center border-bottom border-right">Other Fees</th>
                                <th colspan="5" class="border-bottom border-right text-center">Business</th>
                                <th colspan="5" class="border-bottom text-center">Personal</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center">
                                    <td class="border-right"></td>
                                    @for($i=0; $i<2; $i++)
                                        <td>Amount</td>
                                        <td>Percentage</td>
                                        <td>Currency</td>
                                        <td>Min Value</td>
                                        <td class="border-right">Max Value</td>
                                    @endfor
                                </tr>
                                @foreach (\App\Fee::FEE_OTHER_TYPE as $key => $otherFeeService)
                                    @if(isset($fees[$otherFeeService]) && $fees[$otherFeeService])
                                        <tr>
                                            <td class="border-right"><input class="form-control border-0 text-center text-capitalize p-0" type="text" value="{{ $key }}" tabindex="-1"></td>
                                            @foreach(\App\Account::TYPES as $accountType)
                                                @if(isset($fees[$otherFeeService][$accountType]))
                                                <td>
                                                    <input type="hidden" name="fees[{{ $fees[$otherFeeService][$accountType]->id }}][applied_to]" value="self">
                                                    <input name="fees[{{ $fees[$otherFeeService][$accountType]->id }}][amount]" class="form-control text-center numeric float-only" type="text" value="{{ $fees[$otherFeeService][$accountType]->amount }}">
                                                </td>
                                                <td><input name="fees[{{ $fees[$otherFeeService][$accountType]->id }}][percentage]" class="form-control text-center numeric float-only" type="text" value="{{ $fees[$otherFeeService][$accountType]->percentage }}"></td>
                                                <td>
                                                    <select name="fees[{{ $fees[$otherFeeService][$accountType]->id }}][currency]" class="form-control select2">
                                                        <option selected value="">ALL</option>
                                                        @foreach($currencies as $currency)
                                                            <option @if($fees[$otherFeeService][$accountType]->currency == $currency->name) selected @endif>{{ $currency->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input name="fees[{{ $fees[$otherFeeService][$accountType]->id }}][minimum_amount]" class="form-control text-center float-only" type="text" value="{{ $fees[$otherFeeService][$accountType]->minimum_amount }}"></td>
                                                <td class="border-right"><input name="fees[{{ $fees[$otherFeeService][$accountType]->id }}][maximum_amount]" class="form-control text-center float-only" type="text" value="{{ $fees[$otherFeeService][$accountType]->maximum_amount }}"></td>
                                                @else
                                                    @for($i = 1; $i <5; $i++)
                                                    <td><input class="form-control text-center numeric" type="text" readonly></td>
                                                    @endfor
                                                    <td class="border-right"><input class="form-control text-center numeric" type="text" readonly></td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                    </table>

                    @if(empty($user))
                    <table class="table table-borderless mt-4 border">
                        <thead>
                            <tr>
                                <th class="text-center border-bottom border-right">Markup Fees Rate</th>
                                <th colspan={{ count($fees["markup_rate"]["all"]) }} class="border-bottom border-right text-center">Currencies</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center">
                                    <td class="border-right"></td>
                                    @foreach ($fees["markup_rate"]["all"] as $key => $value)
                                    <td>{{ $key }}</td>
                                    @endforeach
                                </tr>
                                @foreach ($fees["markup_rate"]["all"] as $from_currency_key => $from_currency_value)
                                <tr>
                                    <td class="border-right text-center">
                                        <input class="form-control border-0 text-center text-capitalize" type="text" value="{{ $from_currency_key }}" tabindex="-1">
                                    </td>
                                    @foreach ($from_currency_value as $to_currency_key => $fee)
                                        <td><input name="fees[{{ $fee->id }}][percentage]" class="form-control text-center numeric float-only markup-text" type="text" value="{{ $fee->percentage }}" @if($from_currency_key == $to_currency_key) disabled @endif></td>
                                    @endforeach
                                </tr>
                                @endforeach
                                <tr>
                                    <td class="border-right text-center">
                                        <div class="text-center p-2"><input class="mx-2" type="checkbox" id="applyForAll">APPLY TO ALL</div>
                                    </td>
                                    <td>
                                        <input class="form-control text-center numeric float-only markup-text" id="applyForAllText" type="text" disabled>
                                    </td>
                                </tr>
                            </tbody>
                    </table>
                    @endif
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
        $("#applyForAll").on("change",function(){
            if(this.checked) {
                $("#applyForAllText").prop("disabled",false);
            } else {
                $("#applyForAllText").prop("disabled",true);
            }
        })
        $("#applyForAllText").on("keyup", function(){
            $(".markup-text:not(:disabled)").val(addZeroes($(this).val()));
        });
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
        });
    });
</script>
@endsection
