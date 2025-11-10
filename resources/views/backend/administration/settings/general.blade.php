@extends("backend.administration.settings._layout")

@section("form")
    <div id="general" class="tab-pane active">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title panel-title">{{ _lang('General Settings') }}</h4>
                <form method="post" class="appsvan-submit params-panel" autocomplete="off" action="{{ url('admin/administration/settings/general') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Company Name') }}</label>
                                <input type="text" class="form-control" name="company_name" value="{{ get_option('company_name') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Site Title') }}</label>
                                <input type="text" class="form-control" name="site_title" value="{{ get_option('site_title') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Phone') }}</label>
                                <input type="text" class="form-control" name="phone" value="{{ get_option('phone') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Email') }}</label>
                                <input type="text" class="form-control" name="email" value="{{ get_option('email') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Timezone') }}</label>
                                <select class="form-control select2" name="timezone" required>
                                    <option value="">{{ _lang('-- Select One --') }}</option>
                                    {{ create_timezone_option(get_option('timezone')) }}
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Language') }}</label>
                                <select class="form-control select2" name="language" required>
                                    {!! load_language( get_option('language') ) !!}
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Email Verification') }}</label>
                                <select class="form-control" name="email_verification" required>
                                    <option value="No" {{ get_option('email_verification') == 'No' ? 'selected' : '' }}>{{ _lang('No') }}</option>
                                    <option value="Yes" {{ get_option('email_verification') == 'Yes' ? 'selected' : '' }}>{{ _lang('Yes') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Copyright Text') }}</label>
                                <input type="text" class="form-control" name="copyright" value="{{ get_option('copyright') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Address') }}</label>
                                <textarea class="form-control" name="address">{{ get_option('address') }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">{{ _lang('Save Settings') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection