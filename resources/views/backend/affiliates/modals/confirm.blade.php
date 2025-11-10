<div class="row">
    <div class="col-12">
        <table class="table table-bordered">
            <input type="hidden" id="user_id" name="user_id" value="{{ $user->id }}">
            <tbody>
            <tr>
                <td><label for="parent_code">{{ _lang("Parent Affiliate Code") }}</label></td>
                <td><input type="text" class="form-control" id="parent_code" name="parent_code" autocomplete="off" @if(isset($user->affiliate_details) && $user->affiliate_details->parent_code) value="{{ $user->affiliate_details->parent_code }}" @endif></td>
            </tr>
            <tr>
                <td><label for="account_number">{{ _lang("Account Number") }}</label></td>
                <td>{{ $user->account_number }}</td>
            </tr>
            <tr>
                <td><label for="account_name">{{ _lang("Account Name") }}</label></td>
                <td>{{ $user->first_name }} {{ $user->last_name }}</td>
            </tr>
            <tr>
                <td><label for="email">{{ _lang("Email") }}</label></td>
                <td>{{ $user->email }}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-12 text-right">
        <button type="button" class="btn btn-primary px-4" id="confirm">{{ _lang('Confirm') }}</button>
        <button type="button" class="btn btn-secondary px-3" id="back">{{ _lang('Back') }}</button>
    </div>
</div>