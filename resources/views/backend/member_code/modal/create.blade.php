<form method="post" class="validate" autocomplete="off" action="{{ route('member_codes.generate', [], false) }}"
    enctype="multipart/form-data">
    <input type="hidden" name="email_notif" value="0">
    <div class="row">
        <div class="col-md-12">
            {{ csrf_field() }}
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="control-label">{{ _lang('Number of Codes to Generate') }}</label>
                        <input type="number" class="form-control" name="count" id="count"
                            value="{{ old('count') }}" value="1" min="1" max="10000" required>
                        <span class="err-message">{{ _lang('Count is required.') }}</span>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label class="control-label d-block">&nbsp;</label>
                        <button class="btn btn-secondary" id="btn-generate" type="submit">
                            <i class="fa fa-plus mr-2"></i>{{ _lang('Generate') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
