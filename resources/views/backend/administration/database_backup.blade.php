@extends('layouts.app')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">Database Backup</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item active">Database Backup</li>
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
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title"><span class="panel-title">&nbsp</span>
                            @can('database_backup_create')
                            <a class="btn btn-primary btn-sm float-right" href="{{ url("admin/administration/database/backup/create") }}">{{ _lang('Backup') }}</a>
                            @endcan
                        </h4>
                        <table class="table table-striped" id="databaseBackupTable">
                            <thead>
                                <tr>
                                    <th>Date Created</th>
                                    <th>Filename</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($files as $file)
                                <tr>
                                    <td>{{ gmdate("m-d-Y", Storage::lastModified($file)) }}</td>
                                    <td>{{ basename($file) }}</td>
                                    <td class="text-right"><a class="btn btn-primary btn-sm" href='{{ url($file) }}' target='_blank'>Download</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-script')
    <script type="text/javascript">
        $(function() {
            $('#databaseBackupTable').DataTable({
                "order": [[ 1, "desc" ]]
            });
        });
    </script>
@endsection