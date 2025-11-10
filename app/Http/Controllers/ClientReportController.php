<?php

namespace App\Http\Controllers;

use App\Currency;
use App\Http\Middleware\User;
use Illuminate\Http\Request;
use App\Transaction;
use App\Account;
use DB;
use Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientReportController extends Controller
{

    const LOG_TRANSACTION_REPORT = 'LOG TRANSACTION REPORT REQUEST';

    protected $transactionFacade;

    public function __construct()
    {
    }

    public function account_statement(Request $request, $view = "")
    {
        if ($view == '') {
            return view('backend.user_panel.reports.account_statement');
        } elseif ($view == 'view') {
            $data = array();
            $date1 = $request->date1;
            $date2 = $request->date2;
            $status = $request->status;
            $account = $request->account;
            if ($status == "all") {
                $data['report_data'] = Transaction::where('account_id', $account)
                                                  ->where('user_id', Auth::id())
                                                  ->whereBetween('created_at', [$date1, $date2])
                                                  ->orderBy('id', 'desc')
                                                  ->get();
            } else {
                $data['report_data'] = Transaction::where('account_id', $account)
                                                  ->where('user_id', Auth::id())
                                                  ->where('status', $status)
                                                  ->whereBetween('created_at', [$date1, $date2])
                                                  ->orderBy('id', 'desc')
                                                  ->get();
            }
            $data['status'] = $request->status;
            $data['date1'] = $request->date1;
            $data['date2'] = $request->date2;
            $data['account'] = $request->account;
            $data['acc'] = Account::find($account);
            return view('backend.user_panel.reports.account_statement', $data);
        }
    }

    public function all_transaction(Request $request, $view = '')
    {
        return $this->transactionFacade::getAllTransactions($request);
    }

    public function getAllUserTransactions(Request $request, $user_id = null)
    {
        return $this->transactionFacade::getAllUserTransactions($request, $user_id);
    }

    public function exportAllUserTransactions(Request $request, int $user_id = null): StreamedResponse
    {
        return $this->transactionFacade::exportAllUserTransactions($request, $user_id);
    }

    public function referred_users(Request $request)
    {
        $data = array();
        $data['report_data'] = \App\User::where('refer_user_id', Auth::id())
                                        ->orderBy('id', 'desc')
                                        ->get();
        return view('backend.user_panel.reports.referred_users', $data);
    }
}
