<div id="import_modal" class="modal animated bounceInDown" tabindex="-1" role="dialog">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">{{ _lang('Browse Your CSV File') }}</h5>
		
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  
		  <div class="alert alert-danger" style="display:none; margin: 15px;"></div>
		  <div class="alert alert-success" style="display:none; margin: 15px;"></div>			  
		  <div class="modal-body" style="overflow:hidden;">
                <form enctype="multipart/form-data" method="post" id="import_csv_form" autocomplete="off" action="{{ url('admin/import') }}">
				{{ csrf_field() }}
                <div class="form-group mt-4 mb-5">
                    <input type="file" name="csv_file" accept=".csv" required>
					<span class="err-message">{{ _lang('CSV File is required.') }}</span>
                </div>
                <div class="form-group float-right">
                    <button type="button" id="review_csv" class="btn btn-primary">{{ _lang('Extract') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ _lang('Cancel') }}</button>
                </div>
                </form>
            </div>
	    </div>
	</div>
</div>