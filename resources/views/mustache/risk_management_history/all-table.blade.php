<script id="mustache-table" type="text/x-handlebars-template">
    @csrf
    @verbatim
	<table class="table table-striped">
        <tr>
            {{#each  headers.headers}}
            <th class="text-wrap">{{this}}</th>
            {{/each }}
        </tr>

        {{#each items}}
        <tr>
            <td class="text-wrap">{{ this.user.account_number }}</td>
            <td class="text-wrap">{{ this.user.full_name }}</td>
            <td class="text-wrap">{{ this.user.account_type }}</td> 
            <td class="text-wrap">{{ this.method }}</td> 
            <td class="text-wrap">{{ this.ip_address }}</td>
            <td class="text-wrap">{{ this.user_agent }}</td>
            <td class="text-wrap">{{hFormatDate this.created_at}}</td>
        </tr>
        {{/each}}
    </table>
    <div class="row">
        <div class="col-6">
            <span class="text-muted">Showing {{ hCurrent page.current  }} to {{ page.page_to }} of {{ page.total }} entries </span>
        </div>
        <div class="col-12 text-right">
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
