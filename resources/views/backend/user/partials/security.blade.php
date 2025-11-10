<div class="card mb-5">
    <div class="card-body">
        <h4 class="card-title panel-title">{{ _lang('Master Password') }}</h4>
        <form method="POST" autocomplete="off" action="{{ url('admin/security_settings/edit/'.$user->id) }}" id="form_security">
            @csrf
            <div class="form-check">
                <input class="form-check-input" type="radio" name="status" id="enabled" value="1" @if($user->security->status == 1)checked @endif>
                <label class="form-check-label" for="enabled">
                    Enable
                </label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="status" id="disabled" value="0" @if($user->security->status == 0)checked @endif>
                <label class="form-check-label" for="disabled">
                    Disable
                </label>
            </div>
            @if($user->security->status == 1 && $user->security->password)
            <a href="javascript:void(0)" class="pl-3" id="resetMasterPassword">Reset Password</a>
            @endif
            <div class="form-group pl-3 mt-2">
                <button type="submit" class="btn btn-primary">
                    {{ _lang('Save') }}
                </button>
            </div>
        </form>
    </div>
</div>