<form method="post" class="validate" style="width:100%;" autocomplete="off" action="{{ url('social_service/update') }}" enctype="multipart/form-data">
    {{ csrf_field() }}

    <input type="hidden" name="releasing" value="1" />
    <input type="hidden" name="social_service_id" value="{{ $socialService['id'] }}">

    <div class="row">
        <div class="col-md-6">
            <div class="row" style="overflow-y:auto;max-height:500px;">
                <div class="col-md-12">
                    <div class="font-weight-bold mb-3">Requestor Information</div>
                    <table class="table table-sm table-bordered">
                        <tr><td><strong>Requestor First Name</strong></td><td>{{ !empty($socialService['requestor_first_name']) ? $socialService['requestor_first_name'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Requestor Middle Name</strong></td><td>{{ !empty($socialService['requestor_middle_name']) ? $socialService['requestor_middle_name'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Requestor Last Name</strong></td><td>{{ !empty($socialService['requestor_last_name']) ? $socialService['requestor_last_name'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Requestor Suffix</strong></td><td>{{ !empty($socialService['requestor_suffix']) ? $socialService['requestor_suffix'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Requestor Relationship To Beneficiary</strong></td><td>{{ !empty($socialService['requestor_relationship_to_beneficiary']) ? $socialService['requestor_relationship_to_beneficiary'] : 'N/A' }}</td></tr>
                    </table>
        
                    <div class="font-weight-bold mb-3">Request Information</div>
                    <table class="table table-sm table-bordered">
                        <tr><td><strong>Control Number</strong></td><td>{{ !empty($socialService['control_number']) ? $socialService['control_number'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Request Type</strong></td><td>{{ !empty($socialService['request_type']) ? $socialService['request_type'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Purpose</strong></td><td>{{ !empty($socialService['purpose_text']) ? $socialService['purpose_text'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Referred By</strong></td><td>{{ !empty($socialService['referred_by']) ? $socialService['referred_by'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Received By</strong></td><td>{{ !empty($socialService['received_by']) ? $socialService['received_by'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Processed By</strong></td><td>{{ !empty($socialService['processed_by']) ? $socialService['processed_by'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Approved By</strong></td><td>{{ !empty($socialService['approver']['full_name']) ? $socialService['approver']['full_name'] : 'N/A' }}</td></tr>
                        <tr><td><strong>File Date</strong></td><td>{{ !empty($socialService['file_date']) ? $socialService['file_date'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Processed Date</strong></td><td>{{ !empty($socialService['processed_date']) ? $socialService['processed_date'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Approved Date</strong></td><td>{{ !empty($socialService['approved_date']) ? $socialService['approved_date'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Received Date</strong></td><td>{{ !empty($socialService['received_date']) ? $socialService['received_date'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Release Date</strong></td><td>{{ !empty($socialService['release_date']) ? $socialService['release_date'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Amount</strong></td><td>{{ !empty($socialService['amount']) ? $socialService['amount'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Encoder</strong></td><td>{{ !empty($socialService['encoder']['full_name']) ? $socialService['encoder']['full_name'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Status</strong></td><td>{{ !empty($socialService['status']) ? $socialService['status'] : 'N/A' }}</td></tr>
                    </table>
        
                </div>
        
                <div class="col-md-12">
                    <div class="font-weight-bold mb-3">Beneficiary Information</div>
                    <table class="table table-sm table-bordered">
                        <tr><td><strong>First Name</strong></td><td>{{ !empty($socialService['first_name']) ? $socialService['first_name'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Middle Name</strong></td><td>{{ !empty($socialService['middle_name']) ? $socialService['middle_name'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Last Name</strong></td><td>{{ !empty($socialService['last_name']) ? $socialService['last_name'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Suffix</strong></td><td>{{ !empty($socialService['suffix']) ? $socialService['suffix'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Contact Number</strong></td><td>{{ !empty($socialService['contact_number']) ? $socialService['contact_number'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Brgy</strong></td><td>{{ !empty($socialService['brgy']) ? $socialService['brgy'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Address</strong></td><td>{{ !empty($socialService['address']) ? $socialService['address'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Organization</strong></td><td>{{ !empty($socialService['organization']) ? $socialService['organization'] : 'N/A' }}</td></tr>
                        <tr><td><strong>Is a Voter?</strong></td><td>{{ $socialService['is_voter'] === 1 ? 'Yes' : 'No' }}</td></tr>
                    </table>
                    <div class="font-weight-bold mb-3">Remarks</div>
                    <div class="card">
                        <div class="card-body">
                            {{ !empty($socialService['remarks']) ? $socialService['remarks'] : 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-md-6">

            <div class="row">

                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">Amount</label>
                        <input type="number" class="form-control" name="amount" id="amount" value="{{ !empty($socialService['amount']) ? $socialService['amount'] : 0 }}" required>
                        <span class="err-message">Amount is required.</span>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">Received By</label>
                        <input type="text" class="form-control" name="received_by" id="received_by" value="{{ !empty($socialService['received_by']) ? $socialService['received_by'] : '' }}" required>
                        <span class="err-message">Received By is required.</span>
                    </div>
                </div>
        
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">Date Received</label>
                        <input type="text" class="form-control datepicker" name="received_date" id="received_date" value="{{ !empty($socialService['received_date']) ? $socialService['received_date'] : '' }}" required>
                        <span class="err-message">Date Processed is required.</span>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-save">Save</button>
                        <button type="reset" class="btn btn-danger">Reset</button>						
                    </div>
                </div>
        
            </div>
            
        </div>

    </div>

</form>