<div id="affiliate_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ _lang('Add New Affiliate') }}</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td><label for="account_number">{{ _lang("Account Number") }}</label></td>
                                    <td><input type="text" class="form-control" id="account_number" name="account_number" autocomplete="off"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-12 text-right">
                        <button type="button" class="btn btn-primary px-4" id="next">{{ _lang('Next') }}</button>
                        <button type="button" class="btn btn-secondary px-3" data-dismiss="modal">{{ _lang('Cancel') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>