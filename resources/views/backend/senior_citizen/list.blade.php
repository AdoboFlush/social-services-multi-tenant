@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">Senior Citizens (Voters)</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item active">Voters</li>
      </ol>
    </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Senior Citizen Voters List</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="ajax-table" class="table table-sm table-bordered table-striped">
                            <thead>
                                <tr>
                                <th>Actions</th>
                                <th>Full name</th>
                                <th>Birth date</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Precinct</th>
                                <th>Address</th>
                                <th>Brgy</th>
                                <th>Alliance</th>
                                <th>Affiliation</th>
                                <th>Religion</th>
                                <th>Civil Status</th>
                                <th>Contact Number</th>
                                <th>Remarks</th>
                                </tr>
                                <tr>
                                <th>Actions</th>
                                <th>Full name</th>
                                <th>Birth date</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Precinct</th>
                                <th>Address</th>
                                <th>Brgy</th>
                                <th>Alliance</th>
                                <th>Affiliation</th>
                                <th>Religion</th>
                                <th>Civil Status</th>
                                <th>Contact Number</th>
                                <th>Remarks</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                <th>Actions</th>
                                <th>Full name</th>
                                <th>Birth date</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Precinct</th>
                                <th>Address</th>
                                <th>Brgy</th>
                                <th>Alliance</th>
                                <th>Affiliation</th>
                                <th>Religion</th>
                                <th>Civil Status</th>
                                <th>Contact Number</th>
                                <th>Remarks</th>
                                </tr>
                            </tfoot>
                            <tbody>
                            </tbody>
                           
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
        </div>
    </div>
</div>
@endsection

@section('js-script')

<script>
$(function () {

    $('#ajax-table thead tr:eq(1) th').each( function () {
        var title = $(this).text();
        if(title != 'Actions' @if(Auth::user()->user_access == "encoder_voter") && title != 'Affiliation' @endif ){
            $(this).html( '<input type="text" placeholder="Search '+title+'" class="column_search" />' );
        } else{
            $(this).html('');
        }
    } );

    var table = $("#ajax-table").DataTable({
        'orderCellsTop': true,
        'fixedHeader': true,
        'responsive': true, 
        "lengthChange": false, 
        "autoWidth": false,
        "columnDefs": [ 
            {"targets": 0,"orderable": false},
            {"targets": 3,"orderable": false},
        ],
        @if(Auth::user()->user_access !== "encoder_voter")
        'buttons': ["copy", "csv", "excel", "pdf", "print", "pageLength"],
		@endif
        'lengthMenu': [
            [ 10, 25, 50],
            [ '10 rows', '25 rows', '50 rows']
        ],
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'initComplete': function(settings, json) {
            table.buttons().container().appendTo('#ajax-table_wrapper .col-md-6:eq(0)');
            this.api()
                .columns()
                .every(function () {
                    var that = this;
                    $('input', this.footer()).on('keyup change clear', function () {
                        if (that.search() !== this.value) {
                            that.search(this.value).draw();
                        }
                    });
                });
        },
        'ajax': {
            'url':'{{url("senior_citizen_voters")}}',
            'data':{
                '_token' : '{{csrf_token()}}'
            },
            "dataSrc": function ( json ) {
                for ( var i=0, ien=json.data.length ; i<ien ; i++ ) {
                    let columnHtml = '<div class="btn-group">';
                    columnHtml += '<a class="btn btn-success btn-sm ajax-modal" href="#" data-title="{{ _lang('Show Voter Information') }}" data-href="{{url('voter/show')}}/'+json.data[i]['id']+'">View</a>';
                    @can('social_service_update')
                    columnHtml += '<a class="btn btn-primary btn-sm ml-1 ajax-modal" href="#" data-title="{{ _lang('Edit Voter') }}" data-href="{{url('voter/edit')}}/'+json.data[i]['id']+'">Edit</a>';
                    @endcan
                    columnHtml += '<a class="btn btn-warning btn-sm ml-1" target="_blank" href="{{url('social_services/create')}}/'+json.data[i]['id']+'">{{ _lang('Request') }}</a>';
                    columnHtml += '</div>';
                    json.data[i]['id'] = columnHtml;
                    json.data[i]['brgy'] = json.data[i]['brgy'].length > 0 ? json.data[i]['brgy'] : 'N/A';
                    json.data[i]['alliance'] = json.data[i]['alliance'] && json.data[i]['alliance'].length > 0 ? json.data[i]['alliance'] : 'N/A';
                    json.data[i]['affiliation'] = json.data[i]['affiliation'] && json.data[i]['affiliation'].length > 0 ? json.data[i]['affiliation'] : 'N/A';
                    json.data[i]['religion'] = json.data[i]['religion'] && json.data[i]['religion'].length > 0 ? json.data[i]['religion'] : 'N/A';
                    json.data[i]['civil_status'] = json.data[i]['civil_status'] && json.data[i]['civil_status'].length > 0 ? json.data[i]['civil_status'] : 'N/A';
                    json.data[i]['contact_number'] = json.data[i]['contact_number'] && json.data[i]['contact_number'].length > 0 ? json.data[i]['contact_number'] : 'N/A';
                    json.data[i]['remarks'] = json.data[i]['remarks'] && json.data[i]['remarks'].length > 0 ? json.data[i]['remarks'] : 'N/A';
                }
                return json.data;
            }
        },
        'columns': [
            { data: 'id' },
            { data: 'full_name' },
            { data: 'birth_date' },
            { data: 'age' },
            { data: 'gender' },
            { data: 'precinct' },
            { data: 'address' },
            { data: 'brgy' },
            { data: 'alliance' },
            { data: 'affiliation' },
            { data: 'religion' },
            { data: 'civil_status' },
            { data: 'contact_number' },
            { data: 'remarks' },
        ]
    });

    // Apply the search
    $( '#ajax-table thead').on( 'keyup', ".column_search",function () {
        table
            .column( $(this).parent().index() )
            .search( this.value )
            .draw();
    });

});

</script>
@endsection