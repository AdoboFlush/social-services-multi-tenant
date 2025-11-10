<script id="mustache-table" type="text/x-handlebars-template">
	@verbatim
	<div class="table-container mb-4">
	<table class="table table-striped" style="font-size: 0.65rem !important;">
		<thead>
			<tr>
				<td colspan="3" align="center" rowspan="1"></td>
				<td colspan="7" align="center" rowspan="1">Debit Information</td>
				<td colspan="6" align="center" rowspan="1">Credit Information</td>
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
			<td>
			{{#ifCond this.approval_date null }}
				{{ hFormatDate this.created_at }}
			{{else}}
				{{ hFormatDate this.approval_date }}
			{{/ifCond}}
			</td>
			<td>
				{{ this.user_type }}
			</td>
			<td>{{ this.transaction_number }}</td>
			<td>
			{{#ifCond this.type 'bulk_withdrawal' }}
				withdrawal
			{{else}}
				{{  hReplace this.type '_' ' ' }}
			{{/ifCond}}
			</td>
			<td>
				{{ hReplace this.method '_' ' ' }}
			</td>
			<td>
			{{#ifCond this.debit_account null }}

			{{ else }}
				<a href="/admin/users/edit/{{ this.debit_account.account_number}}?view=1" target="_blank" data-account_number="{{ this.debit_account.account_number }}" data-user_id="{{ this.debit_account.id }}" onclick="logViewUser(this)">{{ this.debit_account.account_number }}</a>
			{{/ifCond}}
			</td>
			<td>
			{{#ifCond this.debit_account null }}

			{{ else }}
				{{this.debit_account.name}}
			{{/ifCond}}
			</td>
			<td>
			{{#ifCond this.debit_account null }}

			{{ else }}
				{{this.debit_account.currency }}
			{{/ifCond}}
			</td>
			<td class="text-red">
			{{#ifCond this.debit_account null }}

			{{ else }}
				{{#ifCond this.type 'inactivity_fee'}}

				{{else}}
				{{ hFormatNumber this.debit_account.amount }}
				{{/ifCond}}
			{{/ifCond}}
			</td>
			<td>
			{{#ifCond this.debit_account null}}

			{{else}}
				{{ hFormatNumber this.debit_account.fee }}
			{{/ifCond}}
			</td>
			<td>
			{{#ifCond this.credit_account null }}

			{{ else }}
				<a href="/admin/users/edit/{{ this.credit_account.account_number}}?view=1" target="_blank" data-account_number="{{ this.credit_account.account_number }}" data-user_id="{{ this.credit_account.id }}" onclick="logViewUser(this)">{{ this.credit_account.account_number }}</a>
			{{/ifCond}}
			</td>
			<td>
			{{#ifCond this.credit_account null }}

			{{ else }}
				{{this.credit_account.name}}
			{{/ifCond}}
			</td>
			<td>
			{{#ifCond this.credit_account null }}

			{{ else }}
				{{this.credit_account.currency }}
			{{/ifCond}}
			</td>
			<td class="text-success">
			{{#ifCond this.credit_account null }}

			{{ else }}
				{{ hFormatNumber this.credit_account.amount }}
			{{/ifCond}}
			</td>
			<td>
			{{#ifCond this.credit_account null}}

			{{else}}
				{{ hFormatNumber this.credit_account.fee }}
			{{/ifCond}}
			</td>
			<td>
				{{ this.rate  }}
			</td>
			<td>{{hStatusClass this.status}}</td>
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