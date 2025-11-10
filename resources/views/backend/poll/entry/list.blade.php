@extends('backend.poll.layout')

@section('card-header')
<h3 class="card-title">Entry List</h3>
<!-- <span class="float-right">
  <a href="#" class="btn btn-primary btn-sm ajax-modal" data-title="Add New Entry" data-href="{{ route('poll.entries.create', [], false) }}" data-fullscreen="true">Add New Entry</a>
</span> -->
@endsection

@section('tab-content')
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="table-responsive">
        <table id="ajax-table" class="table table-sm table-bordered table-striped">
          <thead>
            <tr>
              <th>
                <input type="checkbox" id="select-all">
              </th>
              <th>Candidate</th>
              <th>Election</th>
              <th>Watcher</th>
              <th>Area</th>
              <th>Barangay</th>
              <th>Precinct</th>
              <th>Votes</th>
              <th>Date Created</th>
              <th>Date Updated</th>
              <th>Remarks</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>Actions</th>
              <th>Candidate</th>
              <th>Election</th>
              <th>Watcher</th>
              <th>Area</th>
              <th>Barangay</th>
              <th>Precinct</th>
              <th>Votes</th>
              <th>Date Created</th>
              <th>Date Updated</th>
              <th>Remarks</th>
            </tr>
          </tfoot>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
    <div class="col-md-12">
      <div class="card-footer clearfix">
        <div class="float-right">
          <button type="button" class="btn btn-danger btn-sm" id="delete-selected">Delete Selected</button>
        </div>
      </div>
  </div>
</div>
@endsection

@section('js-script')
<script>
  $(function() {
    var table = $("#ajax-table").DataTable({
      searchable: false,
      processing: true,
      serverSide: true,
      ajax: {
        url: '{{ route("poll.entries.index", [], false) }}',
        type: 'GET',
        dataSrc: function(json) {
          for (var i = 0, ien = json.data.length; i < ien; i++) {
            let columnHtml = '<div class="btn-group">';
            columnHtml += '<input type="checkbox" data-id="' + json.data[i]['id'] + '" class="row-checkbox ml-2 mr-3 mt-2">';
            json.data[i]['id'] = columnHtml;
          }
          return json.data;
        }
      },
      columns: [
        { data: 'id' },
        { data: 'candidate_name', orderable: false },
        { data: 'election_name', orderable: false },
        { data: 'watcher_name', orderable: false },
        { data: 'area', orderable: false },
        { data: 'brgy', orderable: false },
        { data: 'precinct', orderable: false },
        { data: 'votes' },
        { data: 'created_at' },
        { data: 'updated_at' },
        { data: 'remarks' },
      ],
    });

    // Handle "Select All" checkbox
    $('#select-all').on('click', function() {
      $('.row-checkbox').prop('checked', this.checked);
    });

    // Handle bulk delete
    $('#delete-selected').on('click', function() {
      let selectedIds = [];
      $('.row-checkbox:checked').each(function() {
        selectedIds.push($(this).data('id'));
      });

      if (selectedIds.length > 0) {

        swal({
            title: `Are you sure you want to delete the selected entries?`,
            text: "Once deleted, you will not be able to recover these entries!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                  url: '{{ route("poll.entries.bulk_delete", [], false) }}',
                  type: 'POST',
                  data: {
                    ids: selectedIds,
                    _token: '{{ csrf_token() }}'
                  },
                  success: function(response) {
                    swal("Selected entries has been deleted.", {
                        icon: "success",
                    });
                    table.ajax.reload();
                  },
                  error: function(xhr) {
                    swal(data?.message, {
                        icon: "error",
                    });
                  }
                });
            }
        });
      } else {
        swal('Please select at least one entry to delete.', {
            icon: "error",
        });
      }
    });
  });
</script>
@endsection