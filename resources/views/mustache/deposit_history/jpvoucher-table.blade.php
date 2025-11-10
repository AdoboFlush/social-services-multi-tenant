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
            <td class="text-wrap">{{hFormatDate this.created_at}}</td>
            <td class="text-wrap">{{hFormatDate this.created_at}}</td>
            <td class="text-wrap">
                <a href="/admin/users/edit/{{ this.user.account_number}}?view=1" target="_blank" data-account_number="{{ this.user.account_number }}" data-user_id="{{ this.user.id}}" onclick="logViewUser(this)">{{ this.user.account_number }}</a>
            </td>
            <td class="text-wrap">{{this.user.first_name}} {{this.user.last_name}}</td>
            <td>{{this.account.currency}}</td>
            <td class="text-wrap">{{hFormatNumber this.amount}}</td>
            <td>
                {{#ifCond this.fee null }}
                0.00
                {{ else }}
                {{hFormatNumber this.fee}}
                {{/ifCond}}
            </td>
            <td class="text-wrap">JP Voucher</td>
            <td class="text-wrap">{{this.transaction_number}}</td>
            <td class="text-wrap">{{this.payment_id}}</td>
            <td class="text-wrap">{{this.transfer_id}}</td>
            <td class="text-wrap"><a href='/uploads/receipts/{{this.import_file}}' download>{{this.import_file}}</a></td>
            <td>{{hStatusClass this.status}}</td>
            <td class="text-center">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                    <form action="destroy/{{this.id}}" method="post">
                        @endverbatim
                        @csrf
                        @verbatim
                        <input name="_method" type="hidden" value="DELETE">
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <button data-href="edit/{{this.id}}" data-title="Update Deposit" class="dropdown-item dropdown-edit ajax-modal"><i class="mdi mdi-pencil"></i>Edit</button>
                            <button data-href="show/{{this.id}}" data-title="View Deposit" class="dropdown-item dropdown-view ajax-modal"><i class="mdi mdi-eye"></i>View</button>
                            <button class="btn-remove dropdown-item" type="submit" data-ajax="destroy/verify/{{this.id}}"><i class="mdi mdi-delete"></i>Delete</button>
                        </div>
                    </form>
                </div>
            </td>
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