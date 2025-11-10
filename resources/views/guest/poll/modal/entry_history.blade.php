<div class="card card-warning" style="width: 100%; height: auto;">
    <div class="card-header">
        <h3 class="card-title">{{ _lang('Entry History Logs') }}</h3>
    </div>
    <div class="card-body">
        <div class="container-fluid mb-1 mt-1">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="activity-log-table" class="table table-sm table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Action</th>
                                    <th>Candidate</th>
                                    <th>Description</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>Action</th>
                                    <th>Candidate</th>
                                    <th>Description</th>
                                    <th>Created At</th>
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

<script>
    $(function() {
        $('#activity-log-table').DataTable({
            'responsive': true,
            'autoWidth': false,
            'lengthChange': false,
            'searching': false,
            'ordering': false,
            'processing': true,
            'serverSide': true,
            'ajax': {
                'url': '{{ route("poll.guest.watcher.activity_logs.get", [], false) }}',
                'type': 'GET'
            },
            'columns': [
                { data: 'id', visible: false },
                { data: 'action' },
                { data: 'candidate' },
                { data: 'description' },
                { data: 'created_at' }
            ]
        });
    });
</script>
