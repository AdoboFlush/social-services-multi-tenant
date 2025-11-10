@extends('layouts.app')

@section('content')
<div class="sb-page-header pb-2 sb-page-header-dark bg-gradient-primary-to-secondary">
    <div class="container-fluid">
        <div class="sb-page-header-content py-3">
            <h1 class="sb-page-header-title">
                <div class="sb-page-header-icon"><i data-feather="bell"></i></div>
                <span>{{ _lang('Advertisements') }}</span>
            </h1>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body ">
                        <h4 class="card-title"><span class="panel-title">{{ _lang('Advertisements List') }}</span></h4>
                        <div class="row mb-4">
                            <div class="col-12">
                                <a href="{{ route('advertisements.create') }}"
                                    class="btn btn-outline-info px-3 mr-2">{{ _lang('Create') }}</a>
                            </div>
                        </div>
                        <div class="table-container">
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
    </div>
</div>
@endsection

@include("mustache.all-advertisement")

@section('js-script')
<script>
    let MUSTACHE_TABLE_API = '/admin/advertisements/list';
    let MUSTACHE_TABLE_HEADERS = {
        headers: [
            "Language",
            "Order",
            "Banner",
            "Title",
            "Link",
            "Owner",
            "Sequence",
        ]
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/handlebars@latest/dist/handlebars.js"></script>
<script type="text/javascript" src="{{asset('js/mustache-table.js')}}"></script>
@endsection