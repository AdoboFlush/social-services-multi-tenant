<script id="mustache-table" type="text/x-handlebars-template">
    <div class="table-container">
	    <table class="table table-striped">
            <tr>
                <th style="width: 20px;">
                    <input class="form-check-input m-0 p-0 position-relative" type="checkbox" name="update_all">
				</th>
                <th>Account Number</th>
                <th>Account Name</th>
                <th>Email</th>
                <th class="text-center">KYC Status</th>
                <th class="text-center">Account Status</th>
                <th>Date of Last Upload</th>
                <th>Date of Status Change</th>
                <th class="text-center">View</th>
            </tr>
            @verbatim
            {{#each items}}
            <tr id="row_{{ this.id }}">
				<td>
					<input class="form-check-input m-0 p-0 position-relative" type="checkbox" value="{{ this.id  }}" name="update_checked[]">
				</td>
                <td class='account_number'>
                    <a href="/admin/users/edit/{{ this.account_number}}" target="_blank" data-account_number="{{ this.account_number }}" data-user_id="{{ this.id }}" onclick="logViewUser(this)">{{ this.account_number }}</a>
                </td>
                <td class='account_name'>{{ this.full_name }}</td>
				<td class='email'>{{ this.email }}</td>
				<td class='text-center'>
                    {{#ifCond this.kyc_status null }}
                        {{#ifCond this.account_status 'Verified' }}
                            {{#ifCond this.document_submitted_at null }}

                            {{else}}
                                Approved
                            {{/ifCond}}
                        {{else}}
                            Unreviewed
                        {{/ifCond}}
                    {{else}}
                        {{ this.kyc_status }}
                    {{/ifCond}}
				</td>
				<td class='text-center'>
					{{#ifCond this.is_dormant 1 }}
						{{ hStatusClass "Dormant" }}
					{{else}}
						{{ hStatusClass this.account_status }}
					{{/ifCond}}
				</td>
				<td class='date_last_upload'>
                    {{#ifCond this.document_submitted_at null }}
                        0000-00-00 00:00:00
                    {{else}}
                        {{ this.document_submitted_at }}
                    {{/ifCond}}
                </td>
                <td class='date_last_upload'>
                    {{#ifCond this.kyc_status_updated_at null }}
                        0000-00-00 00:00:00
                    {{else}}
                        {{ this.kyc_status_updated_at}}
                    {{/ifCond}}
                </td>
				<td class="text-center">
					<a class="btn btn-info btn-sm view_document" data-user_id="{{ this.id }}" data-url="documents/'{{ this.id }}" href="documents/{{ this.id }}">View Documents</a>
				</td>
			</tr>
            {{/each}}
        </table>
    </div>
    <div class="row">
		<div class="col-6">
			<span class="text-muted">Showing {{ hCurrent page.current  }} to {{ page.page_to }} of {{ page.total }} entries </span>
		</div>
		<div class="col-6 text-right">
			<nav aria-label="Page navigation example">
			  <ul class="pagination justify-content-end">
			  	{{#ifCond page.current 1}}
			  		<li class="page-item disabled">
				      <span class="page-link">Prev</span>
				    </li>
			  	{{else}}
			  		<li class="page-item">
				      <a class="page-link" href="javascript:;" tabindex="-1" data-page="{{ hSubtract page.current 1 }}">Prev</a>
				    </li>
			  	{{/ifCond}}

			  	{{#ifCond page.in_first false}}
			  		<li class="page-item">
				      <a class="page-link" href="javascript:;" tabindex="-1" data-page="1">1</a>
				    </li>
				    <li class="page-item disabled">
				      <span class="page-link">...</span>
				    </li>
			  	{{/ifCond}}

			    {{#each page.pages}}			    	
		  			{{#ifCond this ../page.current}}
		  			<li class="page-item active">
				      <span class="page-link">{{ this }}</span>
				    </li>
				    {{else}}
				    <li class="page-item">
				      <a class="page-link" href="javascript:;" tabindex="-1" data-page="{{this}}">{{ this }}</a>
				    </li>
				    {{/ifCond}}					
		  		{{/each }}	

		  		{{#ifCond page.in_last false}}
		  			<li class="page-item disabled">
				      <span class="page-link">...</span>
				    </li>
			  		<li class="page-item">
				      <a class="page-link" href="javascript:;" tabindex="-1" data-page="{{ page.to }}">{{ page.to }}</a>
				    </li>
			  	{{/ifCond}}

			  	{{#ifCond page.current page.to}}
			  		<li class="page-item disabled">
				      <span class="page-link">Next</span>
				    </li>
			  	{{else}}
			  		<li class="page-item">
				      <a class="page-link" href="javascript:;" tabindex="-1" data-page="{{ hAdd page.current 1 }}">Next</a>
				    </li>
			  	{{/ifCond}}
			  </ul>
			</nav>
		</div>
	</div>
	@endverbatim
</script>