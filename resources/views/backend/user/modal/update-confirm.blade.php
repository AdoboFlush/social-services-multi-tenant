<div id="update_confirm_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
{{--                <h4>{{ _lang('User Document (Status Update)') }}</h4>--}}
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <label id="modal-message">{{ _lang('Are you sure you want to verify the account?') }}</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <a href="{{ url('admin/users/varify/'.$user_id) }}" class="btn btn-primary confirm-btn">{{ _lang('Yes') }}</a>
                        <button class="btn btn-danger"  data-dismiss="modal">{{ _lang('Cancel') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>