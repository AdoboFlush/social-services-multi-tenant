<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">
                Attendee Information
            </div>
        </div>
        <div class="col-md-6">
            <table class="table table-bordered">
                <tr>
                    <td><strong>{{ _lang('Is Voter') }}</strong></td>
                    <td>{{ !empty($data['is_voter']) && $data['is_voter'] > 0 ? 'YES' : 'NO' }}</td>
                </tr>
                <tr>
                    <td><strong>{{ _lang('First Name') }}</strong></td>
                    <td>{{ !empty($data['first_name']) ? $data['first_name'] : 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>{{ _lang('Middle Name') }}</strong></td>
                    <td>{{ !empty($data['middle_name']) ? $data['middle_name'] : 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>{{ _lang('Last Name') }}</strong></td>
                    <td>{{ !empty($data['last_name']) ? $data['last_name'] : 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>{{ _lang('Suffix') }}</strong></td>
                    <td>{{ !empty($data['suffix']) ? $data['suffix'] : 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>{{ _lang('Brgy') }}</strong></td>
                    <td>{{ !empty($data['brgy']) ? $data['brgy'] : 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>{{ _lang('Address') }}</strong></td>
                    <td>{{ !empty($data['address']) ? $data['address'] : 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>{{ _lang('Birth date') }}</strong></td>
                    <td>{{ !empty($data['birth_date']) ? $data['birth_date'] : 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>{{ _lang('Gender') }}</strong></td>
                    <td>{{ !empty($data['gender']) ? $data['gender'] : 'N/A' }}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-bordered">
                <tr>
                    <td><strong>{{ _lang('Alliance') }}</strong></td>
                    <td>{{ !empty($data['alliance']) ? $data['alliance'] : 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>{{ _lang('Affiliation') }}</strong></td>
                    <td>{{ !empty($data['affiliation']) ? $data['affiliation'] : 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>{{ _lang('Civil status') }}</strong></td>
                    <td>{{ !empty($data['civil_status']) ? $data['civil_status'] : 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>{{ _lang('Beneficiary') }}</strong></td>
                    <td>{{ !empty($data['beneficiary']) ? $data['beneficiary'] : 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>{{ _lang('Religion') }}</strong></td>
                    <td>{{ !empty($data['religion']) ? $data['religion'] : 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>{{ _lang('Age') }}</strong></td>
                    <td>{{ !empty($data['age']) ? $data['age'] : 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>{{ _lang('Contact Number') }}</strong></td>
                    <td>{{ !empty($data['contact_number']) ? $data['contact_number'] : 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>{{ _lang('Remarks') }}</strong></td>
                    <td>{{ !empty($data['remarks']) ? $data['remarks'] : 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>{{ _lang('Precinct') }}</strong></td>
                    <td>{{ !empty($data['precinct']) ? $data['precinct'] : 'N/A' }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">
                Social Services approved or released to this attendee
            </div>
            <table class="table table-bordered">
                <tr>
                    <th>Control Number</th>
                    <th>Request Type</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Approved Date</th>
                    <th>Released Date</th>
                    <th>Status</th>
                </tr>
                @foreach ($socialServices as $socialService)
                <tr>
                    <td>{{$socialService->control_number}}</td>
                    <td>{{$socialService->request_type}}</td>
                    <td>{{$socialService->remarks}}</td>
                    <td>PHP {{ number_format($socialService->amount, 2) }}</td>
                    <td>{{$socialService->processed_date}} </td>
                    <td>{{$socialService->release_date}} </td>
                    <td>{{$socialService->status}}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>