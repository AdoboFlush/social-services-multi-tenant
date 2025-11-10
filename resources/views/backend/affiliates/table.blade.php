<table class="table table-striped" id="affiliatesTable">
    <thead>
    <tr>
        <th>{{ _lang("Account Number") }}</th>
        <th>{{ _lang("Account Name") }}</th>
        <th>{{ _lang("Email") }}</th>
        <th>{{ _lang("Affiliate Code") }}</th>
        <th>{{ _lang("Parent Affiliate Code") }}</th>
        <th>{{ _lang("Total No. of Members") }}</th>
        <th>{{ _lang("Date Registered") }}</th>
        <th>{{ _lang("Referral Link") }}</th>
        <th class="text-right pr-5">{{ _lang("Action") }}</th>
    </tr>
    </thead>
    <tbody>
        @foreach($affiliates as $affiliate)
        <tr>
            <td><a href="{{ route('users.edit',$affiliate->user->account_number) }}" target="_blank" data-account_number="{{ $affiliate->user->account_number }}" data-user_id="{{ $affiliate->user->id }}" class="account_number_link">{{ $affiliate->user->account_number }}</a></td>
            <td>{{ $affiliate->user->first_name . " " . $affiliate->user->last_name }}</td>
            <td>{{ $affiliate->user->email }}</td>
            <td>{{ $affiliate->code }}</td>
            <td>{{ $affiliate->parent_code }}</td>
            <td>{{ $affiliate->members->count() }}</td>
            <td>{{ $affiliate->created_at }}</td>
            <td class="text-break-all">{{ url('register?ref=' . md5($affiliate->user->id)) }}</td>
            <td class="text-right">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ _lang('Action') }}
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <button data-href="{{ url("admin/affiliates/show/")."/".$affiliate->code }}" class="dropdown-item dropdown-edit"><i class="mdi mdi-pencil"></i> {{ _lang('View / Edit') }}</button>
                        @can('affiliates_delete')
                        <form action="{{ url("admin/affiliates/delete/")."/".$affiliate->code }}" method="post">
                            @csrf
                            <button class="btn-remove dropdown-item" type="button"><i class="mdi mdi-delete"></i> {{ _lang('Delete') }}</button>
                        </form>
                        @endcan
                    </div>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>