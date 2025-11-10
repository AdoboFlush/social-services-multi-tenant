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
            <td class="text-wrap">
                {{#if this.approval_date}}
                    {{ this.approval_date }}
                {{ else }}
                    {{#if this.transaction.approval_date}}
                        {{ this.transaction.approval_date }}
                    {{/if}}
                {{/if}}
            </td>
            <td class="text-wrap">{{this.transaction.updated_user.first_name}} {{this.transaction.updated_user.last_name}}</td>
            <td class="text-wrap text-break">{{this.transaction_number}}</td>
            <td class="text-wrap">
                <a href="/admin/users/edit/{{ this.user.account_number}}?view=1" target="_blank" data-account_number="{{ this.user.account_number }}" data-user_id="{{ this.user.id}}" onclick="logViewUser(this)">{{ this.user.account_number }}</a>
            </td>
            <td class="text-wrap">{{this.user.first_name}} {{this.user.last_name}}</td>
            <td>{{this.account.currency}}</td>
            <td class="text-wrap">
                {{#ifCond this.transaction.type 'bulk_withdrawal' }}
                    {{hFormatNumber this.transaction.amount }}
                {{ else }}
                    {{hFormatNumber this.debit_amount }}
                {{/ifCond}}
            </td>
            <td>
                {{#ifCond this.fee null }}
                0.00
                {{ else }}
                {{hFormatNumber this.fee}}
                {{/ifCond}}
            </td>
            <td class="text-wrap text-break">{{this.bank_name}}</td>
            <td class="text-wrap">{{this.bank_code}}</td>
            <td class="text-wrap text-break">{{this.bank_branch_name}}</td>
            <td class="text-wrap">{{this.bank_address}}</td>
            <td class="text-wrap text-break">{{this.customer_name}}</td>
            <td class="text-wrap">
                {{#ifCond this.transaction.type 'bulk_withdrawal' }}
                {{hFormatNumber this.transaction.currency }} {{hFormatNumber this.transaction.amount }}
                {{ else }}
                {{hFormatNumber this.transaction.currency }} {{hFormatNumber this.amount }}
                {{/ifCond}}
            </td>
            <td class="text-wrap">{{this.rate}}</td>
            <td class="text-wrap text-break"><a href='/uploads/withdrawals/{{this.import_file}}' download>{{this.import_file}}</a></td>
            <td class="text-wrap">{{hStatusClass this.status}}</td>
            <td class="text-center px-1">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                    <form action="destroy/{{this.id}}" method="post">
                        @endverbatim
                        @csrf
                        @verbatim
                        <input name="_method" type="hidden" value="DELETE">
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <button data-href="/admin/withdraw/edit/{{this.id}}" data-title="Edit Withdrawal" data-fullscreen="true" class="dropdown-item dropdown-edit ajax-modal"><i class="mdi mdi-pencil"></i>Edit</button>
                            <button class="btn-remove dropdown-item" type="submit"><i class="mdi mdi-delete"></i>Delete</button>
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
