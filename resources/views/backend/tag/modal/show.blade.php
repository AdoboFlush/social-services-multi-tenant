<table class="table table-sm table-bordered">
    @foreach($tag as $key => $value)
        @php
            $value = !empty($value) ? $value : 'N/A';
        @endphp
        <tr><td><strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong></td><td>{{ $value }}</td></tr>
    @endforeach
</table>



