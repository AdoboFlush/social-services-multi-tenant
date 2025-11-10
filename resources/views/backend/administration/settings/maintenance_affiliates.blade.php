@extends('backend.administration.settings._layout')

@section('form')
    <div id="maintenance" class="tab-pane active">
        <div class="card">
            <form method="post" class="appsvan-submit params-panel" autocomplete="off"
                action="{{ url('admin/administration/settings/service_maintenance') }}" enctype="multipart/form-data">
                <div class="card-body">
                    <h4 class="card-title panel-title">{{ _lang('Maintenance') }}</h4>
                    @csrf
                    <div class="row">
                        <div class="col-md-6 p-0">
                            <div class="form-group">
                                <label>{{ _lang('Solutions') }}</label>
                                <select class="form-control select2" data-select="maintenance" name="maintenance_id"
                                    required>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div data-wrapper="maintenance">
                        @foreach ($services as $service)
                            <div class="d-none" id="{{ $service->id }}">
                                <div class="col-12">
                                    <div class="custom-control custom-switch">
                                        <div class="custom-control custom-switch p-0 mb-2">
                                            <input type="checkbox" class="custom-control-input"
                                                name="isMaintenance[{{ $service->id }}]"
                                                id="isMaintenance{{ $service->id }}"
                                                {{ $service->isMaintenance ? 'checked' : '' }}>
                                            <label class="custom-control-label"
                                                for="isMaintenance{{ $service->id }}">{{ _lang('Maintenance Mode') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 pr-0">
                                        <div class="form-group">
                                            <div class="row">
                                                <label
                                                    class="col-sm-3 col-form-label">{{ _lang('Applies to affiliate code:') }}</label>
                                                <div class="col pr-0">
                                                    <select name="applies_to_{{ $service->id }}[]"
                                                        class="form-control select2" multiple="multiple" data-select="codes"
                                                        id="select_applies_{{ $service->id }}">
                                                        @foreach ($service->affiliate_codes as $code)
                                                            @isset($code->applies_to)
                                                                <option value="{{ $code->applies_to }}" selected>
                                                                    {{ $code->applies_to }}</option>
                                                            @endisset
                                                        @endforeach
                                                        @foreach ($affiliates as $affiliate)
                                                            <option value="{{ $affiliate->code }}">
                                                                {{ $affiliate->code }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <a class="btn btn-primary custom-btn-xs mt-2"
                                                        data-button="clearAppliesCode" id="{{ $service->id }}">Clear
                                                        Affiliate Codes</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <label class="col-sm-3 col-form-label">{{ _lang('Exception:') }}</label>
                                                <div class="col pr-0">
                                                    <select name="exception_{{ $service->id }}[]"
                                                        class="form-control select2" multiple="multiple" data-select="codes"
                                                        id="select_exception_{{ $service->id }}">
                                                        @foreach ($service->affiliate_codes as $code)
                                                            @isset($code->exception)
                                                                <option value="{{ $code->exception }}" selected>
                                                                    {{ $code->exception }}</option>
                                                            @endisset
                                                        @endforeach
                                                        @foreach ($affiliates as $affiliate)
                                                            <option value="{{ $affiliate->code }}">
                                                                {{ $affiliate->code }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <a class="btn btn-primary custom-btn-xs mt-2"
                                                        data-button="clearExcemptionCode" id="{{ $service->id }}">Clear
                                                        Affiliate Codes</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- WYSWYG --}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ _lang('English') }}</label>
                                            <textarea class="form-control summernote" id="maintenance_content"
                                                name="content[{{ $service->id }}]">{{ $service->content }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ _lang('Japanese') }}</label>
                                            <textarea class="form-control summernote" id="maintenance_jp_content"
                                                name="jp_content[{{ $service->id }}]">{{ $service->jp_content }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{-- Save button --}}
                    @can('maintenance_service_update')
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">{{ _lang('Save Settings') }}</button>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js-script')
    <script type="text/javascript">
        $(document).ready(function() {
            $('div[data-wrapper=maintenance] > div:first-child').addClass("d-block");

            let previous_maintenance_id = $('select[data-select=maintenance]').find(":selected").val();

            $('select[data-select=maintenance]').on('focus', function() {
                previous_maintenance_id = this.value;
            }).change(function() {
                $(`div[data-wrapper=maintenance] #${previous_maintenance_id}`).removeClass("d-block");

                let maintenance_id = $('select[data-select=maintenance]').find(":selected").val();
                $(`div[data-wrapper=maintenance] #${maintenance_id}`).addClass("d-block");

                previous_maintenance_id = this.value;
            });

            $('select[data-select=codes]').each(function(index, object) {
                if ($(object).find('option[value="NO_AFFILIATES"]').length == 0) {
                    $(this).prepend($('<option>', {
                        value: "NO_AFFILIATES",
                        text: "NO_AFFILIATES",
                    }));
                }
            });

            $('a[data-button=clearAppliesCode]').click(function() {
                let id = $(this).attr('id')
                $(`#select_applies_${id}`).val(null).trigger("change")
            })

            $('a[data-button=clearExcemptionCode]').click(function() {
                let id = $(this).attr('id')
                $(`#select_exception_${id}`).val(null).trigger("change")
            })

        });
    </script>
@endsection
