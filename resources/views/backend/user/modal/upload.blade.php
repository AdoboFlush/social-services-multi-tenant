<div id="import_document_modal" class="modal animated bounceInDown" tabindex="-1" role="dialog">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">{{ _lang('Browse Your KYC File') }}</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="alert alert-danger" style="display:none; margin: 15px;"></div>
		  <div class="alert alert-success" style="display:none; margin: 15px;"></div>
		  <div class="modal-body" style="overflow:hidden;">
                <h5 class="account-detail text-capitalize"></h5>
                <form enctype="multipart/form-data" method="post" id="upload_kyc_form" autocomplete="off" action="">
					{{ csrf_field() }}
					<label class="control-label mb-0" for="csv_file">{{ _lang("Proof of Identity") }}</label>
					<div class="form-group mt-2">
						@for($i = 1; $i < 4; $i ++)
    	                	<input type="file" accept="image/*,.csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" name="{{ 'identity_'.$i }}" class="mt-2">
						@endfor
					</div>
					<label class="control-label mb-0" for="csv_file">{{ _lang("Proof of Address") }}</label>
					<div class="form-group mt-2">
						@for($i = 1; $i < 4; $i ++)
							<input type="file" accept="image/*,.csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" name="{{ 'address_'.$i }}" class="mt-2">
						@endfor
					</div>
					<div class="form-group float-right">
						<button type="submit" id="review_csv" class="btn btn-primary">{{ _lang('Upload') }}</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ _lang('Cancel') }}</button>
                	</div>
                </form>
            </div>
	    </div>
	</div>
</div>