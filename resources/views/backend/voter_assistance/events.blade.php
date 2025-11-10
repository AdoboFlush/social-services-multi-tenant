@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">Assistance Events</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item">Events</li>
      <li class="breadcrumb-item active">Event Details</li>
      </ol>
    </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
@endsection

@section('content')

<div class="container-fluid mb-1 mt-1">
    <div class="card card-info" style="width: 100%; height: auto;">
        <div class="card-body">
            <div class="row">
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Event Name') }}</label>
                                <input type="text" class="form-control" name="filter_name" id="filter_name" placeholder="Search by name" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Description') }}</label>
                                <input type="text" class="form-control" name="filter_description" id="filter_description" placeholder="Search by description" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">--</label>
                                <button class="btn btn-primary btn-sm form-control" id="btn-search" type="button"><i class="fa fa-search mr-2"></i>Search</button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">--</label>
                                <button class="btn btn-secondary btn-sm form-control" id="btn-reset" type="button"><i class="fa fa-undo mr-2"></i>Reset</button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">--</label>
                                <button class="btn btn-success btn-sm ml-1 form-control ajax-modal" data-title="Create Assistance Event" data-href="{{route('voter_assistance.events.create', [], false)}}"><i class="fa fa-edit mr-2"></i>Create New Event</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3"></div>
                <div class="hr"></div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="ajax-table" class="table table-bordered table-hover table-sm" style="white-space: nowrap;">
                            <thead>
                                <tr>
                                    <th>Actions</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th># of Claims</th>
                                    <th>Starts At</th>
                                    <th>Ends At</th>
                                    <th>Assistance Type</th>
                                    <th>Is Active</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Actions</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th># of Claims</th>
                                    <th>Starts At</th>
                                    <th>Ends At</th>
                                    <th>Assistance Type</th>
                                    <th>Is Active</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                @can('admin_view')
                <div class="form-group mt-3">
                    <label class="control-label mr-3">All Checked Items : </label>
                    <button class="btn btn-danger btn-multi-delete">Delete</button>
                </div>
                @endcan
            </div>
        </div>
    </div>
</div>

@endsection

@section('js-script')

<script>
    $(function() {
        $('#btn-search').on('click', function(e) {
            let name = $('#filter_name').val();
            let description = $('#filter_description').val();
            let search_query_params = [];

            if (name) {
                search_query_params.push("name=" + name);
            }
            if (description) {
                search_query_params.push("description=" + description);
            }

            let query = search_query_params.join("&");
            $('#ajax-table').DataTable().ajax.url("?" + query).load();
        });

        $('#btn-reset').on('click', function(e) {
            $('#filter_name').val("");
            $('#filter_description').val("");
            $('#ajax-table').DataTable().ajax.url("").load();
        });

        @can('admin_view')
        $('.btn-multi-delete').on('click', function(e){
            var data_arr = [];
            $('.row-checkbox:checked').each( function () {
                data_arr.push($(this).data('id'));
            });
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover the records",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url : "{{url('voter_assistance/events/multi-delete')}}",
                        type: "post",
                        data : {
                            'selected_ids' : data_arr,
                            '_token' : '{{csrf_token()}}'
                        },
                        success: function(data, textStatus, jqXHR)
                        {
                            swal("Records has been deleted!", {
                                icon: "success",
                            }).then(() => {
                                $('#ajax-table').DataTable().ajax.reload(null, false); // Refresh datatable, keep current page
                            });
                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            swal("Deletion failed!", {
                                icon: "danger",
                            });
                        }
                    });
                }
            });
        });
        @endcan

        var table = $("#ajax-table").DataTable({
            'searching': false,
            'orderCellsTop': true,
            'fixedHeader': true,
            'responsive': true,
            "lengthChange": false,
            "autoWidth": false,
            'buttons': ["pageLength"],
            'lengthMenu': [
                [15, 50],
                ['15 rows', '50 rows']
            ],
            'processing': true,
            'serverSide': true,
            'serverMethod': 'get',
            'ajax': {
                'url': '{{url("/voter_assistance/events")}}',
                'data': {
                    '_token': '{{csrf_token()}}'
                },
                "dataSrc": function(json) {
                    for ( var i=0, ien=json.data.length ; i<ien ; i++ ) {
                        let columnHtml = '<div class="btn-group">';
                        columnHtml += '<input type="checkbox" data-id="' + json.data[i]['id'] + '" class="row-checkbox ml-2 mr-3 mt-2">';
                        columnHtml += '<a class="btn btn-success btn-sm" href="{{url('voter_assistance/events/show')}}/'+json.data[i]['id']+'">View Details</a>';
                        columnHtml += '<a class="btn btn-primary btn-sm ml-1 ajax-modal" data-title="{{ _lang('Edit Assistance Event') }}" href="{{url('voter_assistance/events/edit')}}/'+json.data[i]['id']+'">Edit</a>';
                        columnHtml += '</div>';

                        json.data[i]['id'] = columnHtml;
                        json.data[i]['is_active'] = json.data[i]['is_active'] ? "YES" : "NO";
                        json.data[i]['amount'] = "PHP " + json.data[i]['amount'];
                    }

                    return json.data;
                }
            },
            'columns': [
                { data: 'id' },
                { data: 'name' },
                { data: 'description' },
                { data: 'amount' },
                { data: 'assistances_count' },
                { data: 'starts_at' },
                { data: 'ends_at' },
                { data: 'assistance_type' },
                { data: 'is_active' }
            ]
        });
    });
</script>
@endsection
