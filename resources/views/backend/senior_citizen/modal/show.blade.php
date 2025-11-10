<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <table class="table table-bordered">
                <tr><td><strong>{{ _lang('first_name') }}</strong></td><td>{{ !empty($voter['first_name']) ? $voter['first_name'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('middle_name') }}</strong></td><td>{{ !empty($voter['middle_name']) ? $voter['middle_name'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('last_name') }}</strong></td><td>{{ !empty($voter['last_name']) ? $voter['last_name'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('suffix') }}</strong></td><td>{{ !empty($voter['suffix']) ? $voter['suffix'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('brgy') }}</strong></td><td>{{ !empty($voter['brgy']) ? $voter['brgy'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('address') }}</strong></td><td>{{ !empty($voter['address']) ? $voter['address'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('birth_date') }}</strong></td><td>{{ !empty($voter['birth_date']) ? $voter['birth_date'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('gender') }}</strong></td><td>{{ !empty($voter['gender']) ? $voter['gender'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('precinct') }}</strong></td><td>{{ !empty($voter['precinct']) ? $voter['precinct'] : 'N/A' }}</td></tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-bordered">
                <tr><td><strong>{{ _lang('alliance') }}</strong></td><td>{{ !empty($voter['alliance']) ? $voter['alliance'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('affiliation') }}</strong></td><td>{{ !empty($voter['affiliation']) ? $voter['affiliation'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('civil_status') }}</strong></td><td>{{ !empty($voter['civil_status']) ? $voter['civil_status'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('religion') }}</strong></td><td>{{ !empty($voter['religion']) ? $voter['religion'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('age') }}</strong></td><td>{{ !empty($voter['age']) ? $voter['age'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('contact_number') }}</strong></td><td>{{ !empty($voter['contact_number']) ? $voter['contact_number'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('remarks') }}</strong></td><td>{{ !empty($voter['remarks']) ? $voter['remarks'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('full_name') }}</strong></td><td>{{ !empty($voter['full_name']) ? $voter['full_name'] : 'N/A' }}</td></tr>

            </table>
        </div>
    </div>
</div>


