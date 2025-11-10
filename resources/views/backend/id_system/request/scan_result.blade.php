@extends('backend.id_system.layout')

@section('card-header')
<h3 class="card-title">Scan Member ID Result</h3>
<span class="float-right"><a href="{{url("social_services/create/member/".$member['id'])}}" class="btn btn-primary btn-sm" data-title="{{ _lang('Create SS Assistance') }}">Create Social Service Assistance</a></span>
@endsection

@section('tab-content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-success"><i class="fa fa-check mr-2"></i>Member ID Number : {{ $data['id_number'] }} is Valid.</div>
            <div class="alert alert-info">Member Information</div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <table class="table table-sm table-bordered">
                <tr><td><strong>{{ _lang('First Name') }}</strong></td><td>{{ $member['first_name'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Middle Name') }}</strong></td><td>{{ $member['middle_name'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Last Name') }}</strong></td><td>{{ $member['last_name'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Suffix') }}</strong></td><td>{{ $member['suffix'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Brgy') }}</strong></td><td>{{ $member['brgy'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Address') }}</strong></td><td>{{ $member['address'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Birth date') }}</strong></td><td>{{ $member['birth_date'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Gender') }}</strong></td><td>{{ $member['gender'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Precinct') }}</strong></td><td>{{ $member['precinct'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Age') }}</strong></td><td>{{ $member['age'] ?? 'N/A' }}</td></tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-sm table-bordered">
                @if(!empty($voter)) 
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
                    <tr><td><strong>{{ _lang('Is Voter') }}</strong></td><td>YES</td></tr>
                @else
                    <tr><td><strong>{{ _lang('Alliance') }}</strong></td><td>{{ $member['alliance'] ?? 'N/A' }}</td></tr>
                    <tr><td><strong>{{ _lang('Affiliation') }}</strong></td><td>{{ $member['affiliation'] ?? 'N/A' }}</td></tr>
                    <tr><td><strong>{{ _lang('Civil Status') }}</strong></td><td>{{ $member['civil_status'] ?? 'N/A' }}</td></tr>
                    <tr><td><strong>{{ _lang('Beneficiary') }}</strong></td><td>{{ $member['beneficiary'] ?? 'N/A' }}</td></tr>
                    <tr><td><strong>{{ _lang('Religion') }}</strong></td><td>{{ $member['religion'] ?? 'N/A' }}</td></tr>
                    <tr><td><strong>{{ _lang('Contact Number') }}</strong></td><td>{{ $member['contact_number'] ?? 'N/A' }}</td></tr>
                    <tr><td><strong>{{ _lang('Remarks') }}</strong></td><td>{{ $member['remarks'] ?? 'N/A' }}</td></tr>
                    <tr><td><strong>{{ _lang('Is Voter') }}</strong></td><td>NO</td></tr>
                @endif
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="alert alert-info">In case of Emergency, Please contact:</div>
            <table class="table table-sm table-bordered">
                <tr><td><strong>{{ _lang('First Name') }}</strong></td><td>{{ $member['contact_person_first_name'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Middle Name') }}</strong></td><td>{{ $member['contact_person_last_name'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Last Name') }}</strong></td><td>{{ $member['contact_person_middle_name'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Suffix') }}</strong></td><td>{{ $member['contact_person_suffix'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Contact #') }}</strong></td><td>{{ $member['contact_person_number'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Address') }}</strong></td><td>{{ $member['contact_person_address'] ?? 'N/A' }}</td></tr>
            </table>
        </div>

        <div class="col-md-6">
            <div class="alert alert-info">ID Information</div>
            <table class="table table-sm table-bordered">
                <tr><td><strong>{{ _lang('Name on ID') }}</strong></td><td>{{ $data['name_on_id'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Member Account Number') }}</strong></td><td>{{ $member['account_number'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('ID Number') }}</strong></td><td>{{ $data['id_number'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Created At') }}</strong></td><td>{{ $data['created_at'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('Updated At') }}</strong></td><td>{{ $data['updated_at'] ?? 'N/A' }}</td></tr>
                <tr><td><strong>{{ _lang('ID Preview') }}</strong></td><td><a href="{{ url('view_member_id/'.$data['id_number']) }}" target="__blank">{{ $data['created_at']." - ".$data['id_number'] }}</a></td></tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">Social Service Assistances</div>
            <table class="table table-sm table-bordered table-striped">
                <tr>
                    <th>Action</th>
                    <th>Request Type</th>
                    <th>Purpose</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th>File Date</th>
                    <th>Processed Date</th>
                    <th>Release Date</th>
                    <th>Created At</th>
                    <th>Received By</th>
                </tr>

                @foreach($social_services as $social_service)
                <tr>
                    <td><a class="btn btn-warning btn-sm ajax-modal" href="#" data-title="{{ _lang('Show Request Information') }}" data-href="{{url('social_service/show')}}/{{ $social_service->id }}">View</a></td>
                    <td>{{ $social_service->request_type }}</td>
                    <td>{{ $social_service->purpose_text }}</td>
                    <td>
                        <span class="badge
                            @if($social_service->status == 'Pending') badge-info
                            @elseif($social_service->status == 'On-hold') badge-warning
                            @elseif($social_service->status == 'Rejected') badge-danger
                            @elseif($social_service->status == 'Approved') badge-success
                            @elseif($social_service->status == 'Released') badge-primary
                            @elseif($social_service->status == 'For-validation' || $social_service->status == 'For-delete') badge-danger
                            @else badge-secondary
                            @endif
                        ">{{ $social_service->status }}</span>
                    </td>
                    <td>{{ $social_service->amount }}</td>
                    <td>{{ $social_service->file_date }}</td>
                    <td>{{ $social_service->processed_date }}</td>
                    <td>{{ $social_service->release_date }}</td>
                    <td>{{ $social_service->created_at }}</td>
                    <td>{{ $social_service->received_by }}</td>
                </tr>
                @endforeach

            </table>
        </div>
    </div>

</div>

@endsection