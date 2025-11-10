@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">Tags</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item active">Tags</li>
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
                  <h3 class="card-title">Tags List</h3>
                    @can('tag_create')
                    <span class="float-right"><a href="#" class="btn btn-primary btn-sm ajax-modal" data-title="{{ _lang('Add New Tag') }}" data-href="{{url('tag/create')}}" data-fullscreen="true">Add New</a></span>
                    @endcan
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    
                    <ul class="nav nav-tabs mb-3">
						<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#all" data-tag-type="">{{ _lang('All') }}</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#alliance" data-tag-type="alliance">{{ _lang('Alliance') }}</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#alliance_1" data-tag-type="alliance_1">{{ _lang('Sub Alliance') }}</a></li> 
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#affiliation" data-tag-type="affiliation">{{ _lang('Affiliation') }}</a></li>			
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#sectoral" data-tag-type="sectoral">{{ _lang(' Sectoral') }}</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#religion" data-tag-type="religion">{{ _lang('Religion') }}</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#barangay" data-tag-type="brgy">{{ _lang('Barangay') }}</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#purpose" data-tag-type="purpose">{{ _lang('Purpose') }}</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#civil_status" data-tag-type="civil_status">{{ _lang('Civil Status') }}</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#beneficiaries" data-tag-type="beneficiaries">{{ _lang('Beneficiaries') }}</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#organization" data-tag-type="organization">{{ _lang('Organization') }}</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#party_list" data-tag-type="party_list">{{ _lang('Party List') }}</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#position" data-tag-type="position">{{ _lang('Position') }}</a></li>
					</ul>

                    <div class="tab-content">
                        <div class="table-responsive">
                            <table id="ajax-table" class="table table-sm table-bordered table-striped">
                                <thead>
                                    <tr>
                                    <th>Actions</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Parent</th>
                                    <th>Custom Field</th>
                                    </tr>
                                    <tr>
                                    <th>Actions</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Parent</th>
                                    <th>Custom Field</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                    <th>Actions</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Parent</th>
                                    <th>Custom Field</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                </tbody>
                                
                            </table>
                        </div>
                        @can('tag_delete')
                        <div class="form-group mt-3">
                            <label class="control-label mr-3">All Checked Items : </label>
                            <button class="btn btn-danger btn-multi-delete">Delete</button>
                        </div>
                        @endcan
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
                    url : "{{url('tag/delete')}}",
                    type: "post",
                    data : {
                        'selected_ids' : data_arr,
                        '_token' : '{{csrf_token()}}'
                    },
                    success: function(data, textStatus, jqXHR)
                    {
                        swal("Records has been deleted!", {
                            icon: "success",
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

    $('#ajax-table thead tr:eq(1) th').each( function () {
        var title = $(this).text();
        if(title != 'Actions'){
            $(this).html( '<input type="text" placeholder="Search '+title+'" class="column_search" />' );
        }else{
            $(this).html('');
        }
    } );

    $('.nav-tabs .nav-link').on('click', function(e){
        let search_type = $(this).data('tag-type');
        $('#ajax-table').DataTable().ajax.url("?search_type=" + search_type).load();
    });

    var table = $("#ajax-table").DataTable({
        'orderCellsTop': true,
        'fixedHeader': true,
        'responsive': true, 
        "lengthChange": false, 
        "autoWidth": false,
        'buttons': ["copy", "csv", "excel", "pdf", "print", "pageLength"],
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
            'url':'{{url("tags")}}',
            'data':{
                '_token' : '{{csrf_token()}}'
            },
            "dataSrc": function ( json ) {
                for ( var i=0, ien=json.data.length ; i<ien ; i++ ) {
                    let columnHtml = '<div class="btn-group">';
                    columnHtml += '<input type="checkbox" data-id="'+json.data[i]['id']+'" class="row-checkbox ml-2 mr-3 mt-2">';
                    columnHtml += '<a class="btn btn-success btn-sm ajax-modal" href="#" data-title="{{ _lang('Show Tag Information') }}" data-href="{{url('tag/show')}}/'+json.data[i]['id']+'">View</a>';
                    @can('tag_update')
                    columnHtml += '<a class="btn btn-primary btn-sm ml-1 ajax-modal" href="#" data-title="{{ _lang('Edit Tag') }}" data-href="{{url('tag/edit')}}/'+json.data[i]['id']+'">Edit</a>';
                    @endcan
                    columnHtml += '</div>';
                    json.data[i]['id'] = columnHtml;
                    json.data[i]['name'] = json.data[i]['name'].length > 0 ? json.data[i]['name'] : 'N/A';
                    json.data[i]['type'] = json.data[i]['type'].length > 0 ? json.data[i]['type'] : 'N/A';
                    json.data[i]['parent_name'] = json.data[i]['parent_name'].length > 0 ? json.data[i]['parent_name'] : 'N/A';
                }
                return json.data;
            }
        },
        'columns': [
            { data: 'id' },
            { data: 'name' },
            { data: 'type' },
            { data: 'parent_name' },
            { data: 'custom_field' },
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