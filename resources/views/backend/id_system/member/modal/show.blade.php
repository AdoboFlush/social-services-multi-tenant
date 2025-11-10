<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">Member Information</div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <table class="table table-sm table-bordered">
                <tr><td><strong>{{ _lang('First Name') }}</strong></td><td>{{ !empty($data['first_name']) ? $data['first_name'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Middle Name') }}</strong></td><td>{{ !empty($data['middle_name']) ? $data['middle_name'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Last Name') }}</strong></td><td>{{ !empty($data['last_name']) ? $data['last_name'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Suffix') }}</strong></td><td>{{ !empty($data['suffix']) ? $data['suffix'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Brgy') }}</strong></td><td>{{ !empty($data['brgy']) ? $data['brgy'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Address') }}</strong></td><td>{{ !empty($data['address']) ? $data['address'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Birth date') }}</strong></td><td>{{ !empty($data['birth_date']) ? $data['birth_date'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Gender') }}</strong></td><td>{{ !empty($data['gender']) ? $data['gender'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Precinct') }}</strong></td><td>{{ !empty($data['precinct']) ? $data['precinct'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Age') }}</strong></td><td>{{ !empty($data['age']) ? $data['age'] : 'N/A' }}</td></tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-sm table-bordered">
                @if(isset($data['voter']) && !empty($data['voter']))
                    <tr><td><strong>{{ _lang('Alliance') }}</strong></td><td>{{ !empty($data['voter']['alliance']) ? $data['voter']['alliance'] : 'N/A' }}</td></tr>
                    <tr><td><strong>{{ _lang('Affiliation') }}</strong></td><td>{{ !empty($data['voter']['affiliation']) ? $data['voter']['affiliation'] : 'N/A' }}</td></tr>
                    <tr><td><strong>{{ _lang('Sectoral') }}</strong></td><td>{{ !empty($data['voter']['sectoral']) ? $data['voter']['sectoral'] : 'N/A' }}</td></tr>
                    <tr><td><strong>{{ _lang('Beneficiary') }}</strong></td><td>{{ !empty($data['voter']['beneficiary']) ? $data['voter']['beneficiary'] : 'N/A' }}</td></tr>
                    <tr><td><strong>{{ _lang('Religion') }}</strong></td><td>{{ !empty($data['voter']['religion']) ? $data['voter']['religion'] : 'N/A' }}</td></tr>
                    <tr><td><strong>{{ _lang('Remarks') }}</strong></td><td>{{ !empty($data['voter']['remarks']) ? $data['voter']['remarks'] : 'N/A' }}</td></tr>
                    <tr><td><strong>{{ _lang('Party List') }}</strong></td><td>{{ !empty($data['voter']['party_list']) ? $data['voter']['party_list'] : 'N/A' }}</td></tr>
                    <tr><td><strong>{{ _lang('Organization') }}</strong></td><td>{{ !empty($data['voter']['organization']) ? $data['voter']['organization'] : 'N/A' }}</td></tr>
                    <tr><td><strong>{{ _lang('Position') }}</strong></td><td>{{ !empty($data['voter']['position']) ? $data['voter']['position'] : 'N/A' }}</td></tr>
                    <tr><td><strong>{{ _lang('Contact Number') }}</strong></td><td>{{ !empty($data['voter']['contact_number']) ? $data['voter']['contact_number'] : 'N/A' }}</td></tr>
                    <tr><td><strong>{{ _lang('Is Deceased') }}</strong></td><td>{{ isset($data['voter']['is_deceased']) ? ($data['voter']['is_deceased'] ? 'YES' : 'NO') : 'N/A' }}</td></tr>
                @else
                    <tr><td><strong>{{ _lang('Alliance') }}</strong></td><td>{{ !empty($data['alliance']) ? $data['alliance'] : 'N/A' }}</td></tr>
                    <tr><td><strong>{{ _lang('Affiliation') }}</strong></td><td>{{ !empty($data['affiliation']) ? $data['affiliation'] : 'N/A' }}</td></tr>
                    <tr><td><strong>{{ _lang('Civil Status') }}</strong></td><td>{{ !empty($data['civil_status']) ? $data['civil_status'] : 'N/A' }}</td></tr>
                    <tr><td><strong>{{ _lang('Beneficiary') }}</strong></td><td>{{ !empty($data['beneficiary']) ? $data['beneficiary'] : 'N/A' }}</td></tr>
                    <tr><td><strong>{{ _lang('Religion') }}</strong></td><td>{{ !empty($data['religion']) ? $data['religion'] : 'N/A' }}</td></tr>
                    <tr><td><strong>{{ _lang('Contact Number') }}</strong></td><td>{{ !empty($data['contact_number']) ? $data['contact_number'] : 'N/A' }}</td></tr>
                @endif

                <tr><td><strong>{{ _lang('Remarks') }}</strong></td><td>{{ !empty($data['remarks']) ? $data['remarks'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Is Voter') }}</strong></td><td>{{ $data['is_voter'] ? 'YES' : 'NO' }}</td></tr>
                <tr><td><strong>{{ _lang('Has Member Access') }}</strong></td><td>{{ $data['has_member_access'] ? 'YES' : 'NO' }}</td></tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="alert alert-info">In case of Emergency, Please contact:</div>
            <table class="table table-sm table-bordered">
                <tr><td><strong>{{ _lang('First Name') }}</strong></td><td>{{ !empty($data['contact_person_first_name']) ? $data['contact_person_first_name'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Middle Name') }}</strong></td><td>{{ !empty($data['contact_person_last_name']) ? $data['contact_person_last_name'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Last Name') }}</strong></td><td>{{ !empty($data['contact_person_middle_name']) ? $data['contact_person_middle_name'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Suffix') }}</strong></td><td>{{ !empty($data['contact_person_suffix']) ? $data['contact_person_suffix'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Contact #') }}</strong></td><td>{{ !empty($data['contact_person_number']) ? $data['contact_person_number'] : 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Address') }}</strong></td><td>{{ !empty($data['contact_person_address']) ? $data['contact_person_address'] : 'N/A' }}</td></tr>
            </table>
        </div>

        <div class="col-md-6">
            <div class="alert alert-info">Current IDs</div>
            <table class="table table-bordered">
                @foreach($data['id_requests'] as $id_req)
                <tr><td><a href="{{ url('view_member_id/'.$id_req['id_number']) }}" target="__blank">{{ $id_req['created_at']." - ".$id_req['id_number'] }}</a></td></tr>
                @endforeach
            </table>
        </div>

    </div>
</div>