@extends('layouts.tagger')

@section('content-header')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">Activity Logs</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item active">Activity Logs</li>
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
            <h3 class="card-title">Show activity of Tagging users</h3>
          </div>
          <div class="card-body">

            <div class="row">

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">{{ _lang('Causer') }}</label>
                        <select class="form-control select2" name="causer_id" id="causer_id">
                            <option value="">Please select causer</option>
                            @foreach($taggers as $tagger)
                            <option value="{{$tagger->id}}">{{$tagger->first_name}} {{$tagger->last_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">{{ _lang('Filter Field') }}</label>
                        <select class="form-control select2" name="filter_field" id="filter_field">
                            <option value="">Search Field</option>
                            <option value="log_name">Log Name</option>                               
                            <option value="description">Desciption</option>                                
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">{{ _lang('Search Value') }}</label>
                        <input type="text" class="form-control" name="filter_search" id="filter_search" placeholder="Search" />
                    </div>
                </div>
              
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">{{ _lang('Date From') }}</label>
                        <input type="text" class="form-control datepicker" name="filter_date_from" id="filter_date_from" value="{{ isset($params['date_from']) ? $params['date_from'] : ''  }}">
                        <span class="err-message">{{ _lang('Date From is required') }}</span>
                    </div>
                </div>
            
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">{{ _lang('Date To') }}</label>
                        <input type="text" class="form-control datepicker" name="filter_date_to" id="filter_date_to" value="{{ isset($params['date_to']) ? $params['date_to'] : ''  }}">
                        <span class="err-message">{{ _lang('Date To is required') }}</span>
                    </div>
                </div>
        
                <div class="col-md-1">
                    <div class="form-group">
                        <label class="control-label">--</label>
                        <button class="btn btn-primary form-control" id="btn-search" type="button"><i class="fa fa-search mr-2"></i>Search</button>
                        <span class="err-message">{{ _lang('filter_field is required') }}</span>
                    </div>
                </div>
                
            </div>

            <div class="table-responsive">
              <table id="ajax-table" class="table table-bordered table-striped">
                  <thead>
                      <tr>
                        <th>Log Name</th>
                        <th>Causer</th>
                        <th>Description</th>
                        <th>Date Created</th>
                        <th>Actions</th>
                      </tr>
                  </thead>
                  <tfoot>
                  </tfoot>
                  <tbody>
                  </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

</div>

@endsection

@section('js-script')
<script>
$(function () {



    $('#btn-search').on('click', function(e){

        let search_data_arr = [];

        let filter_date_from = $('#filter_date_from').val();
        let filter_date_to = $('#filter_date_to').val();

        let field_name = $('#filter_field').val();
        let search_value = $('#filter_search').val();

        let filter_causer_id = $('#causer_id').find('option:selected').val();

        let search_url_arr = [];
        if(filter_date_from.length > 0){
            search_url_arr.push("filter[date_from]=" + filter_date_from);
        }
        if(filter_date_to.length > 0){
            search_url_arr.push("filter[date_to]=" + filter_date_to);
        }

        if (field_name && search_value) {
            search_data_arr.push({
                key: field_name,
                value: search_value
            });
        }

        if (filter_causer_id){
          search_data_arr.push({
            key: 'causer_id',
            value: filter_causer_id
          })
        }

        let search_query_params = search_data_arr.map((data) => "filter[" + data.key + "]=" + data.value).join("&");
        if (search_query_params) {
            $('#ajax-table').DataTable().ajax.url("?" + search_query_params).load();
        } else {
            $('#ajax-table').DataTable().ajax.url("").load();
        }

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
        'searching': false,
        'ordering': false,
        'serverMethod': 'post',
        'ajax': {
            'url':'{{url("voters/tagging/activity_log")}}',
            'data':{
                '_token' : '{{csrf_token()}}'
            },
            "dataSrc": function ( json ) {
                for ( var i=0, ien=json.data.length ; i<ien ; i++ ) {
                  let columnHtml = '<div class="btn-group">';
                    columnHtml += '<a class="btn btn-success btn-sm ajax-modal" href="#" data-title="{{ _lang('Show Activity') }}" data-href="{{url('voters/tagging/activity_log/show')}}/'+json.data[i]['id']+'">View</a>';
                    columnHtml += '</div>';
                    json.data[i]['id'] = columnHtml;
                }
                return json.data;
            }
        },
        'columns': [
            { data: 'log_name' },
            { data: 'causer.full_name' },
            { data: 'description' },
            { data: 'created_at' },
            { data: 'id' },
        ], 
    });

});
</script>

@endsection