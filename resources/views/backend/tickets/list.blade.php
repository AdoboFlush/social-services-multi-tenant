@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">Tickets</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item active">{{ ucwords($status) }} Tickets</li>
      </ol>
    </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
@endsection

@section('content')
<div class="container-fluid">
    <div class="card d-none d-md-block">
        <div class="card-body">
            <div class="filter row">
                <div class="col-6">
                    <div class="form-group row">
                        <label for="ticket_number" class="col-sm-4 col-form-label">Ticket Number</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" id="id">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ticket_status" class="col-sm-4 col-form-label">Ticket Status</label>
                        <div class="col-sm-7">
                            <select class="form-control" name="status" id="status">
                                @if($status == "all")
                                    <option value="">All</option>
                                    <option value="new">New</option>
                                    <option value="open">Open</option>
                                    <option value="pending">Pending</option>
                                    <option value="on-hold">On-hold</option>
                                    <option value="solved">Solved</option>
                                    <option value="re-opened">Re-opened</option>
                                @else
                                    <option value="{{ $status }}">{{ ucwords($status) }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="department" class="col-sm-4 col-form-label">Department</label>
                        <div class="col-sm-7">
                            <select class="form-control" name="department" id="department">
                                <option value="">All</option>
                                <option value="customer_support">Customer Support</option>
                                <option value="technical_support">Technical Support</option>
                                <option value="others">Others</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="date_created_from" class="col-sm-4 col-form-label">Date Created</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control datepicker" name="date_created_from" id="date_created_from" readOnly="true" placeholder="From">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="offset-sm-4 col-sm-7">
                            <input type="text" class="form-control datepicker" name="date_created_to" id="date_created_to" readOnly="true" placeholder="To">
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group row">
                        <label for="operator" class="col-sm-3 col-form-label">Operator</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" id="operator">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tag" class="col-sm-3 col-form-label">Ticket Tag</label>
                        <div class="col-sm-7">
                            <select class="form-control" name="tag" id="tag">
                                <option value="">All</option>
                                <option value="money_related">Money-related</option>
                                <option value="technical">Technical</option>
                                <option value="others">Others</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="country" class="col-sm-3 col-form-label">Country</label>
                        <div class="col-sm-7">
                            <select class="form-control" name="country" id="country">
                                <option value="">All</option>
                                {{ get_country_list() }}
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="priority" class="col-sm-3 col-form-label">Priority</label>
                        <div class="col-sm-7">
                            <select class="form-control" name="priority" id="priority">
                                <option value="">All</option>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="date_updated_from" class="col-sm-3 col-form-label">Date Updated</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control datepicker" name="date_updated_from" id="date_updated_from" readOnly="true" placeholder="From">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-7">
                            <input type="text" class="form-control datepicker" name="date_updated_to" id="date_updated_to" readOnly="true" placeholder="To">
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center border-bottom py-3 mb-3">
                <button type="button" class="btn btn-primary px-3 mx-1" id="filter">Filter</button>
                <button type="button" class="btn btn-secondary px-3 mx-1" id="reset">Reset</button>
            </div>
            <div class="table-container">
                <div class="badge badge-info my-2 loader">
                    <span class="text-light">Loading</span> <div class="spinner-border spinner-border-sm text-light" role="status"></div>
                </div>
                <div id="ajax-table"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@include("mustache.tickets-table")

@section('js-script')
    <script src="https://cdn.jsdelivr.net/npm/handlebars@latest/dist/handlebars.js"></script>
    <script type="text/javascript">
        var INIT_FILTER = true;
        var MUSTACHE_TABLE_API = '/admin/ticket/get/' + '{{ request()->segment(count(request()->segments())) }}';
        var MUSTACHE_TABLE_HEADERS =  {
            headers : [
                "Ticket ID",
                "Subject",
                "Customer",
                "Country",
                "Created Date",
                "Updated Date",
                "Dept.",
                "Operator",
                "Priority",
                "Ticket Tag",
                "Status"
            ]
        };
        var TRANSLATED_STATUS = {
            Applying: '{{ _lang("Applying") }}',
            Completed: '{{ _lang("Completed") }}',
            Rejected: '{{ _lang("Rejected") }}',
            Canceled: '{{ _lang("Canceled") }}'
        };
    </script>
    <script type="text/javascript" src="{{asset('js/mustache-table.js')}}"></script>
@endsection