@extends("backend.administration.settings._layout")

@section("form")
    <div id="banking" class="tab-pane active">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title panel-title">{{ _lang('Banking Settings') }}</h4>
                <form method="post" class="appsvan-submit params-panel" autocomplete="off" action="{{ url('admin/administration/settings/banking') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Account Number Prefix').' ('._lang('Max 10').')' }}</label>
                                <input type="text" class="form-control" name="account_number_prefix" maxlength="10" value="{{ get_option('account_number_prefix') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Next Account Number') }}</label>
                                <input type="number" class="form-control" name="next_account_number" maxlength="10" value="{{ get_option('next_account_number',date('Y').'1001') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Currency Converter') }}</label>
                                <select class="form-control" name="currency_converter" required>
                                    <option value="manual" {{ get_option('currency_converter') == 'manual' ? 'selected' : '' }}>{{ _lang('Manual') }}</option>
                                    <option value="fixer" {{ get_option('currency_converter') == 'fixer' ? 'selected' : '' }}>{{ _lang('Fixer API') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Fixer API Key') }}</label>
                                <a href="https://fixer.io/" target="_blank" class="float-right">{{ _lang('GET API KEY') }}</a>
                                <input type="text" class="form-control" name="fixer_api_key" value="{{ get_option('fixer_api_key') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Allow Singup') }}</label>
                                <select class="form-control" name="allow_singup" required>
                                    <option value="yes" {{ get_option('allow_singup') == 'yes' ? 'selected' : '' }}>{{ _lang('Yes') }}</option>
                                    <option value="no" {{ get_option('allow_singup') == 'no' ? 'selected' : '' }}>{{ _lang('No') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Need Approval On Transfer Between Users') }}</label>
                                <select class="form-control" name="tbu_approval" required>
                                    <option value="no" {{ get_option('tbu_approval') == 'no' ? 'selected' : '' }}>{{ _lang('No') }}</option>
                                    <option value="yes" {{ get_option('tbu_approval') == 'yes' ? 'selected' : '' }}>{{ _lang('Yes') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Need Approval On Transfer Between Accounts') }}</label>
                                <select class="form-control" name="tba_approval" required>
                                    <option value="no" {{ get_option('tba_approval') == 'no' ? 'selected' : '' }}>{{ _lang('No') }}</option>
                                    <option value="yes" {{ get_option('tba_approval') == 'yes' ? 'selected' : '' }}>{{ _lang('Yes') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('User Referral commission') }} (%)</label>
                                <input type="text" class="form-control" name="user_referral_commission" value="{{ get_option('user_referral_commission') }}">
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