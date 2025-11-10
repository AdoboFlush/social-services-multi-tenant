<div id="canned-message-form-modal" class="modal fade" role="dialog">
    <div class="modal-dialog fullscreen-modal">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header px-4">
                <h4 class="modal-title">{{ _lang('Canned Messages') }}</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4">
                <form method="post" autocomplete="off" action="{{ url("admin/ticket/canned-message") }}" enctype="multipart/form-data" id="canned-message-form">
                    @csrf
                    <input type="hidden" id="language" name="language" value="EN">
                    <div class="form-group">
                        <label class="control-label" for="name">{{ _lang('Name') }}</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <span class="err-message">{{ _lang('Message is required.') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="message">{{ _lang('Message') }}</label>
                        <textarea class="form-control summernote-simple" id="message" rows="10" name="message" required></textarea>
                        <span class="err-message">{{ _lang('Message is required.') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="internal_note">{{ _lang('Internal Note') }}</label>
                        <textarea class="form-control" rows="3" id="internal_note" name="internal_note"></textarea>
                        <span class="err-message">{{ _lang('Internal Note is required.') }}</span>
                    </div>
                </form>
                <div class="details row d-none">
                    <div class="col-2">
                        Date Created:
                    </div>
                    <div class="col-4" id="created_at"></div>
                    <div class="col-2">
                        Created By:
                    </div>
                    <div class="col-4" id="created_by"></div>
                    <div class="col-2">
                        Date Updated:
                    </div>
                    <div class="col-4" id="updated_at"></div>
                    <div class="col-2">
                        Updated By:
                    </div>
                    <div class="col-4" id="updated_by"></div>
                </div>
                <div class="my-4">
                    <button type="button" id="btn-create-canned-message" class="btn btn-primary">{{ _lang('Save') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ _lang('Back') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>