@extends('backend.id_system.layout')

@section('card-header')
<h3 class="card-title">Member List</h3>
<span class="float-right"><a href="{{url()->current()."/create"}}" class="btn btn-primary btn-sm" data-title="{{ _lang('Add New') }}">Add New</a></span>
@endsection

@section('tab-content')
<div class="table-responsive">

    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">{{ _lang('Filter Field') }}</label>
                <select class="form-control select2" name="filter_field" id="filter_field">
                    <option value="">Please select a Field</option>
                    <option value="account_number">Member Number</option>
                    <option value="first_name">First Name</option>
                    <option value="middle_name">Middle Name</option>
                    <option value="last_name">Last Name</option>
                    <option value="brgy">Brgy</option>
                    <option value="alliance">Alliance</option>	
					@if(Auth::user()->user_access !== "encoder_voter")
					<option value="affiliation">Affiliation</option>
					@endif
                    <option value="position">Position</option>
                    <option value="party_list">Party List</option>
                    <option value="sectoral">Sectoral</option>
                    <option value="beneficiary">Beneficiary</option>
                    <option value="religion">Religion</option>
                    <option value="civil_status">Civil Status</option>
                    <option value="contact_number">Contact Number</option>
                </select>
                <span class="err-message">{{ _lang('filter_field is required') }}</span>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">{{ _lang('Enter what to search') }}</label>
                <input type="text" class="form-control" name="filter_search" id="filter_search" placeholder="Search" />
                <span class="err-message">{{ _lang('Search Value is required') }}</span>
            </div>
        </div>
		<!--
		<div class="col-md-3">
            <div class="form-group">
                <label class="control-label">{{ _lang('Include') }}</label>
                <select class="form-control select2" name="include" id="include">
                    <option value="">All</option>
                    <option value="account_number">Has No IDs</option>
                </select>
                <span class="err-message">{{ _lang('filter_field is required') }}</span>
            </div>
        </div>
		-->
        <div class="col-md-1">
            <div class="form-group">
                <label class="control-label">--</label>
                <button class="btn btn-primary form-control" id="btn-search" type="button"><i class="fa fa-search mr-2"></i>Search</button>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">--</label>
                <a href="#" class="btn btn-warning form-control ajax-modal" data-title="{{ _lang('Create ID Requests') }}" data-href="{{url('id_system/members/import_to_request')}}">
                    <i class="fa fa-plus mr-2"></i>
                    Create IDs
                </a>
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group">
                <label class="control-label">--</label>
                <button class="btn btn-success form-control" id="btn-export" type="button"><i class="fa fa-table mr-2"></i>Export</button>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table id="ajax-table" class="table table-sm table-bordered table-striped" style="white-space: nowrap;">
            <thead>
                <tr>
                    <th><input type="checkbox" class="select-all row-checkbox ml-2 mr-3 mt-2"> Actions</th>
                    <th>Full name</th>
                    <th>Member Number</th>
                    <th>Birth date</th>
                    <th>Gender</th>
                    <th>Precinct</th>
                    <th>Address</th>
                    <th>Brgy</th>
                    <th>Alliance</th>
                    <th>Affiliation</th>
                    <th>Sectoral</th>
                    <th>Position</th>
                    <th>Party List</th>
                    <th>Beneficiary</th>
                    <th>Religion</th>
                    <th>Civil Status</th>
                    <th>Contact Number</th>
                    <th>Code</th>
                    <th>Remarks</th>
                    <th>Has Member Access</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>Actions</th>
                    <th>Full name</th>
                    <th>Member Number</th>
                    <th>Birth date</th>
                    <th>Gender</th>
                    <th>Precinct</th>
                    <th>Address</th>
                    <th>Brgy</th>
                    <th>Alliance</th>
                    <th>Affiliation</th>
                    <th>Sectoral</th>
                    <th>Position</th>
                    <th>Party List</th>
                    <th>Beneficiary</th>
                    <th>Religion</th>
                    <th>Civil Status</th>
                    <th>Contact Number</th>
                    <th>Code</th>
                    <th>Remarks</th>
                    <th>Has Member Access</th>
                </tr>
            </tfoot>
            <tbody>
            </tbody>
            
        </table>
    </div>
    <div class="form-group mt-3">
        <label class="control-label mr-3">All Checked Items : </label>
        <button class="btn btn-danger btn-multi-delete">Delete</button>
    </div>
</div>
@endsection

@section('js-script')

<script>
$(function () {

    $('#btn-search').on('click', function(e){
        let field_name = $('#filter_field').val();
        let search_value = $('#filter_search').val();
        if(field_name.length > 0){
            $('#ajax-table').DataTable().ajax.url("?filter["+field_name+"]=" + search_value).load();
        }else{
            $('#ajax-table').DataTable().ajax.url("").load();
        }
    });

    $('#import_csv_form').on('submit', function(){
        $('#import_csv').attr('disabled', 'disabled');
        $('.import-loader').removeClass('d-none');
    });

    var table = $("#ajax-table").DataTable({
        'orderCellsTop': true,
        'fixedHeader': true,
        'responsive': true, 
        "lengthChange": false, 
        "autoWidth": false,
        'searching': false,
        @if(Auth::user()->user_access !== "encoder_voter")
        'buttons': ["copy", "csv", 'excel', "pdf", "print", "pageLength"],
		@endif
        'lengthMenu': [
            [ 10, 25, 50 ],
            [ '10 rows', '25 rows', '50 rows' ]
        ],
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'aoColumnDefs': [
            { 'bSortable': false, 'aTargets': [0] }
        ],
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
            'url':'{{url("id_system/members")}}',
            'data':{
                '_token' : '{{csrf_token()}}'
            },
            "dataSrc": function ( json ) {
                for ( var i=0, ien=json.data.length ; i<ien ; i++ ) {
                    let columnHtml = '<div class="btn-group">';
                    columnHtml += '<input type="checkbox" data-id="'+json.data[i]['id']+'" class="row-checkbox ml-2 mr-3 mt-2">';
                    columnHtml += '<a class="btn btn-success btn-sm ajax-modal" href="#" data-title="{{ _lang('Show Information') }}" data-href="{{url('id_system/members/show')}}/'+json.data[i]['id']+'">View</a>';
                    columnHtml += '<a class="btn btn-primary btn-sm ml-1" data-title="{{ _lang('Edit') }}" href="{{url('id_system/members/edit')}}/'+json.data[i]['id']+'">Edit</a>';
                    columnHtml += '<a class="btn btn-warning btn-sm ml-1" target="_blank" href="{{url('id_system/requests/create')}}/'+json.data[i]['id']+'">{{ _lang('Request') }}</a>';
                    columnHtml += '</div>';
                    json.data[i]['id'] = columnHtml;
                }
                return json.data;
            }
        },
        'columns': [
            { data: 'id' },
            { data: 'full_name', orderable: false, searchable: false },
            {
                data: 'account_number',
                render: function(data, type, row) {
                    return data;
                }, 
                orderable: false, 
                searchable: false 
            },
            { data: 'birth_date', orderable: false, searchable: false },
            { data: 'gender', orderable: false, searchable: false },
            { data: 'precinct', orderable: false, searchable: false },
            { data: 'address', orderable: false, searchable: false },
            { data: 'brgy', orderable: false, searchable: false },
            {
                data: 'voter',
                render: function(data, type, row) {
                    return row.voter ? row.voter.alliance : 'N/A';
                }, 
                orderable: false, 
                searchable: false 
            },
            {
                data: 'voter',
                render: function(data, type, row) {
                    return row.voter ? row.voter.affiliation : 'N/A';
                }, 
                orderable: false, 
                searchable: false 
            },
            {
                data: 'voter',
                render: function(data, type, row) {
                    return row.voter ? row.voter.sectoral : 'N/A';
                }, 
                orderable: false, 
                searchable: false 
            },
            {
                data: 'voter',
                render: function(data, type, row) {
                    return row.voter ? row.voter.position : 'N/A';
                }, 
                orderable: false, 
                searchable: false 
            },
            {
                data: 'voter',
                render: function(data, type, row) {
                    return row.voter ? row.voter.party_list : 'N/A';
                }, 
                orderable: false, 
                searchable: false 
            },
            {
                data: 'voter',
                render: function(data, type, row) {
                    return row.voter ? row.voter.beneficiary : 'N/A';
                }, 
                orderable: false, 
                searchable: false 
            },
            {
                data: 'religion',
                orderable: false, 
                searchable: false 
            },
            {
                data: 'civil_status',
                orderable: false, 
                searchable: false 
            },
            {
                data: 'contact_number',
                orderable: false, 
                searchable: false 
            },
            { data: 'code', orderable: false, searchable: false },
            { data: 'remarks', orderable: false, searchable: false },
            { 
                data: 'has_member_access',
                render: function(data, type, row) {
                    return data ? 'YES' : 'NO';
                }, 
                orderable: false, searchable: false 
            },
        ]
    });

    $('.btn-multi-delete').on('click', function(e){
        var data_arr = [];
        $('.row-checkbox:checked').each( function () {
            data_arr.push($(this).data('id'));
        });
        swal({
            title: "Are you sure?",
            text: "- All members with existing ID requests cannot be deleted. \n - Once deleted, you will not be able to recover the records",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url : "{{url('id_system/members/delete')}}",
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
                        $('#ajax-table').DataTable().ajax.reload();
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

    $('#btn-export').on('click', function(e){
        let field_name = $('#filter_field').val();
        let search_value = $('#filter_search').val();
        let query_params = '';
        if(field_name.length > 0){
            query_params = "filter["+field_name+"]=" + search_value
        }
        let ajax_url = "{{url("/id_system/members/export")}}" + '?' + query_params;
        exportCSV(ajax_url, "id-system-members-{{date('Y-m-d H:i:s')}}.csv");
    });

    $('.select-all').on('click', function(e){
        var checked = $(this).is(":checked");
        $('.row-checkbox').prop("checked", checked).trigger('change');
    });

});

</script>
@endsection