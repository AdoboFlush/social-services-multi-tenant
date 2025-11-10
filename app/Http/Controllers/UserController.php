<?php

namespace App\Http\Controllers;

use App\Account;
use App\Card;
use App\Deposit;
use App\Services\AccountService;
use App\Services\Password\PasswordFacade;
use App\Services\User\UserFacade;
use App\Services\UserService;
use App\Traits\Signature;
use App\Transaction;
use App\User;
use App\Withdraw;
use Auth;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends BaseController
{
    use Signature;

    protected $accountService;
    const LOG_TRANSACTION_REPORT = 'LOG ADMIN - USER TRANSACTION REPORT REQUEST';
    private $userService;
    private $userFacade;
    private $passwordFacade;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        AccountService $accountService,
        UserService  $userService,
        UserFacade $userFacade,
        PasswordFacade $passwordFacade
    )
    {
        $this->accountService = $accountService;
        $this->userService = $userService;
        $this->userFacade = $userFacade;
        $this->passwordFacade = $passwordFacade;
    }

    //@TODO: Will delete once passed in production
    public function forceDormant($id,$days = 100){
        return $this->userFacade::forceDormant($id,$days);
    }

    //@TODO: Will delete once passed in production
    public function forceCheckMaintenance($id = 0){
        return $this->userFacade::forceCheckMaintenance($id);
    }

    /**
     * Display user lists with default filter.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index($account_status = null)
    {
        return $this->userFacade::getAllUsers($account_status);
    }

    public function filterDocuments(Request $request)
    {
        return $this->userFacade::filterUserDocumentsAndDetailsBy($request);
    }

    /**
     * Display a listing of users Documents.
     *
     * @return \Illuminate\Http\Response
     */

	public function documents()
    {
        return $this->userFacade::viewDocuments();
    }

    public function getDocuments(Request $request): Object
    {
        return $this->userFacade::getDocuments($request);
    }

	/**
     * Display single users Documents.
     *
     * @return \Illuminate\Http\Response
     */
	public function view_documents($user_id)
    {
        return $this->userFacade::viewDocumentsById($user_id);
    }


	/**
     * Varify User account.
     *
     * @return \Illuminate\Http\Response
     */
	public function varify($id){
        return $this->userFacade::verifyUser($id);
	}

	public function switchLanguage($to)
    {
        return $this->userFacade::switchLanguage($to);
    }

    public function securityPassword(Request $request)
    {
        if(!$request->isMethod('post')){
            return view('backend.security.index');
        }
        return $this->passwordFacade::securityPassword($request);
    }

    public function createSecurityPassword(Request $request)
    {
        if(!$request->isMethod('post')){
            return redirect("user/security_settings");
        }
        return $this->passwordFacade::createSecurityPassword($request);
    }

    public function confirmSecurityPassword(Request $request)
    {
        return $this->passwordFacade::confirmSecurityPassword($request);
    }

    public function editSecurityPassword(Request $request, $id)
    {
        return $this->passwordFacade::editSecurityPassword($request, $id);
    }

    public function resetSecurityPassword($id)
    {
        return $this->passwordFacade::resetSecurityPassword($id);
    }

    public function updateCardNumber(Request $request, $id)
    {
        return $this->userFacade::updateCardNumber($request, $id);
    }

    public function removeCardNumber($id)
    {
        return $this->userFacade::removeCardNumber($id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.user.create');
		}else{
           return view('backend.user.modal.create');
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
		$user_type = Auth::user()->user_type;
        $user = User::where('id',$id)
                    ->when($user_type, function ($query, $user_type) {
				   		  	 if($user_type == 'staff'){
			                     return $query->where('created_by', Auth::id());
			                 }
		                  })
                    ->first();
        activity('User List')
            ->performedOn($user)
            ->log('Viewed');
		if(! $request->ajax()){
		    return view('backend.user.view',compact('user','id'));
		}else{
			return view('backend.user.modal.view',compact('user','id'));
		}

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $account_number
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $account_number)
    {
        return $this->userFacade::edit($request, $account_number);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return $this->userFacade::update($request,$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    	if(Auth::user()->user_type != 'admin'){
            return back()->with('error',_lang('Permission denied !'));
        }

    	DB::beginTransaction();

        $user_type = Auth::user()->user_type;

        $user = User::where('id',$id)
                    ->when($user_type, function ($query, $user_type) {
				   		  	 if($user_type == 'staff'){
			                     return $query->where('created_by', Auth::id());
			                 }
		                  })->first();

        activity('User List')
            ->performedOn($user)
            ->withProperties(array(
                "name" => $user->first_name . " " . $user->last_name,
                "account_number" => $user->account_number
            ))->log('Deleted');

        if($user){
        	$user->delete();
	        Account::where('user_id',$id)->delete();
	        Transaction::where('user_id',$id)->delete();
	        Deposit::where('user_id',$id)->delete();
	        Withdraw::where('user_id',$id)->delete();
	        Card::where('user_id',$id)->delete();
	    }

        DB::commit();

        return redirect('admin/users')->with('success',_lang('Removed Successfully'));
    }

    /*
     * @param Request $request
     *
     * @return JSON
     */
    public function search(Request $request)
    {
        return $this->userFacade::searchUser($request);
    }

    public function searchUserBy(Request $request)
    {
        return $this->userFacade::searchUserBy($request->account_number);
    }

    public function reviewCsv(Request $request)
    {
        return $this->userFacade::reviewCsv($request);
    }

    public function registerViaAdmin(Request $request)
    {
        return $this->userFacade::individualCreate($request);
    }
    public function registerViaCsv(Request $request)
    {
        return $this->userFacade::bulkCreate($request);
    }

    public function updateStatusById(Request $request, $id)
    {
        return $this->userFacade::updateKycById($request,$id);
    }

    public function viewForceChangePassword() {
        return view('auth.force_change_password');
    }

    public function submitForceChangePassword(Request $request) {
        return $this->userFacade::submitChangePassword($request);
    }

    public function updateForceChangePassword(Request $request) {
        return $this->userFacade::updateForceChangePassword($request);
    }

    public function resendVerificationEmail(Request $request) {
        return $this->userFacade::resendVerificationEmail($request);
    }

    public function updateKycStatus(Request $request) {
        return $this->userFacade::updateKycStatus($request);
    }

    public function userSessions(): View
    {
        return $this->userFacade::userSessions();
    }

    public function userSessionsHistory(Request $request): JsonResponse
    {
        return $this->userFacade::userSessionsHistory($request);
    }

    public function generateIncorrectBalanceUserCSV(Request $request)
    {
        return $this->userFacade::generateIncorrectBalanceUserCSV($request);
    }

}
