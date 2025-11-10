<script id="mustache-table-mobile" type="text/x-handlebars-template">
    @verbatim
    <div class="row d-md-block d-lg-none">
        <div class="col-12">
            <div class="table-container">
                <div class="badge badge-info my-2 loader">
                    <span class="text-light">Loading</span> <div class="spinner-border spinner-border-sm text-light" role="status"></div>
                </div>
                <div id="ajax-table-mobile"></div>
            </div>
            {{#each items}}
            <div class="card p-2 my-2">
                <div class="row mb-2">
                    <div class="col-12">
                        <strong>{{ hTranslateHeader 'Transaction Number' }}:</strong>
                        {{ this.transaction_number }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-6">
                        <strong>{{ hTranslateHeader 'Application Date' }}:</strong>
                        {{ hFormatDate this.created_at }}
                    </div>
                    <div class="col-6">
                        <strong>{{ hTranslateHeader 'Processing Date' }}:</strong>
                        {{ hFormatDate (hFormatProcessingDate this)}}
                    </div>
                </div>
                {{#ifCond this.type 'internal_transfer'}}
                    <div class="row mb-2">
                        <div class="col-6">
                            <strong>{{ hTranslateHeader 'Account Number' }}:</strong>
                            {{this.user_account.account_number }}
                        </div>
                        <div class="col-6">
                            <strong>{{ hTranslateHeader 'Account Name' }}:</strong>
                            {{this.user_account.first_name }} {{this.user_account.last_name }}
                        </div>
                    </div>
                {{ else }}
                    {{#ifCond this.type 'payment_request'}}
                        <div class="row mb-2">
                            <div class="col-6">
                                <strong>{{ hTranslateHeader 'Account Number' }}:</strong>
                                {{this.user_account.account_number }}
                            </div>
                            <div class="col-6">
                                <strong>{{ hTranslateHeader 'Account Name' }}:</strong>
                                {{this.user_account.first_name }} {{this.user_account.last_name }}
                            </div>
                        </div>
                    {{/ifCond}}
                {{/ifCond}}
                <div class="row mb-2">
                    <div class="col-12">
                        <strong>{{ hTranslateHeader 'Type' }}:</strong>
                        {{ hTypeToUpper (hReplace this.type '_' ' ') }}
                        <td>{{ hFormatDrCr this.dr_cr }}</td>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-6">
                        <strong>{{ hTranslateHeader 'Amount' }}:</strong>
                        {{this.currency}} {{hFormatNumber this.amount}}
                    </div>
                    <div class="col-6">
                        <strong>{{ hTranslateHeader 'Fee' }}:</strong>
                        {{ hFormatNumber this.fee }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>{{ hTranslateHeader 'Status' }}:</strong></div>
                        <div class="col-6">
                            {{hStatusClass this.status}}
                        </div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>{{ hTranslateHeader 'Account Balance' }}:</strong></div>
                    <div class="col-6">
                        {{ this.currency}} {{hFormatNumber this.current_balance}}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-12">
                        <strong>{{ hTranslateHeader 'Message' }}:</strong>
                        {{ this.note }}
                    </div>
                </div>
            </div>
            {{/each}}
        </div>
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
