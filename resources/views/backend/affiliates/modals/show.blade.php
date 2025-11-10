<div class="row">
    <div class="col-12">
        <table class="table table-bordered">
            <input type="hidden" id="code" name="code" value="{{ $affiliate->code }}">
            <tbody>
            <tr>
                <td><label for="parent_code">{{ _lang("Parent Affiliate Code") }}</label></td>
                <td><input value="{{ $affiliate->parent_code }}" type="text" class="form-control" id="parent_code" name="parent_code" autocomplete="off"></td>
            </tr>
            <tr>
                <td><label for="integration_url">{{ _lang("Integration URL") }}</label></td>
                <td><input value="{{ $affiliate->integration_url }}" type="text" class="form-control" id="integration_url" name="integration_url" autocomplete="off"></td>
            </tr>
            <tr>
                <td><label for="sid">{{ _lang("SID") }}</label></td>
                <td><input value="{{ $affiliate->sid }}" type="text" class="form-control" id="sid" name="sid" autocomplete="off"></td>
            </tr>
            <tr>
                <td><label for="code">{{ _lang("Affiliate Code") }}</label></td>
                <td>{{ $affiliate->code }}</td>
            </tr>
            <tr>
                <td><label for="referral_link">{{ _lang("Referral Link") }}</label></td>
                <td class="text-break-all">{{ url('register?ref=' . md5($affiliate->user->id)) }}</td>
            </tr>
            <tr>
                <td><label for="referral_switch">{{ _lang("Enable Referral Link") }}</label></td>
                <td>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="referral_switch" name="referral_switch" @if($affiliate->referral_switch) checked @endif value="1">
                        <label class="custom-control-label" for="referral_switch"></label>
                    </div>
                </td>
            </tr>
            <tr>
                <td><label for="kyc_privilege_switch">{{ _lang("Enable KYC Exemption") }}</label></td>
                <td>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="kyc_privilege_switch" name="kyc_privilege_switch" @if($affiliate->kyc_privilege_switch) checked @endif value="1">
                        <label class="custom-control-label" for="kyc_privilege_switch"></label>
                    </div>
                </td>
            </tr>
            <tr>
                <td><label for="account_number">{{ _lang("Account Number") }}</label></td>
                <td>{{ $affiliate->user->account_number }}</td>
            </tr>
            <tr>
                <td><label for="account_name">{{ _lang("Account Name") }}</label></td>
                <td>{{ $affiliate->user->first_name }} {{ $affiliate->user->last_name }}</td>
            </tr>
            <tr>
                <td><label for="email">{{ _lang("Email") }}</label></td>
                <td>{{ $affiliate->user->email }}</td>
            </tr>
            <tr>
                <td><label for="members">{{ _lang("Total No. of Members") }}</label></td>
                <td>{{ $affiliate->members->count() }}</td>
            </tr>
            <tr>
                <td><label for="registered_date">{{ _lang("Registered Date") }}</label></td>
                <td>{{ $affiliate->created_at }}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-12 text-right">
        <button type="button" class="btn btn-primary px-4" id="save">{{ _lang('Save') }}</button>
        <button type="button" class="btn btn-secondary px-3" id="close" data-dismiss="modal">{{ _lang('Close') }}</button>
    </div>
</div>