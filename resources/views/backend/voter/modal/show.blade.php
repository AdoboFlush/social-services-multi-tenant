<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <table class="table table-sm table-bordered">
                <tr><td><strong>Full Name</strong></td><td>{{ $voter['full_name'] ?? 'N/A' }}</td></tr> 
                <tr><td><strong>First Name</strong></td><td>{{ $voter['first_name'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>Middle Name</strong></td><td>{{ $voter['middle_name'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>Last Name</strong></td><td>{{ $voter['last_name'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>Suffix</strong></td><td>{{ $voter['suffix'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>Contact Number</strong></td><td>{{ $voter['contact_number'] ?? 'N/A' }}</td></tr>              
                <tr><td><strong>Gender</strong></td><td>{{ $voter['gender'] ?? 'N/A' }}</td></tr>              
                <tr><td><strong>Birth Date</strong></td><td>{{ $voter['birth_date'] ?? 'N/A' }}</td></tr>             
                <tr><td><strong>Age</strong></td><td>{{ $voter['age'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>Civil Status</strong></td><td>{{ $voter['civil_status'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>Brgy</strong></td><td>{{ $voter['brgy'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>Address</strong></td><td>{{ $voter['address'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>Precinct</strong></td><td>{{ $voter['precinct'] ?? 'N/A' }}</td></tr>       
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-sm table-bordered">
                <tr><td><strong>Alliance</strong></td><td>{{ $voter['alliance'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>Affiliation</strong></td><td>{{ $voter['affiliation'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>Sectoral</strong></td><td>{{ $voter['sectoral'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>Beneficiary</strong></td><td>{{ $voter['beneficiary'] ?? 'N/A' }}</td></tr>   
                <tr><td><strong>Religion</strong></td><td>{{ $voter['religion'] ?? 'N/A' }}</td></tr>                 
                <tr><td><strong>Remarks</strong></td><td>{{ $voter['remarks'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>Party List</strong></td><td>{{ $voter['party_list'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>Organization</strong></td><td>{{ $voter['organization'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>Position</strong></td><td>{{ $voter['position'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>Is Deceased</strong></td><td>{{ isset($voter['is_deceased']) ? ($voter['is_deceased'] ? 'YES' : 'NO') : 'N/A' }}</td></tr>
            </table>
        </div>
    </div>
</div>
