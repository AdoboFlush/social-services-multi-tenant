@extends("backend.administration.settings._layout")

@section("form")
    <div id="maintenance" class="tab-pane active">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title panel-title">{{ _lang('Maintenance') }}</h4>
                <form method="post" class="appsvan-submit params-panel" autocomplete="off" action="{{ url('admin/administration/settings/maintenance') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="hidden" name="isMaintenance" value="0">
                                    <input {{ get_option('isMaintenance') ? "checked" : "" }} type="checkbox" class="custom-control-input" id="isMaintenance" name="isMaintenance" value="1">
                                    <label class="custom-control-label" for="isMaintenance">{{ _lang('Maintenance Mode') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>{{ _lang('Message') }}</label>
                                <textarea class="form-control summernote" name="maintenanceText">{{ get_option('maintenanceText') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">{{ _lang('Save Settings') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection