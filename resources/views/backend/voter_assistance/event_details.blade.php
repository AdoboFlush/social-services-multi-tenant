@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Assistance Event Details</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="/voter_assistance/events">Events</a></li>
                    <li class="breadcrumb-item active">Event Details</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
@endsection

@section('content')

<div class="container-fluid mb-1 mt-1">
    <div class="card card-info" style="width: 100%; height: auto;">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{ _lang('Assistance Event Details') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered table-sm">
                                        <tbody>
                                            <tr>
                                                <th>{{ _lang('Event Name') }}</th>
                                                <td>{{ $assistance_event->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ _lang('Description') }}</th>
                                                <td>{{ $assistance_event->description ?? _lang('N/A') }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ _lang('Assistance Type') }}</th>
                                                <td>{{ $assistance_event->assistance_type }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ _lang('Amount') }}</th>
                                                <td>{{ number_format($assistance_event->amount, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ _lang('Starts At') }}</th>
                                                <td>{{ \Carbon\Carbon::parse($assistance_event->starts_at)->format('F j, Y g:i A') }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ _lang('Ends At') }}</th>
                                                <td>{{ \Carbon\Carbon::parse($assistance_event->ends_at)->format('F j, Y g:i A') }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ _lang('Status') }}</th>
                                                <td>
                                                    <span class="badge {{ $assistance_event->is_active ? 'badge-success' : 'badge-danger' }}">
                                                        {{ $assistance_event->is_active ? _lang('Active') : _lang('Inactive') }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>{{ _lang('Custom Condition Props') }}</th>
                                                <td>{{ $assistance_event->custom_condition_props }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-md-4">

                                </div>

                                <div class="col-md-2">
                                    <!-- Please put the knob here -->
                                    <input type="text" id="claimed-knob" class="knob" value="0" data-width="200" data-height="200" data-fgColor="#3c8dbc" data-readOnly="true" />
                                    <div class="mt-3">
                                        <span>
                                            <i class="fa fa-circle" style="color: #3c8dbc;"></i> Total Claimed: <span id="total_claimed">0</span>
                                        </span><br>
                                        <span>
                                            <i class="fa fa-circle" style="color: #d2d6de;"></i> Total Filtered Voters: <span id="total_filtered_voters">0</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
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
                                    <option value="alliance">Alliance</option>
                                    <option value="alliance_1">Sub Alliance</option>
                                    <option value="party_list">Party list</option>
                                    <option value="party_list_1">Party list 1</option>
                                    <option value="affiliation">Affiliation</option>
                                    <option value="affiliation_subgroup">Aff. Subgroup</option>
                                    <option value="affiliation_1">Affiliation 1</option>
                                    <option value="religion">Religion</option>
                                    <option value="civil_status">Civil Status</option>
                                    <option value="contact_number">Contact Number</option>
                                    <option value="sectoral">Sectoral</option>
                                    <option value="sectoral_subgroup">Sectoral Subgroup</option>
                                    <option value="organization">Organization</option>
                                    <option value="is_deceased">Is Deceased</option>
                                    <option value="assistance_claimed">Assistance Claimed</option>
                                    <option value="remarks">Remarks</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
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
                                    <option value="alliance">Alliance</option>
                                    <option value="alliance_1">Sub Alliance</option>
                                    <option value="party_list">Party list</option>
                                    <option value="party_list_1">Party list 1</option>
                                    <option value="affiliation">Affiliation</option>
                                    <option value="affiliation_subgroup">Aff. Subgroup</option>
                                    <option value="affiliation_1">Affiliation 1</option>
                                    <option value="religion">Religion</option>
                                    <option value="civil_status">Civil Status</option>
                                    <option value="contact_number">Contact Number</option>
                                    <option value="sectoral">Sectoral</option>
                                    <option value="sectoral_subgroup">Sectoral Subgroup</option>
                                    <option value="organization">Organization</option>
                                    <option value="is_deceased">Is Deceased</option>
                                    <option value="assistance_claimed">Assistance Claimed</option>
                                    <option value="remarks">Remarks</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('2nd Search Value') }}</label>
                                <input type="text" class="form-control" name="filter_search_2" id="filter_search_2" placeholder="Search" />
                            </div>
                        </div>

                        <div class="col-md-2">
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

                        <div class="col-md-2"></div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">--</label>
                                <button class="btn btn-primary btn-sm form-control" id="btn-search" type="button"><i class="fa fa-search mr-2"></i>Search</button>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">--</label>
                                <button class="btn btn-secondary btn-sm form-control" id="btn-reset" type="button"><i class="fa fa-undo mr-2"></i>Reset</button>
                            </div>
                        </div>

                        <!-- <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">--</label>
                                <button class="btn btn-success btn-sm form-control" id="btn-export" type="button"><i class="fa fa-table mr-2"></i>Export</button>
                            </div>
                        </div> -->

                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">--</label>
                                <button class="btn btn-warning btn-sm form-control btn-scan-qr" data-mode="coupon" type="button"><i class="fa fa-camera mr-2"></i>Claim via Coupon QR</button>
                            </div>
                        </div>

                         <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">--</label>
                                <button class="btn btn-warning btn-sm form-control btn-scan-qr" data-mode="member-id" type="button"><i class="fa fa-camera mr-2"></i>Claim via Member ID</button>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="hr"></div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="ajax-table" class="table table-bordered table-hover table-sm" style="white-space: nowrap;">
                            <thead>
                                <tr>
                                    <th> <input type="checkbox" class="select-all row-checkbox ml-2 mr-3 mt-2"> Actions</th>
                                    <th>Full name</th>
                                    <th>Birth date</th>
                                    <th>Gender</th>
                                    <th>Precinct</th>
                                    <th>Address</th>
                                    <th>Brgy</th>
                                    <th>Assistances Claimed</th>
                                    <th style="min-width:200px;">Alliance</th>
                                    <th style="min-width:200px;">Sub Alliance</th>
                                    <th style="min-width:200px;">Party list</th>
                                    <th style="min-width:200px;">Party list 1</th>
                                    <th style="min-width:200px;">Affiliation</th>
                                    <th style="min-width:200px;">Aff. Subgroup</th>
                                    <th style="min-width:200px;">Affiliation 1</th>
                                    <th style="min-width:200px;">Sectoral</th>
                                    <th style="min-width:200px;">Sectoral Subgroup</th>
                                    <th style="min-width:200px;">Organization</th>
                                    <th style="min-width:200px;">Is Deceased</th>
                                    <th style="min-width:200px;">Religion</th>
                                    <th style="min-width:200px;">Civil Status</th>
                                    <th style="min-width:200px;">Contact Number</th>
                                    <th style="min-width:200px;">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Actions</th>
                                    <th>Full name</th>
                                    <th>Birth date</th>
                                    <th>Gender</th>
                                    <th>Precinct</th>
                                    <th>Address</th>
                                    <th>Brgy</th>
                                    <th>Assistances Claimed</th>
                                    <th>Alliance</th>
                                    <th>Sub Alliance</th>
                                    <th>Party list</th>
                                    <th>Party list 1</th>
                                    <th>Affiliation</th>
                                    <th>Aff. Subgroup</th>
                                    <th>Affiliation 1</th>
                                    <th>Sectoral</th>
                                    <th>Sectoral Subgroup</th>
                                    <th>Organization</th>
                                    <th>Is Deceased</th>
                                    <th>Religion</th>
                                    <th>Civil Status</th>
                                    <th>Contact Number</th>
                                    <th>Remarks</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label class="control-label mr-3">All Checked Items : </label>
                    <button class="btn btn-secondary btn-multi-download"> <i class="fa fa-download mr-1"></i>Download Claim Coupons </button>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('content-modal')

<div id="scan_qr_modal" class="modal animated bounceInDown" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="max-width:438px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> Claim via QR code scanning </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="qr-reader" style="width: 400px"></div>
            </div>
        </div>
    </div>
</div>

<div id="download-id-modal" class="modal animated bounceInDown" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Downloading...</h5>
            </div>
            <div class="modal-body" style="overflow-y:auto;">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Please don't refresh the page !</h5>
                </div>
                <div class="progress" role="progressbar" aria-label="Download" aria-valuenow="0" aria-valuemin="0" aria-valuemax="0">
                    <div class="progress-bar bg-primary progress-bar-striped" id="download-bar-2" style="width:0%"><span id="download-id-title-2"></span></div>
                </div>
                <div class="download-area d-none"></div>
                <div class="output-area"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('custom-css')
<link rel="stylesheet" href="{{ asset('css/print.min.css')}}">
@endsection

@section('js-script')

<script src="{{asset('js/print.min.js')}}"></script>
<script src="{{asset('js/html2canvas.js')}}"></script>
<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="{{ asset('adminLTE/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('adminLTE/plugins/jquery-knob/jquery.knob.min.js') }}"></script>

<script>
    const {
        jsPDF
    } = window.jspdf; // Ensure jsPDF is correctly referenced
    $(function() {

        $(".knob").knob({
            format: function(value) {
                return value.toFixed(2) + '%';
            },
            min: 0,
            max: 100,
            step: 0.01
        });

        var current_affiliation = '';

        $('#scan_qr_modal .close').on('click', function(e) {
            $('#scan_qr_modal').hide();
        });

        $('.select-all').on('click', function(e) {
            $('.data-checkbox').attr("checked", $(this).is(":checked"));
        });

        $('.btn-scan-qr').on('click', function(e) {

            var mode = $(this).data('mode');
            var html5QrcodeScanner = new Html5QrcodeScanner(
                "qr-reader", {
                    fps: 10,
                    qrbox: 250
                });
            html5QrcodeScanner.render(onScanSuccess);
            $('#scan_qr_modal').show();
            let modal_title = mode == 'coupon' ? "Claim via Scan Coupon QR Code" : "Claim via Member ID";
            $('#scan_qr_modal .modal-title').html(modal_title);

            function onScanSuccess(decodedText, decodedResult) {
                // Disable scanning temporarily
                html5QrcodeScanner.pause();

                $.ajax({
                    url : "{{url('voter_assistance/events/claim/qr') . '/'. $assistance_event->id}}" ,
                    type: "post",
                    data : {
                        'qr_value' : decodedText,
                        'mode' : mode, 
                        '_token' : '{{csrf_token()}}'
                    },
                    success: function(data, textStatus, jqXHR) {
                        if (data?.result == "success") {
                            swal("Assistance claimed successfully.", {
                                icon: "success",
                            });
                            $('#ajax-table').DataTable().ajax.url("").load();
                        } else {
                            swal(data?.message, {
                                icon: "error",
                            });
                        }
                        refreshStats();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        swal("Claim processing failed!", {
                            icon: "error",
                        });
                    },
                    complete: function() {
                        // Re-enable scanning after 2 seconds
                        setTimeout(() => {
                            html5QrcodeScanner.resume();
                        }, 2000);
                    }
                });
            }
        });

        $('#btn-search').on('click', function(e) {
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
            if (search_query_params) {
                $('#ajax-table').DataTable().ajax.url("?" + search_query_params).load();
            } else {
                $('#ajax-table').DataTable().ajax.url("").load();
            }
            refreshStats();
        });

        $('#btn-reset').on('click', function(e) {
            $('#filter_search_1').val("");
            $('#filter_search_2').val("");
            $('#filter_area').val("");
            $('#ajax-table').DataTable().ajax.url("").load();
        });

        $('.select-all').on('click', function(e) {
            $('.data-checkbox').attr("checked", $(this).is(":checked"));
        });

        function refreshStats() {
            $.get("{{ url('/voter_assistance/events/stats') . '/' . $assistance_event->id }}", function(data) {
                var percent = data.percentage || 0;
                $("#claimed-knob").val(parseFloat(percent)).trigger('change');
                $("#claimed-knob-label").text(percent);
                $("#total_claimed").text(data.claimed);
                $("#total_filtered_voters").text(data.total);
            });
        }

        refreshStats();

        // Refresh knob after every DataTable draw
        var table = $("#ajax-table").DataTable({
            'searching': false,
            'orderCellsTop': true,
            'fixedHeader': true,
            'responsive': true,
            "lengthChange": false,
            "autoWidth": false,
            'buttons': ["pageLength"],
            'lengthMenu': [
                [15, 50, 100],
                ['15 rows', '50 rows', '100 rows']
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
                if (data.is_deceased == 1) {
                    $('td', row).css({
                        'background-color': '#dc3545',
                        'color': "white"
                    });
                } else {
                    if (data.assistances_count > 0) {
                        $('td', row).css({
                            'background-color': '#28a745',
                            'color': "white"
                        });
                    }
                }
            },
            'ajax': {
                'url': '{{url("/voter_assistance/events/show/") . "/" . $assistance_event->id}}',
                'data': {
                    '_token': '{{csrf_token()}}'
                },
                "dataSrc": function(json) {
                    for (var i = 0, ien = json.data.length; i < ien; i++) {
                        let current_id = json.data[i]['id'];
                        let columnHtml = '<div class="btn-group">';
                        columnHtml += '<input type="checkbox" data-id="' + json.data[i]['id'] + '" class="data-checkbox row-checkbox ml-2 mr-3 mt-2">';

                        if (json.data[i]['assistances_count'] <= 0 && !json.data[i]['is_deceased']) {
                            columnHtml += '<button class="btn btn-sm btn-warning btn-claim-assistance" data-id="' + json.data[i]['id'] + '" data-full-name="' + json.data[i]['full_name'] + '" data-birth-date="' + json.data[i]['birth_date'] + '" >Claim Assistance</button>';
                        }

                        columnHtml += '</div>';
                        json.data[i]['id'] = columnHtml;
                        json.data[i]['brgy'] = json.data[i]['brgy'].length > 0 ? json.data[i]['brgy'] : 'N/A';
                    }
                    return json.data;
                }
            },
            'columns': [{
                    data: 'id'
                },
                {
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
                {
                    data: 'assistances_count'
                },
                {
                    data: 'alliance'
                },
                {
                    data: 'alliance_1'
                },
                {
                    data: 'party_list'
                },
                {
                    data: 'party_list_1'
                },
                {
                    data: 'affiliation'
                },
                {
                    data: 'affiliation_subgroup'
                },
                {
                    data: 'affiliation_1'
                },
                {
                    data: 'sectoral'
                },
                {
                    data: 'sectoral_subgroup'
                },
                {
                    data: 'organization'
                },
                {
                    data: 'is_deceased'
                },
                {
                    data: 'religion'
                },
                {
                    data: 'civil_status'
                },
                {
                    data: 'contact_number'
                },
                {
                    data: 'remarks'
                }
            ]
        });

        function downloadAndConvertToPDF(selected_ids) {
            if (selected_ids.length === 0) {
                toastr.error("No Selected Ids to preview");
                return;
            }

            let dl_url = "{{url('/voter_assistance/events/generate-multi-coupons').'/'.$assistance_event->id }}?selected_ids=" + encodeURI(selected_ids);

            $.ajax({
                url: dl_url,
                type: "get",
                success: function(data) {
                    $("#download-id-modal").modal("show");
                    $("#download-id-modal .output-area").html(data);

                    var cards = $(".card-area");
                    var pdf = new jsPDF('p', 'mm', 'a4');
                    var pageWidth = pdf.internal.pageSize.getWidth();
                    var pageHeight = pdf.internal.pageSize.getHeight();
                    var cardWidth = 85.6; // Standard credit card width in mm
                    var cardHeight = 53.98; // Standard credit card height in mm
                    var cardsPerRow = 2;
                    var cardsPerPage = Math.floor(pageHeight / cardHeight) * cardsPerRow;
                    var horizontalMargin = (pageWidth - (cardsPerRow * cardWidth)) / (cardsPerRow + 1);
                    var verticalMargin = (pageHeight - (Math.floor(cardsPerPage / cardsPerRow) * cardHeight)) / (Math.floor(cardsPerPage / cardsPerRow) + 1);
                    var currentCard = 0;

                    // Initialize progress bar
                    var totalCards = cards.length;
                    $("#download-bar-2").css("width", "0%").attr("aria-valuenow", "0");

                    cards.each(function(index) {
                        var doc_id = $(this).data("id");
                        html2canvas(document.querySelector("#id-canvass-" + doc_id), {
                            width: 750,
                            logging: false,
                        }).then(canvas => {
                            var imgData = canvas.toDataURL("image/png");
                            var row = Math.floor(currentCard / cardsPerRow);
                            var col = currentCard % cardsPerRow;

                            var x = horizontalMargin + col * (cardWidth + horizontalMargin);
                            var y = verticalMargin + row * (cardHeight + verticalMargin);

                            // Add border to each card
                            pdf.setDrawColor(0); // Black border
                            pdf.setLineWidth(0.5); // Border thickness
                            pdf.rect(x, y, cardWidth, cardHeight);

                            pdf.addImage(imgData, 'PNG', x, y, cardWidth, cardHeight);

                            currentCard++;

                            // Update progress bar
                            var progress = ((currentCard / totalCards) * 100).toFixed(2);
                            $("#download-bar-2").css("width", progress + "%").attr("aria-valuenow", progress);

                            // If the page is full, add a new page
                            if (currentCard % cardsPerPage === 0 && index !== cards.length - 1) {
                                pdf.addPage();
                                currentCard = 0;
                            }

                            // Save the PDF when all cards are processed
                            if (index === cards.length - 1) {
                                pdf.save("{{date('Y-m-d H:i:s')}}-event-coupons.pdf");
                                $("#download-id-modal").modal("hide");
                            }
                        });
                    });
                },
                error: function(jqXHR) {
                    swal("Download failed!", {
                        icon: "error",
                    });
                }
            });
        }

        $('.btn-multi-download').on('click', function(e) {
            let selected_ids = [];
            $('.data-checkbox:checked').each(function() {
                selected_ids.push($(this).data('id'));
            });
            if (selected_ids.length > 0) {
                $("#download-id-modal").modal("show");
                downloadAndConvertToPDF(selected_ids);
            } else {
                toastr.error("No Selected Ids to preview")
            }
        });

        $(document).on('click', '.btn-claim-assistance', function() {
            let id = $(this).data('id');
            let full_name = $(this).data('full-name');
            let birth_date = $(this).data('birth-date');
            swal({
                    title: `Claim assistance for ${full_name} (${birth_date})`,
                    text: "Once claimed, it cannot be undone.",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willSubmit) => {
                    if (willSubmit) {
                        $.ajax({
                            url: "{{url('voter_assistance/events/claim') . '/'. $assistance_event->id}}",
                            type: "post",
                            data: {
                                'voter_id': id,
                                '_token': '{{csrf_token()}}'
                            },
                            success: function(data, textStatus, jqXHR) {
                                if (data?.result == "success") {
                                    swal("Assistance claimed successfully.", {
                                        icon: "success",
                                    });
                                    $('#ajax-table').DataTable().ajax.url("").load();
                                } else {
                                    swal(data?.message, {
                                        icon: "error",
                                    });
                                }
                                refreshStats();
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                swal("Claim processing failed!", {
                                    icon: "error",
                                });
                            }
                        });
                    }
                });
        });

    });
</script>

<style>
    input.knob {
        font-size: 2rem !important; 
    }
</style>
@endsection