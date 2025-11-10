<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileUploadRequest;
use App\Http\Requests\UploadProofOfIdentityRequest;
use App\Http\Requests\UserDocumentRequest;
use App\Services\UserDocument\UserDocumentFacade;
use Auth;
use Illuminate\Http\Request;

class UserDocumentController extends BaseController
{
    private $userDocumentFacade;

    public function __construct(UserDocumentFacade $userDocumentFacade)
    {
        $this->userDocumentFacade = $userDocumentFacade;
    }

    public function create()
    {
        $userDocuments = $this->userDocumentFacade::retrieveAllByUserId(Auth::user()->id);
        return view('backend.user_panel.submit_documents', compact('userDocuments'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->userDocumentFacade::multiUpload($request, Auth::user()->id);
    }

    public function multiUpload(Request $request, $userId)
    {
        return $this->userDocumentFacade::multiUpload($request, $userId);
    }

    public function updateByUserId(Request $request,$userId)
    {
        return $this->userDocumentFacade::updateByUserId($request,$userId);
    }
}