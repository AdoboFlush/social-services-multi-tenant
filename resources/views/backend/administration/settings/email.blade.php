@extends("backend.administration.settings._layout")

@section("form")
    <div id="email" class="tab-pane active">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title panel-title">{{ _lang('Email Settings') }}</h4>
                <form method="post" class="appsvan-submit params-panel" autocomplete="off" action="{{ url('admin/administration/settings/email') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Mail Type') }}</label>
                                <select class="form-control niceselect wide" name="mail_type" id="mail_type" required>
                                    <option value="mail" {{ get_option('mail_type')=="mail" ? "selected" : "" }}>{{ _lang('PHP Mail') }}</option>
                                    <option value="smtp" {{ get_option('mail_type')=="smtp" ? "selected" : "" }}>{{ _lang('SMTP') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('From Email') }}</label>
                                <input type="text" class="form-control" name="from_email" value="{{ get_option('from_email') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('From Name') }}</label>
                                <input type="text" class="form-control" name="from_name" value="{{ get_option('from_name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('SMTP Host') }}</label>
                                <input type="text" class="form-control smtp" name="smtp_host" value="{{ get_option('smtp_host') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('SMTP Port') }}</label>
                                <input type="text" class="form-control smtp" name="smtp_port" value="{{ get_option('smtp_port') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('SMTP Username') }}</label>
                                <input type="text" class="form-control smtp" autocomplete="off" name="smtp_username" value="{{ get_option('smtp_username') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('SMTP Password') }}</label>
                                <input type="password" class="form-control smtp" autocomplete="off" name="smtp_password" value="{{ get_option('smtp_password') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('SMTP Encryption') }}</label>
                                <select class="form-control smtp" name="smtp_encryption">
                                    <option value="ssl" {{ get_option('smtp_encryption')=="ssl" ? "selected" : "" }}>{{ _lang('SSL') }}</option>
                                    <option value="tls" {{ get_option('smtp_encryption')=="tls" ? "selected" : "" }}>{{ _lang('TLS') }}</option>
                                </select>
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

@section('js-script')
    <script type="text/javascript">
        if($("#mail_type").val() != "smtp"){
            $(".smtp").prop("disabled",true);
        }
        $(document).on("change","#mail_type",function(){
            if( $(this).val() != "smtp" ){
                $(".smtp").prop("disabled",true);
            }else{
                $(".smtp").prop("disabled",false);
            }
        });

    </script>
@stop