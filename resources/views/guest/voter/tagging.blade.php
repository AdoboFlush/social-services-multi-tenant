@extends('layouts.tagger')

@section('content')

<div class="container-fluid mb-1 mt-1">
    <div class="card card-info" style="width: 100%; height: auto;">
        <div class="card-header">
            <div class="card-title">
                {{ _lang('Voter Tagging') }}
            </div>
            @if($can_clear_field)
            <div class="float-right">
                <button id="btn-clear-field" class="btn btn-danger btn-sm ml-2"><i class="fa fa-trash mr-2"></i>Clear Field</button>
            </div>
            @endif
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('1st Filter Field') }}</label>
                                <select class="form-control select2" name="filter_field_1" id="filter_field_1">
                                    <option value="">Search Field</option>
                                    <option value="full_name">Full name</option>
                                    <option value="birth_date">Birth Date</option>
                                    <option value="brgy">Barangay</option>
                                    <option value="address">Address</option>
                                    <option value="gender">Gender</option>
                                    <option value="precinct">Precinct</option>

                                    @if(!in_array("alliance", $hide_fields))
                                    <option value="alliance">Alliance</option>
                                    @endif

                                    @if(!in_array("alliance_1", $hide_fields))
                                    <option value="alliance_1">Sub Alliance</option>
                                    @endif

                                    @if(!in_array("party_list", $hide_fields))
                                    <option value="party_list">Party list</option>
                                    @endif

                                    @if(!in_array("party_list_1", $hide_fields))
                                    <option value="party_list_1">Party list 1</option>
                                    @endif

                                    @if(!in_array("affiliation", $hide_fields))
                                    <option value="affiliation">Affiliation</option>
                                    @endif

                                    @if(!in_array("position", $hide_fields))
                                    <option value="position">Position</option>
                                    @endif

                                    @if(!in_array("affiliation_subgroup", $hide_fields))
                                    <option value="affiliation_subgroup">Aff. Subgroup</option>
                                    @endif

                                    @if(!in_array("affiliation_1", $hide_fields))
                                    <option value="affiliation_1">Affiliation 1</option>
                                    @endif

                                    @if(!in_array("religion", $hide_fields))
                                    <option value="religion">Religion</option>
                                    @endif

                                    @if(!in_array("civil_status", $hide_fields))
                                    <option value="civil_status">Civil Status</option>
                                    @endif

                                    @if(!in_array("contact_number", $hide_fields))
                                    <option value="contact_number">Contact Number</option>
                                    @endif

                                    @if(!in_array("sectoral", $hide_fields))
                                    <option value="sectoral">Sectoral</option>
                                    @endif

                                    @if(!in_array("sectoral_subgroup", $hide_fields))
                                    <option value="sectoral_subgroup">Sectoral Subgroup</option>
                                    @endif

                                    @if(!in_array("affiliation_subgroup", $hide_fields))
                                    <option value="organization">Organization</option>
                                    @endif

                                    @if(!in_array("is_deceased", $hide_fields))
                                    <option value="is_deceased">Is Deceased</option>
                                    @endif

                                    @if(!in_array("remarks", $hide_fields))
                                    <option value="remarks">Remarks</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('1st Search Value') }}</label>
                                <input type="text" class="form-control" name="filter_search_1" id="filter_search_1" placeholder="Search" />
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('2nd Filter Field') }}</label>
                                <select class="form-control select2" name="filter_field_2" id="filter_field_2">
                                    <option value="">Search Field</option>
                                    <option value="full_name">Full name</option>
                                    <option value="birth_date">Birth Date</option>
                                    <option value="brgy">Barangay</option>
                                    <option value="address">Address</option>
                                    <option value="gender">Gender</option>
                                    <option value="precinct">Precinct</option>

                                    @if(!in_array("alliance", $hide_fields))
                                    <option value="alliance">Alliance</option>
                                    @endif

                                    @if(!in_array("alliance_1", $hide_fields))
                                    <option value="alliance_1">Sub Alliance</option>
                                    @endif

                                    @if(!in_array("party_list", $hide_fields))
                                    <option value="party_list">Party list</option>
                                    @endif

                                    @if(!in_array("party_list_1", $hide_fields))
                                    <option value="party_list_1">Party list 1</option>
                                    @endif

                                    @if(!in_array("affiliation", $hide_fields))
                                    <option value="affiliation">Affiliation</option>
                                    @endif

                                    @if(!in_array("position", $hide_fields))
                                    <option value="position">Position</option>
                                    @endif

                                    @if(!in_array("affiliation_subgroup", $hide_fields))
                                    <option value="affiliation_subgroup">Aff. Subgroup</option>
                                    @endif

                                    @if(!in_array("affiliation_1", $hide_fields))
                                    <option value="affiliation_1">Affiliation 1</option>
                                    @endif

                                    @if(!in_array("religion", $hide_fields))
                                    <option value="religion">Religion</option>
                                    @endif

                                    @if(!in_array("civil_status", $hide_fields))
                                    <option value="civil_status">Civil Status</option>
                                    @endif

                                    @if(!in_array("contact_number", $hide_fields))
                                    <option value="contact_number">Contact Number</option>
                                    @endif

                                    @if(!in_array("sectoral", $hide_fields))
                                    <option value="sectoral">Sectoral</option>
                                    @endif

                                    @if(!in_array("sectoral_subgroup", $hide_fields))
                                    <option value="sectoral_subgroup">Sectoral Subgroup</option>
                                    @endif

                                    @if(!in_array("affiliation_subgroup", $hide_fields))
                                    <option value="organization">Organization</option>
                                    @endif

                                    @if(!in_array("is_deceased", $hide_fields))
                                    <option value="is_deceased">Is Deceased</option>
                                    @endif

                                    @if(!in_array("remarks", $hide_fields))
                                    <option value="remarks">Remarks</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('2nd Search Value') }}</label>
                                <input type="text" class="form-control" name="filter_search_2" id="filter_search_2" placeholder="Search" />
                            </div>
                        </div>

                        @if($has_area_search)
                        <div class="col-md-1">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Area') }}</label>
                                <select class="form-control" id="filter_area" name="filter_area">
                                    <option value="">{{ _lang('*') }}</option>
                                    <option value="1">{{ _lang('Area 1') }}</option>
                                    <option value="2">{{ _lang('Area 2') }}</option>
                                    <option value="3">{{ _lang('Area 3') }}</option>
                                    <option value="4">{{ _lang('Area 4') }}</option>
                                    <option value="5">{{ _lang('Area 5') }}</option>
                                    <option value="6">{{ _lang('Area 6') }}</option>
                                </select>
                            </div>
                        </div>
                        @endif

                        <div class="col-md-1">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Status') }}</label>
                                <select class="form-control" id="filter_status" name="filter_status">
                                    <option value="">{{ _lang('All') }}</option>
                                    <option value="alive">{{ _lang('Alive') }}</option>
                                    <option value="deceased">{{ _lang('Deceased') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-1">
                            <div class="form-group">
                                <label class="control-label">--</label>
                                <button class="btn btn-primary form-control" id="btn-search" type="button"><i class="fa fa-search mr-2"></i>Search</button>
                            </div>
                        </div>

                        <div class="col-md-1">
                            <div class="form-group">
                                <label class="control-label">--</label>
                                <button class="btn btn-secondary form-control" id="btn-reset" type="button"><i class="fa fa-undo mr-2"></i>Reset</button>
                            </div>
                        </div>

                        @if($has_export)
                        <div class="col-md-1">
                            <div class="form-group">
                                <label class="control-label">--</label>
                                <button class="btn btn-success form-control" id="btn-export" type="button"><i class="fa fa-table mr-2"></i>Export</button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="hr"></div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="ajax-table" class="table table-bordered table-hover table-sm" style="white-space: nowrap;">
                            <thead>
                                <tr>
                                    <th>Full name</th>
                                    <th>Birth date</th>
                                    <th>Gender</th>
                                    <th>Precinct</th>
                                    <th>Address</th>
                                    <th>Brgy</th>

                                    @if(!in_array("alliance", $hide_fields))
                                    <th style="min-width:200px;">Alliance</th>
                                    @endif

                                    @if(!in_array("alliance_1", $hide_fields))
                                    <th style="min-width:200px;">Sub Alliance</th>
                                    @endif

                                    @if(!in_array("position", $hide_fields))
                                    <th style="min-width:200px;">Position</th>
                                    @endif

                                    @if(!in_array("party_list", $hide_fields))
                                    <th style="min-width:200px;">Party list</th>
                                    @endif

                                    @if(!in_array("party_list_1", $hide_fields))
                                    <th style="min-width:200px;">Party list 1</th>
                                    @endif

                                    @if(!in_array("affiliation", $hide_fields))
                                    <th style="min-width:200px;">Affiliation</th>
                                    @endif

                                    @if(!in_array("affiliation_subgroup", $hide_fields))
                                    <th style="min-width:200px;">Aff. Subgroup</th>
                                    @endif

                                    @if(!in_array("affiliation_1", $hide_fields))
                                    <th style="min-width:200px;">Affiliation 1</th>
                                    @endif

                                    @if(!in_array("sectoral", $hide_fields))
                                    <th style="min-width:200px;">Sectoral</th>
                                    @endif

                                    @if(!in_array("sectoral_subgroup", $hide_fields))
                                    <th style="min-width:200px;">Sectoral Subgroup</th>
                                    @endif

                                    @if(!in_array("organization", $hide_fields))
                                    <th style="min-width:200px;">Organization</th>
                                    @endif

                                    @if(!in_array("religion", $hide_fields))
                                    <th style="min-width:200px;">Religion</th>
                                    @endif

                                    @if(!in_array("civil_status", $hide_fields))
                                    <th style="min-width:200px;">Civil Status</th>
                                    @endif

                                    @if(!in_array("contact_number", $hide_fields))
                                    <th style="min-width:200px;">Contact Number</th>
                                    @endif

                                    @if(!in_array("is_deceased", $hide_fields))
                                    <th style="min-width:200px;">Is Deceased</th>
                                    @endif

                                    @if(!in_array("remarks", $hide_fields))
                                    <th style="min-width:200px;">Remarks</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Full name</th>
                                    <th>Birth date</th>
                                    <th>Gender</th>
                                    <th>Precinct</th>
                                    <th>Address</th>
                                    <th>Brgy</th>

                                    @if(!in_array("alliance", $hide_fields))
                                    <th>Alliance</th>
                                    @endif

                                    @if(!in_array("alliance_1", $hide_fields))
                                    <th>Sub Alliance</th>
                                    @endif

                                    @if(!in_array("position", $hide_fields))
                                    <th>Position</th>
                                    @endif

                                    @if(!in_array("party_list", $hide_fields))
                                    <th>Party list</th>
                                    @endif

                                    @if(!in_array("party_list_1", $hide_fields))
                                    <th>Party list 1</th>
                                    @endif

                                    @if(!in_array("affiliation", $hide_fields))
                                    <th>Affiliation</th>
                                    @endif

                                    @if(!in_array("affiliation_subgroup", $hide_fields))
                                    <th>Aff. Subgroup</th>
                                    @endif

                                    @if(!in_array("affiliation_1", $hide_fields))
                                    <th>Affiliation 1</th>
                                    @endif

                                    @if(!in_array("sectoral", $hide_fields))
                                    <th>Sectoral</th>
                                    @endif

                                    @if(!in_array("sectoral_subgroup", $hide_fields))
                                    <th>Sectoral Subgroup</th>
                                    @endif

                                    @if(!in_array("organization", $hide_fields))
                                    <th>Organization</th>
                                    @endif
                                    
                                    @if(!in_array("religion", $hide_fields))
                                    <th>Religion</th>
                                    @endif

                                    @if(!in_array("civil_status", $hide_fields))
                                    <th>Civil Status</th>
                                    @endif

                                    @if(!in_array("contact_number", $hide_fields))
                                    <th>Contact Number</th>
                                    @endif

                                    @if(!in_array("is_deceased", $hide_fields))
                                    <th>Is Deceased</th>
                                    @endif

                                    @if(!in_array("remarks", $hide_fields))
                                    <th>Remarks</th>
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Clear Field Modal -->
<div class="modal fade" id="clearFieldModal" tabindex="-1" role="dialog" aria-labelledby="clearFieldModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 500px;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clearFieldModalLabel">Clear Field</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="clearFieldForm">
                    <div class="form-group">
                        <div id="clearFieldError" class="alert alert-danger d-none"></div>
                        <div id="clearFieldSuccess" class="alert alert-success d-none"></div>
                        <label for="clear_field_select">Clear Field</label>
                        <select class="form-control" id="clear_field_select" name="field" required>
                            <option value="">-- Select Field --</option>
                            <option value="alliance">Alliance</option>
                            <option value="affiliation">Affiliation</option>
                            <option value="position">Position</option>
                            <option value="contact_number">Contact Number</option>
                            <option value="civil_status">Civil Status</option>
                            <option value="religion">Religion</option>
                            <option value="last_update_by">Last Update By</option>
                            <option value="alliance_subgroup">Alliance Subgroup</option>
                            <option value="alliance_1">Alliance 1</option>
                            <option value="alliance_1_subgroup">Alliance 1 Subgroup</option>
                            <option value="affiliation_subgroup">Affiliation Subgroup</option>
                            <option value="affiliation_1">Affiliation 1</option>
                            <option value="affiliation_1_subgroup">Affiliation 1 Subgroup</option>
                            <option value="sectoral">Sectoral</option>
                            <option value="sectoral_1">Sectoral 1</option>
                            <option value="sectoral_subgroup">Sectoral Subgroup</option>
                            <option value="sectoral_1_subgroup">Sectoral 1 Subgroup</option>
                            <option value="organization">Organization</option>
                            <option value="remarks">Remarks</option>
                            <option value="party_list">Party List</option>
                            <option value="party_list_1">Party List 1</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="filter_field_select">Filtered By</label>
                        <select class="form-control" id="filter_field_select" name="filter_field" required>
                            <option value="">-- Select Field --</option>
                            <option value="alliance">Alliance</option>
                            <option value="affiliation">Affiliation</option>
                            <option value="position">Position</option>
                            <option value="contact_number">Contact Number</option>
                            <option value="civil_status">Civil Status</option>
                            <option value="religion">Religion</option>
                            <option value="last_update_by">Last Update By</option>
                            <option value="alliance_subgroup">Alliance Subgroup</option>
                            <option value="alliance_1">Alliance 1</option>
                            <option value="alliance_1_subgroup">Alliance 1 Subgroup</option>
                            <option value="affiliation_subgroup">Affiliation Subgroup</option>
                            <option value="affiliation_1">Affiliation 1</option>
                            <option value="affiliation_1_subgroup">Affiliation 1 Subgroup</option>
                            <option value="sectoral">Sectoral</option>
                            <option value="sectoral_1">Sectoral 1</option>
                            <option value="sectoral_subgroup">Sectoral Subgroup</option>
                            <option value="sectoral_1_subgroup">Sectoral 1 Subgroup</option>
                            <option value="organization">Organization</option>
                            <option value="remarks">Remarks</option>
                            <option value="party_list">Party List</option>
                            <option value="party_list_1">Party List 1</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="filter_field_value">with Value</label>
                        <input type="text" class="form-control" id="filter_field_value" name="field_value" placeholder="Enter value to filter by" autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="clear_field_password">Password</label>
                        <input type="password" class="form-control" id="clear_field_password" name="password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="clearFieldSubmit">Clear Field</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js-script')

<script>
    $(function() {

        var current_affiliation = '';

        $('#btn-search').on('click', function(e) {
            let field_name_1 = $('#filter_field_1').val();
            let search_value_1 = $('#filter_search_1').val();
            let field_name_2 = $('#filter_field_2').val();
            let search_value_2 = $('#filter_search_2').val();
            let filter_area = $('#filter_area').val();
            let filter_status = $('#filter_status').val();
            let search_data_arr = [];

            if (field_name_1 && search_value_1) {
                search_data_arr.push({
                    key: field_name_1,
                    value: search_value_1
                });
            }
            if (field_name_2 && search_value_2) {
                search_data_arr.push({
                    key: field_name_2,
                    value: search_value_2
                });
            }

            let search_query_params = search_data_arr.map((data) => "filter[" + data.key + "]=" + data.value).join("&");
            if (filter_area) {
                search_query_params += '&filter_area=' + filter_area;
            }
            if (filter_status) {
                search_query_params += '&filter_status=' + filter_status;
            }
            if (search_query_params) {
                $('#ajax-table').DataTable().ajax.url("?" + search_query_params).load();
            } else {
                $('#ajax-table').DataTable().ajax.url("").load();
            }
        });

        $('#btn-reset').on('click', function(e) {
            $('#filter_search_1').val("");
            $('#filter_search_2').val("");
            $('#filter_area').val("");
            $('#ajax-table').DataTable().ajax.url("").load();
        });

        $(document).on("click", '.btn-field-update', function() {

            let field = $(this).data('field');
            let value = $(this).data('value');
            let voter_id = $(this).data('voter-id');

            let voter_full_name = $(this).data('voter-name');
            let voter_birth_date = $(this).data('voter-birth-date');
            let voter_brgy = $(this).data('voter-brgy');

            $('.field_updater').remove();

            let update_button = '<div class="input-group-append" id="process_field_update" style="cursor:pointer;" data-field="' + field + '" data-voter-id="' + voter_id + '">' +
                '<div class="input-group-text"><i class="fas fa-save"></i></div>' +
                '</div>';

            let affiliation_field_html = '<div class="input-group field_updater mt-2" id="affiliation_update">' +
                '<select class="form-control" name="field_affiliation" id="field_affiliation">' +
                '<option value="">Select affiliation</option>'
            @foreach($affiliations as $affiliation) +
                '<option value="{{$affiliation->name}}">{{$affiliation->name}}</option>'
            @endforeach
                +
                '</select>' +
                update_button +
                '</div>';

            let position_field_html = '<div class="input-group field_updater mt-2" id="position_update">' +
                '<select class="form-control" name="field_position" id="field_position">' +
                '<option value="">Select position</option>'
            @foreach($positions as $position) +
                '<option value="{{$position}}">{{$position}}</option>'
            @endforeach
                +
                '</select>' +
                update_button +
                '</div>';

            let alliance_field_html = '<div class="input-group field_updater mt-2" id="alliance_update">' +
                '<select class="form-control" name="field_alliance" id="field_alliance">' +
                '<option value="">Select alliance</option>'
            @foreach($alliances as $alliance) +
                '<option value="{{$alliance->name}}">{{$alliance->name}}</option>'
            @endforeach
                +
                '</select>' +
                update_button +
                '</div>';

            let religion_field_html = '<div class="input-group field_updater mt-2" id="religion_update">' +
                '<select class="form-control" name="field_religion" id="field_religion">' +
                '<option value="">Select religion</option>'
            @foreach($religions as $religion) +
                '<option value="{{$religion}}">{{$religion}}</option>'
            @endforeach
                +
                '</select>' +
                update_button +
                '</div>';

            let sectoral_field_html = '<div class="input-group field_updater mt-2" id="sectoral_update">' +
                '<select class="form-control" name="field_sectoral" id="field_sectoral">' +
                '<option value="">Select Sectoral</option>'
            @foreach($sectorals as $sectoral) +
                '<option value="{{$sectoral->name}}">{{$sectoral->name}}</option>'
            @endforeach
                +
                '</select>' +
                update_button +
                '</div>';

            let organization_field_html = '<div class="input-group field_updater mt-2" id="organization_update">' +
                '<select class="form-control" name="field_organization" id="field_organization">' +
                '<option value="">Select Organization</option>'
            @foreach($organizations as $organization) +
                '<option value="{{$organization}}">{{$organization}}</option>'
            @endforeach
                +
                '</select>' +
                update_button +
                '</div>';

            let is_deceased_field_html = '<div class="input-group field_updater mt-2" id="is_deceased_update">' +
                '<select class="form-control" name="field_is_deceased" id="field_is_deceased">' +
                '<option value="0">NO</option>' +
                '<option value="1">YES</option>' +
                '</select>' +
                update_button +
                '</div>';

            let civil_status_field_html = '<div class="input-group field_updater mt-2" id="civil_status_update">' +
                '<select class="form-control" name="field_civil_status" id="field_civil_status">' +
                '<option value="">Select civil status</option>'
            @foreach($civil_statuses as $civil_status) +
                '<option value="{{$civil_status}}">{{$civil_status}}</option>'
            @endforeach
                +
                '</select>' +
                update_button +
                '</div>';

            let contact_number_field_html = '<div class="input-group field_updater mt-2" id="contact_number_update">' +
                '<input type="text" class="form-control" name="field_contact_number" id="field_contact_number" placeholder="Enter contact number" />' +
                update_button +
                '</div>';

            let remarks_field_html = '<div class="input-group field_updater mt-2" id="remarks_update">' +
                '<input type="text" class="form-control" name="field_remarks" id="field_remarks" placeholder="Enter remarks" />' +
                update_button +
                '</div>';

            let alliance_1_field_html = '<div class="input-group field_updater mt-2" id="alliance_1_update">' +
                '<select class="form-control" name="field_alliance_1" id="field_alliance_1">' +
                '<option value="">Select alliance_1</option>'
            @foreach($alliances_1 as $alliance_1) +
                '<option value="{{$alliance_1->name}}">{{$alliance_1->name}}</option>'
            @endforeach
                +
                '</select>' +
                update_button +
                '</div>';

            let affiliation_1_field_html = '<div class="input-group field_updater mt-2" id="affiliation_1_update">' +
                '<select class="form-control" name="field_affiliation_1" id="field_affiliation_1">' +
                '<option value="">Select affiliation 1</option>'
            @foreach($affiliations_1 as $affiliation_1) +
                '<option value="{{$affiliation_1->name}}">{{$affiliation_1->name}}</option>'
            @endforeach
                +
                '</select>' +
                update_button +
                '</div>';

            let party_list_field_html = '<div class="input-group field_updater mt-2" id="party_list_update">' +
                '<select class="form-control" name="field_party_list" id="field_party_list">' +
                '<option value="">Select party list</option>'
            @foreach($party_lists as $party_list) +
                '<option value="{{$party_list}}">{{$party_list}}</option>'
            @endforeach
                +
                '</select>' +
                update_button +
                '</div>';

            let party_list_1_field_html = '<div class="input-group field_updater mt-2" id="party_list_1_update">' +
                '<select class="form-control" name="field_party_list_1" id="field_party_list_1">' +
                '<option value="">Select party list 1</option>'
            @foreach($party_lists_1 as $party_list_1) +
                '<option value="{{$party_list_1}}">{{$party_list_1}}</option>'
            @endforeach
                +
                '</select>' +
                update_button +
                '</div>';

            if (field == "affiliation") {
                $(this).parent('td').append(affiliation_field_html);
            } else if (field == "position") {
                $(this).parent('td').append(position_field_html);
            } else if (field == "affiliation_subgroup") {
                let current_affiliation = $(this).data('current-aff');
                if (current_affiliation) {
                    $.ajax({
                        context: this,
                        type: 'get',
                        url: "{{url('tag/get_child_tags_by_parent_name')}}/" + current_affiliation,
                        success: function(data, status) {
                            if (status == 'success') {
                                let affiliation_group_field_html = '<div class="input-group field_updater mt-2" id="affiliation_update">' +
                                    '<select class="form-control" name="field_affiliation_subgroup" id="field_affiliation_subgroup">' +
                                    '<option value="">Select affiliation subgroup</option>';
                                $.each(data, function(key, value) {
                                    affiliation_group_field_html += '<option value="' + value.name + '">' + value.name + '</option>';
                                });
                                affiliation_group_field_html += '</select>' + update_button + '</div>';
                                $(this).parent('td').append(affiliation_group_field_html);
                            }
                        }
                    });
                }
            } else if (field == "sectoral_subgroup") {
                let current_sectoral = $(this).data('current-sectoral');
                if (current_sectoral) {
                    $.ajax({
                        context: this,
                        type: 'get',
                        url: "{{url('tag/get_child_tags_by_parent_name')}}/" + current_sectoral,
                        success: function(data, status) {
                            if (status == 'success') {
                                let sectoral_group_field_html = '<div class="input-group field_updater mt-2" id="sectoral_subgroup_update">' +
                                    '<select class="form-control" name="field_sectoral_subgroup" id="field_sectoral_subgroup">' +
                                    '<option value="">Select sectoral subgroup</option>';
                                $.each(data, function(key, value) {
                                    sectoral_group_field_html += '<option value="' + value.name + '">' + value.name + '</option>';
                                });
                                sectoral_group_field_html += '</select>' + update_button + '</div>';
                                $(this).parent('td').append(sectoral_group_field_html);
                            }
                        }
                    });
                }
            } else if (field == "alliance") {
                $(this).parent('td').append(alliance_field_html);
            } else if (field == "religion") {
                $(this).parent('td').append(religion_field_html);
            } else if (field == "civil_status") {
                $(this).parent('td').append(civil_status_field_html);
            } else if (field == "contact_number") {
                $(this).parent('td').append(contact_number_field_html);
            } else if (field == "sectoral") {
                $(this).parent('td').append(sectoral_field_html);
            } else if (field == "organization") {
                $(this).parent('td').append(organization_field_html);
            } else if (field == "is_deceased") {
                $(this).parent('td').append(is_deceased_field_html);
            } else if (field == "alliance_1") {
                $(this).parent('td').append(alliance_1_field_html);
            } else if (field == "affiliation_1") {
                $(this).parent('td').append(affiliation_1_field_html);
            } else if (field == "remarks") {
                $(this).parent('td').append(remarks_field_html);
            } else if (field == "party_list") {
                $(this).parent('td').append(party_list_field_html);
            } else if (field == "party_list_1") {
                $(this).parent('td').append(party_list_1_field_html);
            }

        });

        $(document).on("click", '#process_field_update', function() {

            let field_name = $(this).data('field');
            let selected_voter_id = $(this).data('voter-id');
            let field_value = $('#field_' + field_name).val();
            let ajax_url = "{{url('voters/tagging/update/')}}/" + selected_voter_id;

            $.ajax({
                url: ajax_url,
                type: "post",
                data: {
                    'field_name': field_name,
                    'field_value': field_value,
                    '_token': '{{csrf_token()}}'
                },
                success: function(data, textStatus, jqXHR) {
                    if (data.status == 1) {
                        swal("Records has been updated", {
                            icon: "success",
                        });
                        $('.field_updater').remove();
                    } else {
                        swal("Update failed. Error: " + data.message, {
                            icon: "error",
                        });
                    }
                    $("#ajax-table").DataTable().ajax.reload(null, false);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    swal("Update failed!", {
                        icon: "error",
                    });
                }
            });

        });

        $(document).on("keypress", function(e) {
            if (e.key === "Enter") {
                let activeElement = $(document.activeElement);
                if (activeElement.closest('.field_updater').length > 0) {
                    activeElement.closest('.field_updater').find('#process_field_update').trigger('click');
                }
            }
        });

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
            "aoColumnDefs": [{
                'bSortable': false,
                'aTargets': [0]
            }],
            'processing': true,
            'serverSide': true,
            'serverMethod': 'get',
            'initComplete': function(settings, json) {
                table.buttons().container().appendTo('#ajax-table_wrapper .col-md-6:eq(0)');
                this.api()
                    .columns()
                    .every(function() {
                        var that = this;
                        $('.column_search input', this.footer()).on('keyup change clear', function() {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        });
                    });
            },
            "rowCallback": function(row, data, index) {
                if (data.appended_is_deceased_data == 1) {
                    $('td', row).css({
                        'background-color': '#dc3545',
                        'color': "white"
                    });
                } else {
                    if (data.appended_alliance_data == "ATAYDE" || data.appended_alliance_data == "ARJO ATAYDE" || data.appended_alliance_data == "GAB ATAYDE") {
                        $('td', row).css({
                            'background-color': '#28a745',
                            'color': "white"
                        });
                    } else if (data.appended_alliance_data == "CRISOLOGO") {
                        $('td', row).css({
                            'background-color': '#003d8d',
                            'color': "white"
                        });
                    }
                }
            },
            'ajax': {
                'url': '{{url("/voters/tagging")}}',
                'data': {
                    '_token': '{{csrf_token()}}'
                },
                "dataSrc": function(json) {
                    for (var i = 0, ien = json.data.length; i < ien; i++) {
                        let current_id = json.data[i]['id'];
                        let columnHtml = '<div class="btn-group">';
                        columnHtml += '<input type="checkbox" data-id="' + json.data[i]['id'] + '" class="data-checkbox row-checkbox ml-2 mr-3 mt-2">';
                        columnHtml += '</div>';
                        json.data[i]['id'] = columnHtml;
                        json.data[i]['brgy'] = json.data[i]['brgy'].length > 0 ? json.data[i]['brgy'] : 'N/A';
                        json.data[i]['appended_alliance_data'] = json.data[i]['alliance'] ?? 'N/A';
                        json.data[i]['appended_is_deceased_data'] = json.data[i]['is_deceased'] ?? 0;

                        let append_field_data = ' data-voter-id="' + current_id + '" data-voter-name="' + json.data[i]['full_name'] + '" data-voter-birth-date="' + json.data[i]['birth_date'] + '" data-voter-brgy="' + json.data[i]['brgy'] + '" ';
                        let current_aff = json.data[i]['affiliation'];
                        let current_sectoral = json.data[i]['sectoral'];

                        @if(!$for_viewing_only)

                        json.data[i]['alliance'] = '<button class="btn btn-sm btn-warning btn-field-update mr-2" data-field="alliance" data-value="' + json.data[i]['alliance'] + '" ' + append_field_data + '><i class="nav-icon fa fa-edit"></i></button>' + json.data[i]['alliance'] ?? 'N/A';

                        json.data[i]['affiliation'] = '<button class="btn btn-sm btn-warning btn-field-update mr-2" data-field="affiliation" data-value="' + json.data[i]['affiliation'] + '" ' + append_field_data + '><i class="nav-icon fa fa-edit"></i></button>' + json.data[i]['affiliation'] ?? 'N/A';

                        json.data[i]['position'] = '<button class="btn btn-sm btn-warning btn-field-update mr-2" data-field="position" data-value="' + json.data[i]['position'] + '" ' + append_field_data + '><i class="nav-icon fa fa-edit"></i></button>' + json.data[i]['position'] ?? 'N/A';

                        json.data[i]['affiliation_subgroup'] = '<button class="btn btn-sm btn-warning btn-field-update mr-2" data-field="affiliation_subgroup" data-current-aff="' + current_aff + '" data-value="' + json.data[i]['affiliation_subgroup'] + '" ' + append_field_data + '><i class="nav-icon fa fa-edit"></i></button>' + json.data[i]['affiliation_subgroup'] ?? 'N/A';

                        json.data[i]['sectoral_subgroup'] = '<button class="btn btn-sm btn-warning btn-field-update mr-2" data-field="sectoral_subgroup" data-current-sectoral="' + current_sectoral + '" data-value="' + json.data[i]['sectoral_subgroup'] + '" ' + append_field_data + '><i class="nav-icon fa fa-edit"></i></button>' + json.data[i]['sectoral_subgroup'] ?? 'N/A';

                        json.data[i]['religion'] = '<button class="btn btn-sm btn-warning btn-field-update mr-2" data-field="religion" data-value="' + json.data[i]['religion'] + '" ' + append_field_data + '><i class="nav-icon fa fa-edit"></i></button>' + json.data[i]['religion'] ?? 'N/A';

                        json.data[i]['civil_status'] = '<button class="btn btn-sm btn-warning btn-field-update mr-2" data-field="civil_status" data-value="' + json.data[i]['civil_status'] + '" ' + append_field_data + '><i class="nav-icon fa fa-edit"></i></button>' + json.data[i]['civil_status'] ?? 'N/A';

                        @if(!in_array("contact_number", $hide_fields))
                        json.data[i]['contact_number'] = '<button class="btn btn-sm btn-warning btn-field-update mr-2" data-field="contact_number" data-value="' + json.data[i]['contact_number'] + '" ' + append_field_data + '><i class="nav-icon fa fa-edit"></i></button>' + json.data[i]['contact_number'] ?? 'N/A';
                        @endif

                        json.data[i]['is_deceased'] = '<button class="btn btn-sm btn-warning btn-field-update mr-2" data-field="is_deceased" data-value="' + json.data[i]['is_deceased'] + '" ' + append_field_data + '><i class="nav-icon fa fa-edit"></i></button>' + (json.data[i]['is_deceased'] == 1 ? 'YES' : 'NO');

                        json.data[i]['sectoral'] = '<button class="btn btn-sm btn-warning btn-field-update mr-2" data-field="sectoral" data-value="' + json.data[i]['sectoral'] + '" ' + append_field_data + '><i class="nav-icon fa fa-edit"></i></button>' + json.data[i]['sectoral'] ?? 'N/A';

                        json.data[i]['organization'] = '<button class="btn btn-sm btn-warning btn-field-update mr-2" data-field="organization" data-value="' + json.data[i]['organization'] + '" ' + append_field_data + '><i class="nav-icon fa fa-edit"></i></button>' + json.data[i]['organization'] ?? 'N/A';

                        @if(!in_array("remarks", $hide_fields))
                        json.data[i]['remarks'] = '<button class="btn btn-sm btn-warning btn-field-update mr-2" data-field="remarks" data-value="' + json.data[i]['remarks'] + '" ' + append_field_data + '><i class="nav-icon fa fa-edit"></i></button>' + json.data[i]['remarks'] ?? 'N/A';
                        @endif

                        json.data[i]['alliance_1'] = '<button class="btn btn-sm btn-warning btn-field-update mr-2" data-field="alliance_1" data-value="' + json.data[i]['alliance_1'] + '" ' + append_field_data + '><i class="nav-icon fa fa-edit"></i></button>' + json.data[i]['alliance_1'] ?? 'N/A';

                        json.data[i]['affiliation_1'] = '<button class="btn btn-sm btn-warning btn-field-update mr-2" data-field="affiliation_1" data-value="' + json.data[i]['affiliation_1'] + '" ' + append_field_data + '><i class="nav-icon fa fa-edit"></i></button>' + json.data[i]['affiliation_1'] ?? 'N/A';

                        json.data[i]['party_list'] = '<button class="btn btn-sm btn-warning btn-field-update mr-2" data-field="party_list" data-value="' + json.data[i]['party_list'] + '" ' + append_field_data + '><i class="nav-icon fa fa-edit"></i></button>' + json.data[i]['party_list'] ?? 'N/A';

                        json.data[i]['party_list_1'] = '<button class="btn btn-sm btn-warning btn-field-update mr-2" data-field="party_list_1" data-value="' + json.data[i]['party_list_1'] + '" ' + append_field_data + '><i class="nav-icon fa fa-edit"></i></button>' + json.data[i]['party_list_1'] ?? 'N/A';

                        @endif

                    }
                    return json.data;
                }
            },
            'columns': [{
                    data: 'full_name'
                },
                {
                    data: 'birth_date'
                },
                {
                    data: 'gender'
                },
                {
                    data: 'precinct'
                },
                {
                    data: 'address'
                },
                {
                    data: 'brgy'
                },

                @if(!in_array("alliance", $hide_fields)) {
                    data: 'alliance'
                },
                @endif

                @if(!in_array("alliance_1", $hide_fields)) {
                    data: 'alliance_1'
                },
                @endif

                @if(!in_array("position", $hide_fields)) {
                    data: 'position'
                },
                @endif

                @if(!in_array("party_list", $hide_fields)) {
                    data: 'party_list'
                },
                @endif

                @if(!in_array("party_list_1", $hide_fields)) {
                    data: 'party_list_1'
                },
                @endif

                @if(!in_array("affiliation", $hide_fields)) {
                    data: 'affiliation'
                },
                @endif

                @if(!in_array("affiliation_subgroup", $hide_fields)) {
                    data: 'affiliation_subgroup'
                },
                @endif

                @if(!in_array("affiliation_1", $hide_fields)) {
                    data: 'affiliation_1'
                },
                @endif

                @if(!in_array("sectoral", $hide_fields)) {
                    data: 'sectoral'
                },

                @if(!in_array("sectoral_subgroup", $hide_fields)) {
                    data: 'sectoral_subgroup'
                },
                @endif

                @endif
                @if(!in_array("organization", $hide_fields)) {
                    data: 'organization'
                },
                @endif
                @if(!in_array("religion", $hide_fields)) {
                    data: 'religion'
                },
                @endif
                @if(!in_array("civil_status", $hide_fields)) {
                    data: 'civil_status'
                },
                @endif
                @if(!in_array("contact_number", $hide_fields)) {
                    data: 'contact_number'
                },
                @endif
                @if(!in_array("is_deceased", $hide_fields)) {
                    data: 'is_deceased'
                },
                @endif
                @if(!in_array("remarks", $hide_fields)) {
                    data: 'remarks'
                },
                @endif
            ]
        });

        @if($has_export)
        $('#btn-export').on('click', function(e) {

            let field_name_1 = $('#filter_field_1').val();
            let search_value_1 = $('#filter_search_1').val();
            let field_name_2 = $('#filter_field_2').val();
            let search_value_2 = $('#filter_search_2').val();
            let filter_area = $('#filter_area').val();
            let search_data_arr = [];

            if (field_name_1 && search_value_1) {
                search_data_arr.push({
                    key: field_name_1,
                    value: search_value_1
                });
            }
            if (field_name_2 && search_value_2) {
                search_data_arr.push({
                    key: field_name_2,
                    value: search_value_2
                });
            }
            let search_query_params = search_data_arr.map((data) => "filter[" + data.key + "]=" + data.value).join("&");
            if (filter_area) {
                search_query_params += '&filter_area=' + filter_area;
            }
            let ajax_url = "{{url('voters/tagging/export')}}?" + search_query_params;
            exportCSV(ajax_url, "voter-tagging-{{date('Y-m-d H:i:s')}}.csv");

        });
        @endif

        // Clear Field Button Click
        $('#btn-clear-field').on('click', function() {
            $('#clearFieldForm')[0].reset();
            $('#clearFieldError').addClass('d-none').text('');
            $('#clearFieldSuccess').addClass('d-none').text('');
            $('#clearFieldModal').modal('show');
        });

        // Clear Field Submit
        $('#clearFieldSubmit').on('click', function() {
            let field = $('#clear_field_select').val();
            let password = $('#clear_field_password').val();
            let fieldFilter = $('#filter_field_select').val();
            let fieldValue = $('#filter_field_value').val();

            $('#clearFieldError').addClass('d-none').text('');
            $('#clearFieldSuccess').addClass('d-none').text('');
            if (!field || !password) {
                $('#clearFieldError').removeClass('d-none').text('Please select a field and enter your password.');
                return;
            }
            if (!confirm('Are you sure you want to clear all values for this field? This action cannot be undone.')) {
                return;
            }

            $.ajax({
                url: "{{ url('voters/tagging/clear-field') }}",
                type: 'POST',
                data: {
                    field: field,
                    password: password,
                    field_filter: fieldFilter,
                    field_value: fieldValue,
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    if (res.status === 1) {
                        $('#clearFieldSuccess').removeClass('d-none').text(res.message);
                        setTimeout(function() {
                            $('#clearFieldModal').modal('hide');
                            $('#ajax-table').DataTable().ajax.reload();
                        }, 1200);
                    } else {
                        $('#clearFieldError').removeClass('d-none').text(res.message);
                    }
                },
                error: function(xhr) {
                    let msg = 'An error occurred.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    $('#clearFieldError').removeClass('d-none').text(msg);
                }
            });
        });

    });
</script>
@endsection