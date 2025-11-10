<table class="table table-bordered">
    <tr><td><strong>{{ _lang('Log Name') }}</strong></td><td>{{ $data['log_name'] }}</td></tr>
    <tr><td><strong>{{ _lang('Description') }}</strong></td><td>{{ $data['description'] }}</td></tr>
    <tr><td><strong>{{ _lang('Causer') }}</strong></td><td>{{ isset($data['causer']['full_name']) ? $data['causer']['full_name'] : 'N/A' }}</td></tr>
    <tr><td><strong>{{ _lang('Created At') }}</strong></td><td>{{ $data['created_at'] }}</td></tr>
    <tr><td><strong>{{ _lang('Properties') }}</strong></td><td><pre>@php print_r($data['properties']->toArray()) @endphp</pre></td></tr>
</table>