<script id="mustache-table" type="text/x-handlebars-template">
	@verbatim
	<div class="table-container mb-4">
	<table class="table table-striped w-100" style="font-size: 0.65rem !important;">
		<thead>
			<tr>
				{{#each  headers.headers}}
				<th class="text-wrap">{{this}}</th>
				{{/each }}
			</tr>
		</thead>

		{{#each items}}
		<tr>
			<td class="text-wrap">{{ hFormatDate this.created_at}}</td>
			<td class="text-wrap">{{ hFormatDate (hFormatProcessingDate this)}}
			</td>
			<td class="text-wrap">{{#ifCond this.type 'internal_transfer'}}
					<a href="/admin/users/edit/{{this.user_account.account_number }}" target="_blank" data-account_number="{{this.user_account.account_number }}" data-user_id="{{ this.user_account.id }}" onclick="logViewUser(this)">{{this.user_account.account_number }}</a>
                {{ else }}
                    {{#ifCond this.type 'payment_request'}}
						<a href="/admin/users/edit/{{this.user_account.account_number }}" target="_blank" data-account_number="{{this.user_account.account_number }}" data-user_id="{{ this.user_account.id }}" onclick="logViewUser(this)">{{this.user_account.account_number }}</a>
                    {{/ifCond}}
                {{/ifCond}}
            </td>
            <td class="text-wrap">
                {{#ifCond this.type 'internal_transfer'}}
                    {{this.user_account.first_name }} {{this.user_account.last_name }}
                {{ else }}
                    {{#ifCond this.type 'payment_request'}}
                        {{this.user_account.first_name }} {{this.user_account.last_name }}
                    {{/ifCond}}
                {{/ifCond}}
            </td>
            <td>{{ hFormatDrCr this.dr_cr }}</td>
			<td class="text-wrap">
				{{#ifCond this.type 'bulk_withdrawal'}}
					{{ hTypeToUpper 'withdrawal' }}
				{{ else }}
					{{ hTypeToUpper (hReplace this.type '_' ' ') }}
				{{/ifCond}}
			</td>
            <td>{{ this.currency}}</td>
            {{#ifCond this.dr_cr 'dr' }}
                <td class="text-red">{{ hFormatNumber this.amount}}</td>
            {{else}}
                <td class="text-success">{{ hFormatNumber this.amount}}</td>
            {{/ifCond}}
            <td>{{ hFormatNumber this.fee}}</td>
            <td class="text-wrap line-break-anywhere">{{ this.transaction_number}}</td>
            <td class="text-wrap">{{ this.note}}</td>
            <td>{{hStatusClass this.status}}</td>
            <td class="text-wrap">{{ this.currency}} {{hFormatNumber this.current_balance}}</td>
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