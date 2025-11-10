@extends('backend.poll.layout')

@section('card-header')
<h3 class="card-title">Candidate List</h3>
<span class="float-right">
  <a href="#" class="btn btn-primary btn-sm ajax-modal" data-title="Add New Candidate" data-href="{{ route('poll.candidates.create', [], false) }}" data-fullscreen="true">Add New</a>
</span>
@endsection

@section('tab-content')
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="table-responsive">
        <table id="ajax-table-candidate" class="table table-sm table-bordered table-striped">
          <thead>
            <tr>
              <th>Actions</th>
              <th>Candidate</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>Actions</th>
              <th>Candidate</th>
            </tr>
          </tfoot>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js-script')
<script>
  $(function() {

    $('#ajax-table-candidate thead tr:eq(1) th').each(function() {
      var title = $(this).text();
      if (title != 'Actions') {
        $(this).html('<input type="text" placeholder="Search ' + title + '" class="column_search" />');
      } else {
        $(this).html('');
      }
    });

    $('.nav-tabs .nav-link').on('click', function(e) {
      let search_type = $(this).data('tag-type');
      $('#ajax-table-candidate').DataTable().ajax.url("?search_type=" + search_type).load();
    });

    var table = $("#ajax-table-candidate").DataTable({
      'orderCellsTop': true,
      'searching': false,
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
        'url': '{{route("poll.candidates.index", [], false)}}',
        'data': {
          '_token': '{{csrf_token()}}'
        },
        "dataSrc": function(json) {
          for (var i = 0, ien = json.data.length; i < ien; i++) {
            let columnHtml = '<div class="btn-group">';
            columnHtml += '<input type="checkbox" data-id="' + json.data[i]['id'] + '" class="row-checkbox ml-2 mr-3 mt-2">';
            // columnHtml += '<a class="btn btn-success btn-sm" href="{{ url("poll/candidates/show") }}/'+ json.data[i]['id'] +'">View</a>';
            columnHtml += '<a class="btn btn-primary btn-sm ml-1 ajax-modal" href="#" data-title="{{ _lang('Edit') }}" data-href="{{ url("poll/candidates/edit") }}/'+ json.data[i]['id'] +'">Edit</a>';
            columnHtml += '</div>';
            json.data[i]['id'] = columnHtml;
          }
          return json.data;
        }
      },
      'columns': [{
          data: 'id'
        },
        {
          data: 'name',
          orderable: false,
          searchable: false,
          render: function(data, type, row) {
            let nameHtml = '<span>';
            if (row.image) {
              nameHtml += '<img src="/uploads/'+ row.image + '" style="width:40px;height:40px;"/>';
            } else {
              nameHtml += '<img src="/images/avatar-classic.png" style="width:40px;height:40px;"/>';
            }
            nameHtml += '<strong class="ml-2">' + row.name + '</strong>';
            return nameHtml + "</span>";
          }
        }
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