<form method="post" class="validate" style="width:100%;" autocomplete="off" action="{{ route('poll.candidates.store', [], false) }}" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Name') }}</label>
                <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" required>
                <span class="err-message">{{ _lang('Name is required.') }}</span>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Remarks') }}</label>
                <textarea class="form-control" name="remarks" id="remarks">{{ old('remarks') }}</textarea>
            </div>
        </div>

        <div class="col-md-6">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label">{{ _lang('Profile Image') }} ( 300 X 300 {{ _lang('for better view') }} )</label>
                    <input type="file" accept="image/*" class="dropify" name="image" data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG" data-default-file="">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-save">{{ _lang('Save') }}</button>
                <button type="reset" class="btn btn-danger">{{ _lang('Reset') }}</button>
            </div>
        </div>
    </div>
</form>
