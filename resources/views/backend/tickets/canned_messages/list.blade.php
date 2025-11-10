@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">Dashboard</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item"><a href="/">Tickets</a></li>
      <li class="breadcrumb-item active">Canned Messages</li>
      </ol>
    </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
@endsection

@section('content')
<div class="container-fluid">
    <div class="card d-none d-md-block overflow-visible">
        <div class="card-body">
            @php $languages = array("en","jp") @endphp
            <ul class="nav nav-tabs mb-4">
                @foreach($languages as $key => $language)
                <li class="nav-item"><a class="nav-link @if(Session::has("language") && session("language") == $language) active @elseif(!Session::has("language") && $key == 0) active @endif" data-toggle="tab" href="#{{ $language }}">{{ strtoupper($language)." "._lang('Canned Messages') }}</a></li>
                @endforeach
            </ul>
            <div class="tab-content">
                @foreach($languages as $key => $language)
                <div id="{{ $language }}" class="tab-pane @if(Session::has("language") && session("language") == $language) active @elseif(!Session::has("language") && $key == 0) active @endif">
                    <div class="card-title panel-title float-right mb-2">
                        <button class="btn btn-primary btn-sm float-right btn-add" data-lang="{{ $language }}">{{ _lang('Add New') }}</button>
                    </div>
                    <table class="table table-striped datatable">
                        <thead>
                        <tr>
                            <th>{{ _lang("Name") }}</th>
                            <th>{{ _lang("Internal Note") }}</th>
                            <th>{{ _lang("Date Created") }}</th>
                            <th>{{ _lang("Date Updated") }}</th>
                            <th class="text-right pr-5">{{ _lang("Action") }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cannedMessages as $cannedMessage)
                            @if($cannedMessage->language == $language)
                            <tr id="{{ $cannedMessage->id }}">
                                <td>{{ $cannedMessage->name }}</td>
                                <td>{{ $cannedMessage->internal_note }}</td>
                                <td>{{ $cannedMessage->created_at }}</td>
                                <td>{{ $cannedMessage->updated_at }}</td>
                                <td class="text-right">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{ _lang('Action') }}
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <button onclick="edit('{{ url("admin/ticket/canned-message/".$cannedMessage->id) }}','{{ $language }}')" class="dropdown-item dropdown-edit"><i class="mdi mdi-pencil"></i> {{ _lang('View / Edit') }}</button>
                                            <form action="{{ url("admin/ticket/canned-message/delete/".$cannedMessage->id) }}" method="post">
                                                @csrf
                                                <button class="btn-remove dropdown-item" type="button"><i class="mdi mdi-delete"></i> {{ _lang('Delete') }}</button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@include('backend.tickets.canned_messages.form')
@endsection

@section('js-script')
    <script type="text/javascript">
        $(function(){

            $('.datatable').DataTable({
                "order": []
            });

            $(".btn-add").on("click", function (e) {
                var url = "{{ url('admin/ticket/canned-message') }}";
                $("#canned-message-form").trigger("reset");
                $("#canned-message-form .summernote-simple").summernote('code','');
                $("#canned-message-form").attr("action", url);

                $(".details").addClass("d-none").removeClass("d-flex");
                $("#canned-message-form #language").val($(this).data("lang"));
                $("#canned-message-form-modal").modal("show");
            });

            $("#btn-create-canned-message").on('click',function(event){
                var validated = validateFields($('#canned-message-form'));
                if(validated) {
                    $("#preloader").css("display","block");
                    $("#canned-message-form").submit();
                }
            });

            @if(Session::has("canned_response"))
                toastr.success("{{ session("canned_response") }}");
            @endif

            @if(Session::has("canned_error_response"))
                toastr.error("{{ session("canned_error_response") }}");
            @endif
            });

        function edit(url,lang){
            $("#canned-message-form #language").val(lang);
            $.ajax({
                url: url,
                beforeSend: function(){
                    $("#preloader").css("display","block");
                },success: function(data){
                    console.log(data);
                    $("#canned-message-form #name").val(data.message.name);
                    $("#canned-message-form #message").val(data.message.message);
                    $("#canned-message-form .summernote-simple").summernote('code',data.message.message);
                    $("#canned-message-form #internal_note").val(data.message.internal_note);

                    $(".details #created_at").text(data.message.created_at);
                    $(".details #created_by").text(data.message.creator["first_name"] + " " + data.message.creator["last_name"]);
                    $(".details #updated_at").text(data.message.updated_at);
                    $(".details #updated_by").text(data.message.editor["first_name"] + " " + data.message.editor["last_name"]);

                    $(".details").removeClass("d-none").addClass("d-flex");
                    $("#canned-message-form").attr("action", url);
                    $("#preloader").css("display","none");
                }
            });
            $("#canned-message-form #language").val(lang);
            $("#canned-message-form-modal").modal("show");
        }
    </script>
@endsection