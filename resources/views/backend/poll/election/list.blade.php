@extends('backend.poll.layout')

@section('card-header')
<h3 class="card-title">Election List</h3>
<span class="float-right">
  <a href="#" class="btn btn-primary btn-sm ajax-modal" data-title="Add New Election" data-href="{{ route('poll.elections.create', [], false) }}" data-fullscreen="true">Add New</a>
</span>
@endsection

@section('tab-content')
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="table-responsive">
        <table id="ajax-table" class="table table-sm table-bordered table-striped">
          <thead>
            <tr>
              <th>Actions</th>
              <th>Title</th>
              <th>Date</th>
              <th>Is Active</th>
              <th>Type</th>
              <th>Remarks</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>Actions</th>
              <th>Title</th>
              <th>Date</th>
              <th>Is Active</th>
              <th>Type</th>
              <th>Remarks</th>
            </tr>
          </tfoot>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
    <!-- /.card -->
  </div>
</div>
@endsection

@section('js-script')
<script>
  $(function() {
    $('#ajax-table thead tr:eq(1) th').each(function() {
      var title = $(this).text();
      if (title != 'Actions') {
        $(this).html('<input type="text" placeholder="Search ' + title + '" class="column_search" />');
      } else {
        $(this).html('');
      }
    });

    $('.nav-tabs .nav-link').on('click', function(e) {
      let search_type = $(this).data('tag-type');
      $('#ajax-table').DataTable().ajax.url("?search_type=" + search_type).load();
    });

    var table = $("#ajax-table").DataTable({
      'orderCellsTop': true,
      'fixedHeader': true,
      'responsive': true,
      "lengthChange": false,
      "autoWidth": false,
      ordering: false,
      'buttons': ["copy", "csv", "excel", "pdf", "print", "pageLength"],
      'lengthMenu': [
        [10, 25, 50],
        ['10 rows', '25 rows', '50 rows']
      ],
      'processing': true,
      'serverSide': true,
      'serverMethod': 'get',
      'initComplete': function(settings, json) {
        table.buttons().container().appendTo('#ajax-table_wrapper .col-md-6:eq(0)');
        this.api()
          .columns()
          .every(function() {
            var that = this;
            $('input', this.footer()).on('keyup change clear', function() {
              if (that.search() !== this.value) {
                that.search(this.value).draw();
              }
            });
          });
      },
      'ajax': {
        'url': '{{route("poll.elections.index", [], false)}}',
        "dataSrc": function(json) {
          for (var i = 0, ien = json.data.length; i < ien; i++) {

            let columnHtml = '<div class="btn-group">';
            columnHtml += '<input type="checkbox" data-id="' + json.data[i]['id'] + '" class="row-checkbox ml-2 mr-3 mt-2">';
            columnHtml += '<a class="btn btn-success btn-sm" href="{{ url("poll/elections/show") }}/' + json.data[i]['id'] + '">View</a>';
            columnHtml += '<a class="btn btn-primary btn-sm ml-1 ajax-modal" href="#" data-title="{{ _lang('Edit ') }}" data-href="{{ url("poll/elections/edit") }}/' + json.data[i]['id'] + '">Edit</a>';
            columnHtml += '</div>';
            json.data[i]['id'] = columnHtml;

            json.data[i]['is_active'] = json.data[i]['is_active'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Is Active</span>';

          }
          return json.data;
        }
      },
      'columns': [{
          data: 'id'
        },
        {
          data: 'name'
        },
        {
          data: 'election_date'
        },
        {
          data: 'is_active'
        },
        {
          data: 'type'
        },
        {
          data: 'remarks'
        },
      ]
    });

    // Apply the search
    $('#ajax-table thead').on('keyup', ".column_search", function() {
      table
        .column($(this).parent().index())
        .search(this.value)
        .draw();
    });
  });
</script>
@endsection