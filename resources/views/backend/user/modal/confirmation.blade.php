<div id="confirm_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
{{--                <h4>{{ _lang('Internal Transfer (Transfer Funds to Oriental Wallet Users)') }}</h4>--}}
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <label id="modal-message">{{ _lang('Are you sure you want to change all the Unreviewed KYC documents to Approved?') }}</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="button" id="btn-submit" data-action="" class="btn btn-primary confirm-btn">Confirm</button>
                        <button class="btn btn-danger"  data-dismiss="modal">Back</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>