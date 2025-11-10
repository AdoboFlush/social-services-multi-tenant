<div class="row">
    <div class="col">
        <form method="POST" class="validate" autocomplete="off" action="{{ url('user/security_settings/confirm') }}" id="form_security">
            @csrf
            <div class="row">
                <div class="col-12 px-1 px-sm-3">
                    <label for="password" class="control-label">
                        {{ _lang('Please enter your master password to continue using this service.') }}
                        <span class="required"> *</span>
                    </label>
                    <div class="input-group show-hide-password max-w-500">
                        <input id="password" type="password" class="form-control" name="password" required>
                        <div class="input-group-append">
                            <div class="input-group-text cursor-pointer btn-show-hide-password">
                                <i data-feather="eye-off"></i>
                            </div>
                        </div>
                    </div>
                    <span class="v-error" id="password_error"></span>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary" id="createPassword">
                        {{ _lang('Confirm') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
