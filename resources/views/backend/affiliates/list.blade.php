@extends('layouts.app')

@section('content')
    <div class="sb-page-header pb-2 sb-page-header-dark bg-gradient-primary-to-secondary">
        <div class="container-fluid">
            <div class="sb-page-header-content py-3">
                <h1 class="sb-page-header-title">
                    <div class="sb-page-header-icon"><i data-feather="user"></i></div>
                    <span>{{ _lang("Affiliate Management") }}</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-4">
        <div class="card d-none d-md-block">
            <div class="card-body">
                <h4 class="card-title panel-title">{{ _lang("Affiliate Management") }}</h4>
                <div class="filter row">
                    <div class="col-6">
                        <div class="form-group row">
                            <label for="email_address" class="col-sm-4 col-form-label">{{ _lang("Email Address") }}</label>
                            <div class="col-sm-7">
                                <input type="email" class="form-control" id="email_address">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-sm-4 col-form-label">{{ _lang("Name") }}</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" id="name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="registered_date_from" class="col-sm-4 col-form-label">{{ _lang("Registered Date") }}</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control datepicker" name="registered_date_from" id="registered_date_from" readOnly="true" placeholder="From">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="offset-sm-4 col-sm-7">
                                <input type="text" class="form-control datepicker" name="registered_date_to" id="registered_date_to" readOnly="true" placeholder="To">
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group row">
                            <label for="code" class="col-sm-3 col-form-label">{{ _lang("Affiliate Code") }}</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" id="code">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="parent_code" class="col-sm-3 col-form-label">{{ _lang("Parent Affiliate Code") }}</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" id="parent_code">
                            </div>
                        </div>
                        <div class="form-group row mt-5">
                            <button type="button" class="btn btn-primary px-3 mx-1" id="filter">{{ _lang("Filter") }}</button>
                            <button type="button" class="btn btn-secondary px-3 mx-1" id="reset">{{ _lang("Reset") }}</button>
                            <button type="button" class="btn btn-outline-info px-3 mx-1" id="export">{{ _lang("Export") }}</button>
                            <button type="button" class="btn btn-outline-warning px-3 mx-1" id="add">{{ _lang("Add New") }}</button>
                        </div>
                    </div>
                </div>
                <div class="text-center border-bottom py-3"></div>
                <div class="table-container">
                    @include('backend.affiliates.table')
                </div>
            </div>
        </div>
    </div>
    @include('backend.affiliates.modals.create')
@endsection

@section('js-script')
    <script type="text/javascript">
        $(function(){
            var affiliatesTable = $('#affiliatesTable').DataTable({
                "order": [],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'csv',
                        className: 'd-none',
                        title: 'Affiliates',
                        exportOptions: {
                            columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ]
                        }
                    }
                ]
            });

            $('#add').on("click", function() {
                $("#account_number").val('');
                $("#affiliate_modal").modal("show");
            });

            $(".table").on("click", ".dropdown-edit", function() {
                var url =  $(this).data("href");
                edit(url);
            });

            $("#export").on("click",function(){
                $(".buttons-csv").trigger("click");
            });

            $('#next').on("click",function() {
                var acc_num = $("#account_number").val();
                if(acc_num){
                    $.ajax({
                        url: '{{ route("search.affiliate", [], false) }}',
                        method: 'POST',
                        data:{
                            "_token": "{{ csrf_token() }}",
                            "account_number": acc_num
                        },
                        beforeSend: function(){
                            $("#preloader").css("display","block");
                        },success: function(data){
                            setTimeout(function(){
                                if(data.success){
                                    $("#preloader").css("display","none");
                                    $('.modal').modal('hide');
                                    $('#main_modal .modal-title').text("Add New Affiliate");
                                    $('#main_modal .modal-body').html(data.modal);
                                    $('#main_modal').modal('show');
                                    $('#back').bind('click',function(){
                                        $('.modal').modal('hide');
                                        $("#affiliate_modal").modal("show");
                                    });
                                    $('#confirm').bind('click',function(){
                                        confirm();
                                    })
                                } else {
                                    $("#preloader").css("display","none");
                                    toastr.error(data.message);
                                }

                            }, 500);
                        },
                        error: function (request, status, error) {
                            setTimeout(function(){
                                $("#preloader").css("display","none");
                                toastr.error("An Error Occurred ");
                            }, 500);
                        }
                    });
                } else {
                    toastr.error("{{ _lang('Invalid account number') }}")
                }
            });

            function edit(url){
                $.ajax({
                    url: url,
                    beforeSend: function(){
                        $("#preloader").css("display","block");
                    },success: function(data){
                        setTimeout(function(){
                            if(data.success){
                                $("#preloader").css("display","none");
                                $('.modal').modal('hide');
                                $('#main_modal .modal-title').text("Affiliate Details");
                                $('#main_modal .modal-body').html(data.modal);
                                $('#main_modal').modal('show');
                                $('#back').bind('click',function(){
                                    $('.modal').modal('hide');
                                    $("#affiliate_modal").modal("show");
                                });
                                $('#save').bind('click',function(){
                                    confirm();
                                })
                            } else {
                                $("#preloader").css("display","none");
                                toastr.error(data.message);
                            }

                        }, 500);
                    }
                });
            }

            function confirm(){
                $.ajax({
                    url: '{{ route("create.affiliate", [], false) }}',
                    method: 'POST',
                    data:{
                        "_token": "{{ csrf_token() }}",
                        "parent_code": $("#parent_code").val(),
                        "integration_url": $("#integration_url").val(),
                        "sid": $("#sid").val(),
                        "code": $("#code").val(),
                        "user_id": $("#user_id").val(),
                        "kyc_privilege_switch": $('#kyc_privilege_switch').is(':checked') ? 1 : 0,
                        "referral_switch": $('#referral_switch').is(':checked') ? 1 : 0
                    },
                    beforeSend: function(){
                        $("#preloader").css("display","block");
                    },success: function(data){
                        setTimeout(function(){
                            if(data.success){
                                $('.modal').modal('hide');
                                $(".table-container").html(data.table);
                                affiliatesTable = $('#affiliatesTable').DataTable({
                                    "order": [],
                                    dom: 'Bfrtip',
                                    buttons: [
                                        {
                                            extend: 'csv',
                                            className: 'd-none',
                                            title: 'Affiliates',
                                            exportOptions: {
                                                columns: [ 0, 1, 2, 3, 4, 5, 6 ]
                                            }
                                        }
                                    ]
                                });
                                $("#preloader").css("display","none");
                                toastr.success(data.message);
                                $(".table").on("click", ".dropdown-edit", function() {
                                    var url =  $(this).data("href");
                                    edit(url);
                                });
                            } else {
                                $("#preloader").css("display","none");
                                toastr.error(data.message);
                            }
                        }, 500);
                    },
                    error: function (request, status, error) {
                        setTimeout(function(){
                            $("#preloader").css("display","none");
                            toastr.error("An Error Occurred ");
                        }, 500);
                    }
                });
            }

            $('#filter').on("click", function() {
                search();
                affiliatesTable.draw();
            });

            $('#reset').on("click", function() {
                $(".filter input[type='text'], .filter input[type='email']").val('');
                search();
                affiliatesTable.draw();
            });


            $.fn.dataTable.ext.search.push(
                    function (settings, data, dataIndex) {
                        var min = $('#registered_date_from').val() != '' ? $('#registered_date_from').datepicker('getDate') : "Invalid Date";
                        var max = $('#registered_date_to').val() != '' ? $('#registered_date_to').datepicker('getDate') : "Invalid Date";
                        var startDate = new Date(data[6].split(' ')[0]);
                        startDate.setHours(0,0,0,0);
                        if (min == "Invalid Date" && max == "Invalid Date") return true;
                        if (min == "Invalid Date" && startDate <= max) return true;
                        if (max == "Invalid Date" && startDate >= min) return true;
                        if (startDate <= max && startDate >= min) return true;
                        return false;
                    }
            );

            function search(){
                affiliatesTable.column(2).search($('#email_address').val(), false, true);
                affiliatesTable.column(1).search($('#name').val(), false, true);
                affiliatesTable.column(3).search($('#code').val(), false, true);
                affiliatesTable.column(4).search($('#parent_code').val(), false, true);
            }

            @if(Session::has("affiliate_response"))
            toastr.success("{{ session("affiliate_response") }}");
            @endif

            @if(Session::has("affiliate_error_response"))
            toastr.error("{{ session("affiliate_error_response") }}");
            @endif

        });
    </script>
@endsection