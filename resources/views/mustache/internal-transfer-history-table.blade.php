<script id="mustache-table" type="text/x-handlebars-template">
	@verbatim
	<table class="table table-striped" style="font-size: 0.65rem !important;">
		<thead>
			<tr>
				<td colspan="3" align="center" rowspan="1"></td>
				<td colspan="6" align="center" rowspan="1">Debit Information</td>
				<td colspan="5" align="center" rowspan="1">Credit Information</td>
				<td rowspan="1" colspan="1"></td>
			</tr>

			<tr>
				{{#each  headers.headers}}
				<th>{{this}}</th>
				{{/each }}
			</tr>
		</thead>
		{{#each items}}
        <tr>
            <td>{{hFormatDate this.created_at}}</td>
            <td>
				{{#ifCond this.method 'api' }}
					api
				{{else}}
					{{ this.transaction.created_user.user_type}}
				{{/ifCond}}
			</td>
			<td>{{this.transaction_number}}</td>
			<td>
				<a href="/admin/users/edit/{{ this.sender.owner.account_number }}?view=1" data-user_id="{{ this.sender.owner.id }}" target="_blank" onclick="logViewUser(this)">{{ this.sender.owner.account_number }}</a>
			</td>
			<td class="text-wrap">{{this.sender.owner.first_name}} {{this.sender.owner.last_name}}</td>
			<td>{{this.sender.currency}}</td>
			<td>{{hFormatNumber this.sent_amount}}</td>
			<td>{{hFormatNumber this.sender_fee}}</td>
			<td>
				<a href="/admin/users/edit/{{ this.receiver.owner.account_number}}?view=1" data-user_id="{{ this.receiver.owner.id }}" target="_blank" onclick="logViewUser(this)">{{ this.receiver.owner.account_number }}</a>
			</td>
			<td class="text-wrap">{{this.receiver.owner.first_name}} {{this.receiver.owner.last_name}}</td>
			<td>{{this.receiver.currency}}</td>
			<td>{{hFormatNumber this.received_amount}}</td>
			<td>{{hFormatNumber this.receiver_fee}}</td>
			<td>{{this.transaction.rate}}</td>
			<td class="text-wrap text-break">
				<a href="uploads/bulk_internal_transfer/{{ this.import_file }}" download>{{ this.import_file }}</a>
			</td>
			<td>{{hStatusClass this.status}}</td>
		</tr>
        {{/each}}
	</table>
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