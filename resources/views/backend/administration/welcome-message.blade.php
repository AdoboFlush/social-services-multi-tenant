@extends('layouts.app')


@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">Welcome Message</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item active">Welcome Message</li>
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
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ _lang('Set Welcome / Announcement Message') }}</h3>
                </div>
                <div class="card-body">
                    <form method="post" class="validate" autocomplete="off"
                        action="{{ route('utility.update_welcome_message', ['message' => $message->id], [], false) }}">
                        {{ csrf_field() }}
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <textarea class="form-control summernote-admin" id="message" rows="20"
                                            name="content" required>
                                            {{ $message->content }}
                                        </textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-6">
                                <button type="submit" id="save" class="btn btn-outline-info px-3 mr-2">
                                    {{ _lang('Save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection