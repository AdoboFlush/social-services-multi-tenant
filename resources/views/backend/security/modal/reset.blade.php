<div id="reset_password_modal" class="modal animated bounceInDown" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ _lang("Reset Password") }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" class="validate" autocomplete="off" action="{{ url('user/security_settings') }}" id="form_security">
                    @csrf
                    <input type="hidden" name="two_fa_type" value="security_password_reset">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="password" class="control-label">{{ _lang('New Password') }}<span class="required"> *</span></label>
                            <div class="input-group show-hide-password">
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
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="password_confirmation" class="control-label">{{ _lang('Confirm Password') }}<span class="required"> *</span></label>
                            <div class="input-group show-hide-password">
                                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
                                <div class="input-group-append">
                                    <div class="input-group-text cursor-pointer btn-show-hide-password">
                                        <i data-feather="eye-off"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 text-right">
                            <button type="button" class="btn btn-primary" id="createPassword">
                                {{ _lang('Continue') }}
                            </button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">
                                {{ _lang('Close') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>