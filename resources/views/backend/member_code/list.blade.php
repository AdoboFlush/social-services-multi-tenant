@extends('layouts.app')

@section('content')

<div class="container-fluid pt-4">
    <div class="card">
        <div class="card-header bg-olive">
            <h3 class="card-title"><i class="fa fa-code mr-2"></i>{{ _lang('Member Code') }}</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="control-label">{{ _lang('Member Code') }}</label>
                        <input type="text" class="form-control" name="member_code" id="member_code" />
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label class="control-label">{{ _lang('Contact Number') }}</label>
                        <input type="text" class="form-control" name="contact_number" id="contact_number" />
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label class="control-label">{{ _lang('First Name') }}</label>
                        <input type="text" class="form-control" name="first_name" id="first_name" />
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label class="control-label">{{ _lang('Middle Name') }}</label>
                        <input type="text" class="form-control" name="middle_name" id="middle_name" />
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label class="control-label">{{ _lang('Last Name') }}</label>
                        <input type="text" class="form-control" name="last_name" id="last_name" />
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label class="control-label">{{ _lang('Birth Date') }}</label>
                        <input type="text" class="form-control" name="birth_date" id="birth_date" />
                    </div>
                </div>

                <div class="col-auto">
                    <div class="form-group">
                        <label class="control-label d-block">&nbsp;</label>
                        <button class="btn btn-secondary" id="btn-search" type="button"><i class="fa fa-search mr-2"></i>{{ _lang('Search') }}</button>
                    </div>
                </div>

                <!-- <div class="col-auto">
					<div class="form-group">
						<label class="control-label d-block">&nbsp;</label>
                        <button class="btn btn-default ajax-modal" href="#" data-title="{{ _lang('Generate Code') }}" data-href="{{ route('member_codes.create', [], false) }}" id="btn-create" type="button">
							<i class="fa fa-plus mr-2"></i>{{ _lang('Generate') }}
						</button>
					</div>
				</div> -->

                <!-- <div class="col-md-1">
					<div class="form-group">
						<label class="control-label d-block">&nbsp;</label>
						<button class="btn btn-default" id="btn-datatable-reset"
							type="button">{{ _lang('Reset') }}</button>
					</div>
				</div> -->
            </div>

            <div class="table-responsive">
                <table id="ajax-table" class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>{{ _lang('Member Code') }}</th>
                            <th>{{ _lang('Contact Number') }}</th>
                            <th>{{ _lang('First Name') }}</th>
                            <th>{{ _lang('Middle Name') }}</th>
                            <th>{{ _lang('Last Name') }}</th>
                            <th>{{ _lang('Birthdate') }}</th>
                            <th>{{ _lang('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-script')
<script>
    $(function() {


        $('#btn-search').on('click', function(e) {
            let search_url = '?' + buildFilterQuery();
            $('#ajax-table').DataTable().ajax.url(search_url).load();
        });

        function buildFilterQuery() {
            let search_url_arr = [];

            let member_code = $('#member_code').val();
            let contact_number = $('#contact_number').val();
            let first_name = $('#first_name').val();
            let middle_name = $('#middle_name').val();
            let last_name = $('#last_name').val();
            let birth_date = $('#birth_date').val();

            if (member_code.length > 0) {
                search_url_arr.push("filter[member_code]=" + member_code);
            }
            if (contact_number.length > 0) {
                search_url_arr.push("filter[contact_number]=" + contact_number);
            }
            if (first_name.length > 0) {
                search_url_arr.push("filter[first_name]=" + first_name);
            }
            if (middle_name.length > 0) {
                search_url_arr.push("filter[middle_name]=" + middle_name);
            }
            if (last_name.length > 0) {
                search_url_arr.push("filter[last_name]=" + last_name);
            }
            if (birth_date.length > 0) {
                search_url_arr.push("filter[birth_date]=" + birth_date);
            }
            return search_url_arr.join('&');
        }

        var table = $("#ajax-table").DataTable({
            'ordering': false,
            'orderCellsTop': true,
            'fixedHeader': true,
            'responsive': true,
            "lengthChange": false,
            "autoWidth": false,
            'searching': false,
            'lengthMenu': [
                [10, 25, 50],
                ['10 rows', '25 rows', '50 rows']
            ],
            'processing': true,
            'serverSide': true,
            'serverMethod': 'get',
            'initComplete': function(settings, json) {
                table.buttons().container().appendTo('#ajax-table_wrapper .col-md-6:eq(0)');
            },
            'ajax': {
                'url': '{{ route('member_codes', [], false) }}',
                'data': {
                    '_token': '{{ csrf_token() }}'
                },
                "dataSrc": function(json) {
                    for (var i = 0, ien = json.data.length; i < ien; i++) {
                        json.data[i]['active'] = json.data[i]['active'] == 1 ?
                            '<span class="badge badge-success">Active</span>' :
                            '<span class="badge badge-danger">Inactive</span>';
                    }
                    return json.data;
                }
            },
            'columns': [{
                    data: 'code'
                },
                {
                    data: 'contact_number',
                    render: function(data, type, row) {
                        return typeof row.member !== undefined && row.member !== null ? row.member.contact_number : '--';
                    },
                },
                {
                    data: 'first_name',
                    render: function(data, type, row) {
                        return typeof row.member !== undefined && row.member !== null ? row.member.first_name : '--';
                    },
                },
                {
                    data: 'middle_name',
                    render: function(data, type, row) {
                        return typeof row.member !== undefined && row.member !== null ? row.member.middle_name : '--';
                    },
                },
                {
                    data: 'last_name',
                    render: function(data, type, row) {
                        return typeof row.member !== undefined && row.member !== null ? row.member.last_name : '--';
                    },
                },
                {
                    data: 'birth_date',
                    render: function(data, type, row) {
                        return typeof row.member !== undefined && row.member !== null ? row.member.birth_date : '--';
                    },
                },
                {
                    data: 'active'
                },
            ]
        });

    });
</script>
@endsection