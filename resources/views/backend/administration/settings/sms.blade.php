@extends("backend.administration.settings._layout")

@section("form")
    <div id="sms" class="tab-pane active">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title panel-title">{{ _lang('SMS Settings') }}</h4>
                <form method="post" class="appsvan-submit params-panel" autocomplete="off" action="{{ url('admin/administration/settings/sms') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('TWILIO SID') }}</label>
                                        <input type="text" class="form-control" name="TWILIO_SID" value="{{ get_option('TWILIO_SID') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('TWILIO TOKEN') }}</label>
                                        <input type="text" class="form-control" name="TWILIO_TOKEN" value="{{ get_option('TWILIO_TOKEN') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('TWILIO MOBILE NUMBER') }}</label>
                                        <input type="text" class="form-control" name="TWILIO_MOBILE" value="{{ get_option('TWILIO_MOBILE') }}" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">{{ _lang('Save Settings') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection