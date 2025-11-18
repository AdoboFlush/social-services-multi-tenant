@extends('backend.assistance_queue.layout')

@section('tab-content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Queue Table
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-3 mb-2">
                            <select id="is-active-filter" class="form-select form-select-sm form-control">
                                <option value="1">Show All Active Queues</option>
                                <option value="0">Show All Inactive Queues</option>
                            </select>
                        </div>
                        <div class="col-md-12 table-responsive">
                            <table id="ajax-table" class="table table-bordered table-sm table-striped" style="white-space: nowrap;">
                                <thead>
                                    <tr>
                                        <th>Name</th>
										<th>Request Type</th>
                                        <th>Status</th>
                                        <th>Served By</th>
                                        <th>Queue #</th>
										<th>Date Created</th>
                                        <th>Date Updated</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Name</th>
										<th>Request Type</th>
                                        <th>Status</th>
                                        <th>Served By</th>
                                        <th>Queue #</th>
										<th>Date Created</th>
                                        <th>Date Updated</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('content-modal')

@endsection

@section('js-script')

<script>

$(function () {
    var table = $("#ajax-table").DataTable({
        'processing': true,
        'serverSide': true,
        'orderable': false,
        'searching': false,
        'ajax': {
            'url':'{{route("assistance-queue.get", [], false)}}',
            'data': function(d) {
                d.for_report = 1;
                d.is_active = $('#is-active-filter').val();
            },
            "dataSrc": function ( json ) {
                for ( var i=0, ien=json.data.length ; i<ien ; i++ ) {
                    let columnHtml = '<div class="btn-group">';
                    columnHtml += '</div>';
                    json.data[i]['id'] = columnHtml;
                }
                return json.data;
            }
        },
        'columns': [
            { data: 'name' },
			{ data: 'type' },
            { 
                data: 'status',
                render: function(data, type, row) {
                    let badgeClass = '';
                    switch(data) {
                        case 'on_queue':
                            badgeClass = 'badge bg-warning text-dark';
                            break;
                        case 'canceled':
                            badgeClass = 'badge bg-danger';
                            break;
                        case 'processing':
                            badgeClass = 'badge bg-info text-dark';
                            break;
                        case 'completed':
                            badgeClass = 'badge bg-success';
                            break;
                        default:
                            badgeClass = 'badge bg-secondary';
                    }
                    // Capitalize first letter for display
                    let label = data.charAt(0).toUpperCase() + data.slice(1).replace('_', ' ');
                    return `<span class="${badgeClass}">${label}</span>`;
                }
            },
            {
                data: 'served_by',
                render: function(data, type, row) {
                    return data ? data.full_name : '-';
                }, 
                orderable: false, 
                searchable: false 
            },
            { data: 'sequence_number' },
            { data: 'created_at' },
            { data: 'updated_at' },
        ]
    });

    // Apply the search
    $( '#ajax-table thead').on( 'keyup', ".column_search",function () {
        table
            .column( $(this).parent().index() )
            .search( this.value )
            .draw();
    });

    // Filter for is_active
    $('#is-active-filter').on('change', function() {
        table.ajax.reload();
    });
});
</script>

@endsection