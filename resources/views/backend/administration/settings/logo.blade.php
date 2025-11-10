@extends("backend.administration.settings._layout")

@section("form")
    <div id="logo" class="tab-pane active">
        <div class="card">
            <div class="card-body">
                <form method="post" class="appsvan-submit params-panel" autocomplete="off" action="{{ url('admin/administration/upload_logo') }}" enctype="multipart/form-data">
                    <h4 class="card-title panel-title">{{ _lang('Logo Upload') }}</h4>
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Upload Logo') }}</label>
                                <input type="file" accept="iamge/*" class="form-control dropify" name="logo" data-max-file-size="8M" data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG" data-default-file="{{ get_logo() }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block">{{ _lang('Upload') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection