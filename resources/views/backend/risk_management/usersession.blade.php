@extends('layouts.app')

@section('content')
    <div class="sb-page-header pb-2 sb-page-header-dark bg-gradient-primary-to-secondary">
        <div class="container-fluid">
            <div class="sb-page-header-content py-3">
                <h1 class="sb-page-header-title">
                    <div class="sb-page-header-icon"><i data-feather="alert-octagon"></i></div>
                    <span>User Session</span>
                </h1>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="filter">
                            <div class="row">
                                <div class="col-lg-6 col-md-12 mb-3">
                                    <label class="control-label">{{ _lang('Search') }}</label>
                                    <div class="row search-input-group">
                                        <div class="col-md-5 col-s-12">
                                            <select class="form-control" name="search_type" id="search_type">
                                                <option selected="selected" value="account_name">Account Name</option>
                                                <option value="account_number">Account Number</option>
                                                <option value="ip_address">IP Address</option>
                                            </select>
                                        </div>
                                        <div class="col-md-7 col-s-12">
                                            <input type="text" class="form-control" name="search"
                                                placeholder="Search">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('From') }}</label>
                                        <input type="text" class="form-control datepicker" name="date_from" id="date_from"
                                            readOnly="true" placeholder="From">
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('To') }}</label>
                                        <input type="text" class="form-control datepicker" name="date_to" id="date_to"
                                            readOnly="true" placeholder="To">
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-4 col-sm-12">
                                    <label class="control-label">{{ _lang('Method') }}</label>
                                    <select class="form-control" name="method" id="method">
                                        <option value="">All</option>
                                        <option value="api">API</option>
                                        <option value="oriental wallet">Oriental Wallet</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 text-left mt-3 mb-3">
                                    <button type="button" id="btn-search"
                                        class="btn btn-primary px-3 mr-2">{{ _lang('Search') }}</button>
                                    <button type="button" id="reset"
                                        class="btn btn-secondary px-3">{{ _lang('Reset') }}</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-container">
                            <div class="row my-3">
                                <div class="col-6">
                                    <label>Show
                                        <select class="form-control select2-row-number" id="rows">
                                            <option selected>10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select> entries
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="badge badge-info my-2 loader">
                            <span class="text-light">Loading</span>
                            <div class="spinner-border spinner-border-sm text-light" role="status"></div>
                        </div>
                        <div id="ajax-table"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('mustache.risk_management_history.all-table')

@section('js-script')
    <script src="https://cdn.jsdelivr.net/npm/handlebars@latest/dist/handlebars.js"></script>
    <script type="text/javascript">
        var MUSTACHE_TABLE_API = '/admin/risk-management/history';
        // var MUSTACHE_EXPORT_API = '';
        var MUSTACHE_TABLE_HEADERS = {
            headers: [
                "Account Number",
                "Name",
                "Account Type",
                "Method",
                "IP Address",
                "Device",
                "Date",
            ]
        };
    </script>
    <script type="text/javascript" src="{{ asset('js/mustache-table.js?rand=' . \Carbon\Carbon::now()->format('YmdHis')) }}"></script>
@endsection
