@if(isset($user->affiliate_details->members) && !$user->affiliate_details->members->isEmpty())
<div class="card mb-5">
    <div class="card-body">
        <h4 class="card-title panel-title">{{ _lang('Members') }}</h4>

        <table class="table data-table table-striped">
            <thead>
            <tr>
                <th>{{ _lang('Account Number') }}</th>
                <th>{{ _lang('Account Name') }}</th>
                <th>{{ _lang('Email') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($user->affiliate_details->members as $member)
                <tr>
                    <td><a href="{{ route('users.edit',$member->user->account_number) }}">{{ $member->user->account_number }}</a></td>
                    <td>{{ $member->user->first_name . " " . $member->user->last_name }}</td>
                    <td>{{ $member->user->email }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif