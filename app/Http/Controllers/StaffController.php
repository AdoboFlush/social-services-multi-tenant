<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\User;

use App\Services\Staff\StaffFacade;

use Validator;
use Hash;

class StaffController extends Controller
{
    private $staff_facade;

	public function __construct(
		StaffFacade $staff_facade
	)
	{        
		$this->staff_facade = $staff_facade;
	}

	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::whereIn('user_type', [User::ADMIN, User::TAGGER, User::PAYMASTER, User::WATCHER])
					 ->orderBy("id","desc")->get();
        return view('backend.staff.list',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		return $this->staff_facade::create($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {	
        return $this->staff_facade::store($request);
    }
	

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $user = User::find($id);
		if(! $request->ajax()){
		    return view('backend.staff.view',compact('user','id'));
		}else{
			return view('backend.staff.modal.view',compact('user','id'));
		} 
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
       
       return $this->staff_facade::edit($id, $request); 
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
		return $this->staff_facade::update($id, $request); 	    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $log = array(
            "name" => $user->first_name . " " . $user->last_name,
            "email" => $user->email,
        );
        activity('Manager and Admin')
            ->performedOn($user)
            ->withProperties($log)
            ->log('Delete');
        $user->delete();
        return redirect('admin/staffs')->with('success',_lang('Removed Successfully'));
    }
}
