@extends("backend.administration.settings._layout")

@section("form")
    <div id="fee" class="tab-pane active">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title panel-title">{{ _lang('Fee Settings') }}</h4>
                <table class="table table-striped datatable">
                    <thead>
                    <tr>
                        <th>{{ _lang("Account Number") }}</th>
                        <th>{{ _lang("Account Name") }}</th>
                        <th>{{ _lang("Email") }}</th>
                        <th>{{ _lang("Account Status") }}</th>
                        <th>{{ _lang("Last Update") }}</th>
                        <th>{{ _lang("Updated By") }}</th>
                        <th class="text-right pr-5">{{ _lang("Action") }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($merchants as $merchant)
                        <tr>
                            <td><a href="{{ route('users.edit',$merchant->account_number) }}" target="_blank"  class="account_number_link">{{ $merchant->account_number }}</a></td>
                            <td>{{ ucwords($merchant->first_name) }} {{ ucwords($merchant->last_name) }}</td>
                            <td>{{ $merchant->email }}</td>
                            <td>{{ $merchant->account_status ? $merchant->account_status : "Unverified" }}</td>
                            <td>{{ $merchant->fees->count() ? $merchant->fees()->orderBy("updated_at","desc")->first()->updated_at : "" }}</td>
                            <td>{{ $merchant->fees->count() ? $merchant->fees()->orderBy("updated_at","desc")->first()->updated_by_user->first_name : "" }}</td>
                            <td class="text-right">
                                <div class="dropdown">
                                    <a class="btn btn-secondary btn-sm" type="button" href="{{ route("merchant.fee.view",$merchant->id) }}">
                                        {{ _lang('View / Edit') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js-script')
    <script type="text/javascript">
        $(function(){
            $('.datatable').DataTable({
                "order": []
            });
        });
    </script>
@endsection