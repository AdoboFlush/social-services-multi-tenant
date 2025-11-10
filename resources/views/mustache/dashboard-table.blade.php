<script id="mustache-table" type="text/x-handlebars-template">
	@verbatim
	<table class="table table-responsive table-striped">
		<tr>
			{{#each  headers.headers}}
			<th>{{this}}</th>
			{{/each }}
		</tr>

		{{#each items}}
		<tr>
			<td>{{this.created_at}}</td>
			<td>
				<a href="/admin/users/edit/{{ this.account_number}}" target="_blank" data-account_number="{{ this.account_number }}" data-user_id="{{ this.id }}" onclick="logViewUser(this)">{{ this.account_number }}</a>
			</td>
			<td>{{this.account_type}}</td>
			<td>{{this.first_name}}</td>
			<td>{{this.last_name}}</td>
			<td>{{this.email}}</td>
			<td>{{this.user_information.country_of_residence}}</td>
			<td>
				{{#ifCond this.affiliate_details null}}

				{{else}}
					{{ this.affiliate_details.parent_code }}
				{{/ifCond}}
			</td>
			<td>
				{{#ifCond this.is_dormant 1}}
				{{ hStatusClass 'dormant'}}
				{{ else }}
				{{ hStatusClass this.account_status}}
				{{/ifCond}}
			</td>
			<td>{{this.user_information.account_verified_at}}</td>
			<td>{{this.user_information.account_closed_at}}</td>
			<td>{{this.user_information.account_suspended_at}}</td>
			<td>{{this.user_information.account_declared_dormant_at}}</td>
			<td>{{this.user_information.last_login_at}}</td>
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