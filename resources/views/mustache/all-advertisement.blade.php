<script id="mustache-table" type="text/x-handlebars-template">
	@verbatim
	<table class="table table-responsive table-striped">
		<tr>
			{{#each  headers.headers}}
        <th>{{this}}</th>
			{{/each }}
			<th></th>
		</tr>

		{{#each items}}
		<tr>
            <td>{{this.language}}</td>
			<td>{{this.ordinal}}</td>
			<td><img class="w-50" src={{this.banner_url}} /></td>
			<td>{{this.title}}</td>
			<td><a href="{{this.link}}" target="_blank">{{this.link}}</a></td>
			<td>{{this.owner_full_name}}</td>
            <td>
				<div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    	Action
                    </button>
                    <form action="advertisements/{{ this.id }}" method="post">
						@endverbatim
						@method('DELETE')
						@csrf
						@verbatim
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
							<a class="dropdown-item" href="advertisements/{{this.id}}/edit?lang={{this.language}}">Edit</a>
                            <button class="btn-remove dropdown-item" type="submit"><i class="mdi mdi-delete"></i> Delete</button>
                        </div>
                    </form>
                </div>
			</td>
        </tr>
		{{/each}}
	</table>
	<div class="row">
		<div class="col-6">
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