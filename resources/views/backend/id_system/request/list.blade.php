@extends('backend.id_system.layout')

@section('card-header')
<h3 class="card-title">Request List</h3>
<span class="float-right ml-2">
    <div class="btn-group">
        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-qrcode"></i> Scan ID QR
        </button>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="#" id="btn-scan-qr" data-title="{{ _lang('Via Camera') }}">
                <i class="fa fa-qrcode mr-2"></i> via Camera
            </a>
            <a class="dropdown-item" href="#" id="btn-scan-qr-alt" data-title="{{ _lang('Via Device / Manual') }}">
                <i class="fa fa-qrcode mr-2"></i> via Device / Manual
            </a>
            <!-- Add more dropdown items here if needed -->
        </div>
    </div>
</span>

<span class="float-right"><a href="{{url()->current()."/create"}}" class="btn btn-primary btn-sm" data-title="{{ _lang('Add New') }}">Add New</a></span>
@endsection

@section('tab-content')

<input type="hidden" id="total-pages" value="2"/>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label">{{ _lang('Date From') }}</label>
            <input type="text" class="form-control datepicker" name="filter_date_from" id="filter_date_from" value="{{ old('filter_date_from') }}">
            <span class="err-message">{{ _lang('Date From is required') }}</span>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label">{{ _lang('Date To') }}</label>
            <input type="text" class="form-control datepicker" name="filter_date_to" id="filter_date_to" value="{{ old('filter_date_to') }}">
            <span class="err-message">{{ _lang('Date To is required') }}</span>
        </div>
    </div>
	
	<div class="col-md-3">
		<div class="form-group">
			<label class="control-label">{{ _lang('Filter Field') }}</label>
			<select class="form-control select2" name="filter_field" id="filter_field">
				<option value="">Please select a Field</option>
				<option value="account_number">Member Number</option>
				<option value="first_name">First Name</option>
				<option value="middle_name">Middle Name</option>
				<option value="last_name">Last Name</option>
				<option value="brgy">Brgy</option>
			</select>
			<span class="err-message">{{ _lang('filter_field is required') }}</span>
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			<label class="control-label">{{ _lang('Enter what to search') }}</label>
			<input type="text" class="form-control" name="filter_search" id="filter_search" placeholder="Search" />
			<span class="err-message">{{ _lang('Search Value is required') }}</span>
		</div>
	</div>

</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label">{{ _lang('Template') }}</label>
            <select class="form-control select2" name="filter_template" id="filter_template">
                <option value="">Filter By Template</option>
                @foreach($templates as $template)
                <option value="{{$template->id}}">{{$template->name}}</option>
                @endforeach
            </select>
            <span class="err-message">{{ _lang('filter_field is required') }}</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label">{{ _lang('Search ID Number') }}</label>
            <input type="text" class="form-control" name="filter_id_number" id="filter_id_number" placeholder="Enter ID Number" />
            <span class="err-message">{{ _lang('Search Value is required') }}</span>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label">{{ _lang('Alliance') }}</label>
            <select class="form-control select2" name="filter_alliance" id="filter_alliance">
                <option value="">Filter By Alliance</option>
                @foreach($alliances as $alliance)
                <option value="{{$alliance->name}}">{{$alliance->name}}</option>
                @endforeach
            </select>
            <span class="err-message">{{ _lang('filter_field is required') }}</span>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label">{{ _lang('Affiliation') }}</label>
            <select class="form-control select2" name="filter_affiliation" id="filter_affiliation">
                <option value="">Filter By Affiliation</option>
                @foreach($affiliations as $affiliation)
                <option value="{{$affiliation->name}}">{{$affiliation->name}}</option>
                @endforeach
            </select>
            <span class="err-message">{{ _lang('filter_field is required') }}</span>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label">{{ _lang('Show') }}</label>
            <select class="form-control select2" name="filter_show" id="filter_show">
                <option value="">All</option>
                <option value="not_downloaded">Not Downloaded</option>
                <option value="downloaded">Downloaded</option>
            </select>
        </div>
    </div>

    <div class="col-md-3"></div>
    <div class="col-md-3"></div>

    <div class="col-md-1">
        <div class="form-group">
            <label class="control-label">--</label>
            <button class="btn btn-primary form-control" id="btn-search" type="button"><i class="fa fa-search mr-2"></i>Search</button>
        </div>
    </div>
	
	@if(Auth::user()->user_access !== "encoder_voter")
	<div class="col-md-1">
        <div class="form-group">
            <label class="control-label">--</label>
            <button class="btn btn-success form-control" id="btn-export" type="button"><i class="fa fa-table mr-2"></i>Export</button>
        </div>
    </div>
	@endif

    <!-- <div class="col-md-1">
        <div class="form-group">
            <label class="control-label">--</label>
                <button class="btn btn-secondary form-control dropdown-toggle dropdown-icon"  type="button" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-download mr-2"></i>Download
                </button>

                <div class="dropdown-menu" style="">
                    <a class="dropdown-item" id="btn-download-front" href="javascript:void(0)">Download A4 (Front)</a>
                    <a class="dropdown-item" id="btn-download-back" href="javascript:void(0)">Download A4 (Back)</a>
                    {{-- <a class="dropdown-item" id="btn-download-front-single" href="javascript:void(0)">Single Download (Front)</a>
                    <a class="dropdown-item" id="btn-download-back-single" href="javascript:void(0)">Single Download (Back)</a> --}}
                </div>

        </div>
    </div> -->

</div>

<div class="row">
    <div class="col-md-12">
      <div class="table-responsive">
          <table id="ajax-table" class="table table-sm table-bordered table-striped" style="white-space:nowrap;">
              <thead>
                  <tr>
                    <th> <input type="checkbox" class="select-all row-checkbox ml-2 mr-3 mt-2"> Actions</th>
                    <th>Member</th>
                    <th>ID Number</th>
                    <th>Brgy</th>
                    <th>Alliance</th>
                    <th>Affiliation</th>                  
                    <th>Template</th>
                    <th>Download Count</th>
                    <th>Download By</th>
                    <th>Last Download At</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Remarks</th>
                  </tr>
              </thead>
              <tfoot>
                  <tr>
                    <th>Actions</th>
                    <th>Member</th>
                    <th>ID Number</th>
                    <th>Brgy</th>
                    <th>Alliance</th>
                    <th>Affiliation</th>             
                    <th>Template</th>
                    <th>Download Count</th>
                    <th>Download By</th>
                    <th>Last Download At</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Remarks</th>
                  </tr>
              </tfoot>
              <tbody>
              </tbody>    
          </table>
      </div>
      <div class="form-group mt-3">
          <label class="control-label mr-3">All Checked Items : </label>
		  <!-- <button class="btn btn-success btn-multi-preview">Batch Preview</button> -->
          <button class="btn btn-primary btn-multi-download" data-part="0"><i class="fa fa-download mr-2"></i> Download Front (A4)</button>
          <button class="btn btn-primary btn-multi-download" data-part="1"><i class="fa fa-download mr-2"></i>Download Back (A4)</button>
          <button class="btn btn-primary btn-multi-download" data-part="2"><i class="fa fa-download mr-2"></i>Download All Sides (A4)</button>
          <button class="btn btn-danger btn-multi-delete"><i class="fa fa-trash mr-2"></i>Delete</button>
      </div>
    </div>
    
</div>

@endsection

@section('content-modal')

<div id="scan_qr_modal" class="modal animated bounceInDown" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="max-width:438px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> Scan QR Code Via Camera </h5>
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
    <div class="modal-dialog" role="document" style="width: 800px; max-height:200px;">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Downloading IDs. Please don't refresh the page. (<span id="download-id-title"> 1 / 1</span>)</h5>
        </div>
        <div class="alert alert-danger" style="display:none; margin: 15px;"></div>
        <div class="alert alert-success" style="display:none; margin: 15px;"></div>
        <div class="modal-body" style="overflow-y:hidden; overflow-x:hidden; height:70px">
            <div class="progress mb-5">
                <div class="progress-bar bg-primary progress-bar-striped" role="progressbar" id="download-bar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="0" style="width:0%">
                    <span class="sr-only">Downloaded</span>
                </div>
            </div>
            <div class="download-area d-none"></div>
            <div class="output-area"></div>
        </div>
      </div>
    </div>
</div>

<div id="scan_qr_modal_alt" class="modal animated bounceInDown" tabindex="-1" role="dialog">
  <div class="modal-dialog" style="max-width:438px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Scan QR Code Via Device / Manual </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close-scan-qr-modal-alt">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="input-group">
          <input type="text" class="form-control" placeholder="Enter ID Number Code or QR Value" id="scan_id_number" autofocus>
          <div class="input-group-append">
            <button class="btn btn-primary btn-sm" type="button" id="submit_scan_id_number">
              <i class="fa fa-paper-plane"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('custom-css')
<link rel="stylesheet" href="{{ asset('css/print.min.css')}}">
<style>
    .profile-pic-square {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 10px;
        border: 2px solid #dee2e6;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        background: #fff;
        display: block;
    }
</style>
@endsection

@section('js-script')

<script src="{{asset('js/print.min.js')}}"></script>
<script src="{{asset('js/html2canvas.js')}}"></script>
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
$(function () {

	$('.select-all').on('click', function(e){
        var checked = $(this).is(":checked");
        $('.data-checkbox').prop("checked", checked).trigger('change');
    });

    // SCAN QR VIA CAMERA

    $('#btn-scan-qr').on('click', function(e){
        var html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader", { fps: 10, qrbox: 250 });
            html5QrcodeScanner.render(onScanSuccess);
        $('#scan_qr_modal').show();
        function onScanSuccess(decodedText, decodedResult) {
            html5QrcodeScanner.clear().then(_ => {
                $(location).attr("href", "/id_system/requests/scan/" + decodedText);
                $('#scan_qr_modal').hide();
            }).catch(error => {
                console.log('Unexpected Error. Please try again.');
            });
        }
    });

    $('#scan_qr_modal .close').on('click', function(e){
        $('#scan_qr_modal').hide();
    });

    // SCAN / MANUAL INPUT

    $("#btn-scan-qr-alt").on('click', function(e) {
      $('#scan_qr_modal_alt').show();
      $('#scan_id_number').val('');
      $('#scan_id_number').focus();
    });

    $('#submit_scan_id_number').on('click', function(e) {
      var id_number = $('#scan_id_number').val();
      if (id_number.trim() === '') {
        alert('Please enter a valid ID Number Code.');
        return;
      }
      let url = "/id_system/requests/scan/" + id_number;
      $(location).attr("href", url);
      $('#scan_qr_modal_alt').hide();
    });

    $('#close-scan-qr-modal-alt').on('click', function() {
      $('#scan_qr_modal_alt').hide();
    });


    $('#btn-download-front').on('click', function(e){
        $("#download-id-modal").modal("show");
        downloadIDByPage();
    });

    $('#btn-download-back').on('click', function(e){
        $("#download-id-modal").modal("show");
        downloadIDByPage(1, 1);
    });

    $('#btn-search').on('click', function(e){
        let search_url = '?' + buildFilterQuery();
        $('#ajax-table').DataTable().ajax.url(search_url).load();
    });
	
	$('#btn-export').on('click', function(e){
        let ajax_url = "{{url("id_system/requests/export")}}" + '?' + buildFilterQuery();
        exportCSV(ajax_url, "id-requests-{{date('Y-m-d H:i:s')}}.csv");
    });

    $(document).on('click', '#print-front', function(){
        html2canvas(document.querySelector("#id-canvass-front"), {width: 750}).then(canvas => {
            document.querySelector("#id-output-front").appendChild(canvas);
            let canv = $("#id-output-front canvas");
            let img = canv[0].toDataURL("image/png");
            printImage(img);
            $("#id-output-front").hide();
        });
    });

    $(document).on('click', '#print-back', function(){
        html2canvas(document.querySelector("#id-canvass-back"), {width: 750}).then(canvas => {
            document.querySelector("#id-output-back").appendChild(canvas);
            let canv = $("#id-output-back canvas");
            let img = canv[0].toDataURL("image/png");
            printImage(img);
            $("#id-output-back").hide();
        });
    });

    $(document).on('click', '#download-front', function(){
        html2canvas(document.querySelector("#id-canvass-front"), {width: 750}).then(canvas => {
            document.querySelector("#id-output-front").appendChild(canvas);
            let canv = $("#id-output-front canvas");
            let img = canv[0].toDataURL("image/png");
            let link = document.createElement('a');
            link.download = $("#download-front").data('file-name');
            link.href = img;
            link.click();
            $("#id-output-front").hide();
        });
    });

    $(document).on('click', '#download-back', function(){
        html2canvas(document.querySelector("#id-canvass-back"), {width: 750}).then(canvas => {
            document.querySelector("#id-output-back").appendChild(canvas);
            let canv = $("#id-output-back canvas");
            let img = canv[0].toDataURL("image/png");
            let link = document.createElement('a');
            link.download = $("#download-back").data('file-name');
            link.href = img;
            link.click();
            $("#id-output-back").hide();
        });
    });
	
	$('.btn-multi-preview').on('click', function(e){
		let data_arr = [];
        $('.data-checkbox:checked').each( function () {
            data_arr.push($(this).data('id'));
        });
		if(data_arr.length > 0) {
			let query_str = "?selected_ids=" + encodeURI(data_arr);
			window.open("{{url('id_system/requests/multiple-preview')}}" + query_str, '_blank');
		} else {
			toastr.error("No Selected Ids to preview")
		}
    });

    $('.btn-multi-download').on('click', function(e){
		let selected_ids = [];
        let part = $(this).data('part');
        $('.data-checkbox:checked').each( function () {
            selected_ids.push($(this).data('id'));
        });
		if(selected_ids.length > 0) {
            $("#download-id-modal").modal("show");
            downloadBySelectedIds(selected_ids, 1, part);
        } else {
			toastr.error("No Selected Ids to preview")
		}
    });
   
    $('.btn-multi-delete').on('click', function(e){
        let data_arr = [];
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
                    url : "{{url('id_system/requests/delete')}}",
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
                            icon: "error",
                        });
                    }
                });
            }
        });
    });

    $('.nav-tabs .nav-link').on('click', function(e){
        let search_type = $(this).data('tag-type');
        $('#ajax-table').DataTable().ajax.url("?search_type=" + search_type).load();
    });

    var table = $("#ajax-table").DataTable({
        'orderCellsTop': true,
        'fixedHeader': true,
        'responsive': true, 
        "lengthChange": false,
        'searching': false, 
        "autoWidth": false,
        'buttons': ["pageLength"],
		"aoColumnDefs": [
			  { 'bSortable': false, 'aTargets': [ 0 ] }
		],
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
            'url':'{{url("id_system/requests")}}',
            'data':{
                '_token' : '{{csrf_token()}}'
            },
            "dataSrc": function ( json ) {
                for ( var i=0, ien=json.data.length ; i<ien ; i++ ) {
                    let columnHtml = '<div class="btn-group">';
                    columnHtml += '<input type="checkbox" data-id="'+json.data[i]['id']+'" class="data-checkbox row-checkbox ml-2 mr-3 mt-2">';
                    columnHtml += '<a class="btn btn-success btn-sm ajax-modal" href="#" data-title="{{ _lang('ID Card Preview') }}" data-href="{{url('id_system/requests/preview')}}/'+json.data[i]['id']+'">Preview</a>';
                    columnHtml += '<a class="btn btn-primary btn-sm ml-1" data-title="{{ _lang('Edit') }}" href="{{url('id_system/requests/edit')}}/'+json.data[i]['id']+'">Edit</a>';
                    columnHtml += '</div>';
                    json.data[i]['id'] = columnHtml;
                    // Combine profile picture and member name in one column
                    let profilePic = json.data[i]['profile_pic'] !== null
                        ? '/uploads/profile/' + json.data[i]['profile_pic']
                        : '/images/avatar-classic.png';
                    let memberName = json.data[i]['name_on_id'] || '';
                    json.data[i]['member_profile'] = '<div class="d-flex align-items-center">'
                        + '<img src="'+profilePic+'" class="profile-pic-square mr-2" alt="Profile"/>'
                        + '<span>'+memberName+'</span>'
                        + '</div>';
                    
                    switch(json.data[i]['status']){
                        case 'Pending':
                            json.data[i]['status'] = '<span class="badge badge-info">Pending</span>';
                        break
                        case 'Processing':
                            json.data[i]['status'] = '<span class="badge badge-warning">Processing</span>';
                        break
                        case 'Failed':
                            json.data[i]['status'] = '<span class="badge badge-danger">Failed</span>';
                        break
                        case 'Processed':
                            json.data[i]['status'] = '<span class="badge badge-success">Processed</span>';
                        break
                        default:
                            json.data[i]['status'] = '<span class="badge badge-primary">'+json.data[i]['status']+'</span>';
                    }
                }
                return json.data;
            }
        },
        'columns': [
            { data: 'id' },
            { data: 'member_profile' },
            { data: 'id_number' },
            { data: 'member.brgy' },
            { data: 'member.alliance' },
            { data: 'member.affiliation' },
            { data: 'template.name' },
            { data: 'download_count' },
            { data: 'downloader.full_name' },
            { data: 'last_downloaded_at' },
            { data: 'created_at' },
            { data: 'updated_at' },
            { data: 'remarks' },
        ]
    });

});

///////////////////START DOWNLOAD IDS PER PAGE / SELECTED IDS (RECURSIVE) /////////////////////

function downloadIDByPage(page = 1, part = 0)
{
    let dl_url = "{{url('id_system/requests/generate-id-per-page')}}?page=" + page;
    if(part == 1) {
        dl_url += "&part=1";
    }
    $.ajax({
        url : dl_url + "&" + buildFilterQuery(),
        type: "get",
        success: function(data)
        {
            $("#download-id-modal .output-area").html(data);
            convertHtmlToCanvas(data, page);
        },
        error: function (jqXHR)
        {
            swal("Download failed!", {
                icon: "error",
            });
        }
    });
}

function downloadBySelectedIds(selected_ids, page, part = 0)
{
    let chunk_size = part == 2 ? 5 : 10;
    let last_page = Math.ceil(selected_ids.length / chunk_size);
    let cursor = 1;
    for (let i = 0; i < selected_ids.length; i += chunk_size) {
        if(page != cursor) {
            cursor += 1;
            continue;
        }
        let ids_chunk = selected_ids.slice(i, i + chunk_size);
        let query_str = "?selected_ids=" + encodeURI(selected_ids);
        let dl_url = "{{url('id_system/requests/generate-multi-ids')}}?selected_ids=" + encodeURI(ids_chunk) + "&batch_number=" + page + "&last_page=" + last_page;
        dl_url += "&part=" + part;

        let progress_val = (i / selected_ids.length) * 100;
        $("#download-id-title").html( i + "/" + selected_ids.length );
        $("#download-bar").css("width", progress_val + "%");
       
        $.ajax({
            url : dl_url,
            type: "get",
            success: function(data)
            {
                $("#download-id-modal .output-area").html(data);
                convertHtmlToCanvas(data, page, selected_ids, part);
            },
            error: function (jqXHR)
            {
                swal("Download failed!", {
                    icon: "error",
                });
            }
        });
        cursor += 1;
    }
}

function convertHtmlToCanvas(data, page, selected_ids = [], part = 0) {
    var cards = $(".card-area");
    var current = 1;
    var total = cards.length;
    cards.each(function () { 
        var doc_id = $(this).data("id");
        var doc_file_name = $(this).data("file-name");
        var canvass_part = $(this).data("part");
        var id_canvass_id = "#id-canvass-" + doc_id;
        var id_markup_id = "#id-markup-" + doc_id;
        if(part == 2) {
            id_canvass_id = "#id-canvass-" + doc_id + "-" + canvass_part;
            id_markup_id = "#id-markup-" + doc_id + '-' + canvass_part;
        }

        html2canvas(document.querySelector(id_canvass_id), {width: 750, logging: false}).then(canvas => {
            $(document).find(id_canvass_id).append(canvas);
            $(id_canvass_id + " canvas")
                .attr("data-id", doc_id)
                .attr("data-file-name", doc_file_name)
                .css({width: "1012px", height: "636px"});
            $(id_markup_id).remove();
            convertCardsToA4(current++, total, page, selected_ids, part);
        });
    });
}

function convertCardsToA4(current, total, page, selected_ids = [], part = 0){
    if(current === total) {
        html2canvas(document.querySelector(".paper-area"), {width: 2480, logging: false}).then(canvas => {
            let last_page = $(document).find(".paper-area").data("last-page");
            $(document).find(".download-area").append(canvas);
            $(".paper-area").remove();
            downloadZip(page, last_page, selected_ids, part);
        });
    }
}

var zip = new JSZip();
var img = zip.folder("images");

function downloadZip(page, last_page, selected_ids = [], part = 0){
    let next_page = page + 1;
    $(".download-area canvas").each( function () {
        var canv = $(this);
        var imgDataRaw = canv[0].toDataURL("image/png");
        var imgData = imgDataRaw.replace("data:image/png;base64,", "");
        img.file(page + ".png", imgData, {base64: true});
    });
    if(page >= last_page) {   
        zip.generateAsync({type:"blob"}).then(function(content) {
            saveAs(content, "{{date('Y-m-d H:i:s')}}-member-ids.zip");
        });
        $("#download-bar").css("width", "0%");
        $("#download-id-title").html("1/1");
        $("#download-id-modal").modal("hide");
    } else {
        // recursive approach
        if(selected_ids.length > 0) {
            downloadBySelectedIds(selected_ids, next_page, part)
        } else {
            downloadIDByPage(next_page); 
        }
    }
}

///////////////////END DOWNLOAD IDS PER PAGE/////////////////////

function printImage(img) {
    var html  = '<html><head>' +
        '</head>' +
        '<body style="-webkit-print-color-adjust:exact;" layout="portrait" leftmargin="0" topmargin=0" rightmargin="0" bottommargin="0" marginwidth="0" marginheight=0">'+
        '<img src="'+ img +'" style="width:100%;display: block;" onload="javascript:window.print();"/>' +
        '</body></html>';
    var win = window.open("about:blank","_blank");
    win.document.write(html);
}

function buildFilterQuery() {
    let filter_date_from = $('#filter_date_from').val();
    let filter_date_to = $('#filter_date_to').val();
    let filter_template = $('#filter_template').find('option:selected').val();
    let filter_id_number = $('#filter_id_number').val();
    let filter_show = $('#filter_show').val();
    
    let filter_field = $('#filter_field').val();
    let filter_search = $('#filter_search').val();
    let filter_affiliation = $('#filter_affiliation').val();
    let filter_alliance = $('#filter_alliance').val();
    let search_url_arr = [];
    
    if(filter_date_from.length > 0){
        search_url_arr.push("filter[date_from]=" + filter_date_from);
    }
    if(filter_date_to.length > 0){
        search_url_arr.push("filter[date_to]=" + filter_date_to);
    }
    if(filter_template.length > 0){
        search_url_arr.push("filter[template_id]=" + filter_template);
    }
    if(filter_id_number.length > 0){
        search_url_arr.push("filter[id_number]=" + filter_id_number);
    }
    if(filter_field.length > 0){
        search_url_arr.push("filter[filter_field]=" + filter_field);
    }
    if(filter_search.length > 0){
        search_url_arr.push("filter[filter_search]=" + filter_search);
    }
    if(filter_affiliation.length > 0){
        search_url_arr.push("filter[affiliation]=" + filter_affiliation);
    }
    if(filter_alliance.length > 0){
        search_url_arr.push("filter[alliance]=" + filter_alliance);
    }
    if(filter_show.length > 0){
        search_url_arr.push("filter[show]=" + filter_show);
    }
    
    return search_url_arr.join('&');
}

</script>
@endsection