<?php

namespace App\Services\UserDocument;

use App\Repositories\User\UserInterface;
use App\Repositories\UserDocument\UserDocumentInterface;
use App\Services\BaseService;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserDocumentService extends BaseService
{

    const LOGS_UPLOADING = 'UPLOADING DOCUMENT:';
    const LOGS_UPLOAD_ERROR = 'LOG UPLOAD ERROR:';

    const FILE_SIZE_LIMIT = 1; //MB

    protected $userDocumentInterface;
    protected $userInterface;

    public function __construct(
        UserDocumentInterface $userDocumentInterface,
        UserInterface $userInterface
    ) {
        $this->userDocumentInterface = $userDocumentInterface;
        $this->userInterface = $userInterface;
    }

    public function retrieveAllByUserId($userId)
    {
       return $this->userDocumentInterface->getAllByUserId($userId);
    }

    public function create($request,$id=null)
    {
        try {
            Log::info(self::LOGS_UPLOADING);
            DB::beginTransaction();
            if ($request->hasfile('nid_passport')) {
                $this->uploadFile($request->file('nid_passport'),$id,"identification");
            }
            if ($request->hasfile('electric_bill')) {
                $this->uploadFile($request->file('electric_bill'),$id,"address");
            }
            $this->newKyc($id);
            DB::commit();
            return back()->with('document_success', _lang('Thank you for submitting your document(s). Our Customer Support Team will notify you as soon as your document(s) have been reviewed.'));
        } catch (Exception $e) {
            DB::rollBack();
            $message = $this->getErrorMessage($e);
            Log::error(self::LOGS_UPLOAD_ERROR . ' - ' . $message);
            return back()->with('error', _lang('Unexpected Error Occurred: ' .  $message));
        }
    }

    public function multiUpload($request,$id){
        try {
            Log::info(self::LOGS_UPLOADING);

            if(!$request->file()){
                return back()->with('error', _lang('Proof of ID or Proof of Address is required.'));
            }
            $allowed_mime_type = "image/*";
            $validator = Validator::make($request->all(), [
                'identity_1' => "mimeTypes:${allowed_mime_type}",
                'identity_2' => "mimeTypes:${allowed_mime_type}",
                'identity_3' => "mimeTypes:${allowed_mime_type}",
                'identity_4' => "sometimes|mimeTypes:${allowed_mime_type}",
                'identity_5' => "sometimes|mimeTypes:${allowed_mime_type}",
                'identity_6' => "sometimes|mimeTypes:${allowed_mime_type}",
                'address_1' => "sometimes|mimeTypes:${allowed_mime_type}",
                'address_2' => "sometimes|mimeTypes:${allowed_mime_type}",
                'address_3' => "sometimes|mimeTypes:${allowed_mime_type}",
            ]);

            if ($validator->fails()) {
                return back()->with('error', _lang('Uploaded a file with an invalid file type'));
            }

            foreach($request->file() as $value){
                if (!$this->_validateFileSize($value->getSize())) {
                    return back()->with('error', _lang('File Size Exceeded, limit of ' . self::FILE_SIZE_LIMIT . ' MB.'));
                }
            }

            DB::beginTransaction();
            $files = array();

            foreach($request->file() as $key => $value){
                $file = explode("_",$key);
                if($file[0] == "identity") {
                    $file = $this->uploadFile($value,$id,"identification");
                    array_push($files,$file);
                }
                if($file[0] == "address"){
                    $file = $this->uploadFile($value,$id,"address");
                    array_push($files,$file);
                }
            }
            activity()->disableLogging();
            $user = $this->newKyc($id);

            DB::commit();
            if(Auth::user()->user_type == "admin"){
                activity()->enableLogging();
                activity("User Documents")
                    ->causedBy(Auth::user())
                    ->performedOn($user)
                    ->withProperties($files)
                    ->log('Uploaded New Document');
                return back()->with('document_success', _lang('Documents successfully uploaded.'));
            }
            return back()->with('document_success', _lang('Thank you for submitting your document(s). Our Customer Support Team will notify you as soon as your document(s) have been reviewed.'));
        } catch (Exception $e) {
            DB::rollBack();
            $message = $this->getErrorMessage($e);
            Log::error(self::LOGS_UPLOAD_ERROR . ' - ' . $message);
            return back()->with('error', _lang('Unexpected Error Occurred: ' .  $message));
        }
    }

    public function updateByUserId($request, $userId)
    {
        if($request->status_all){
            $param['status'] = $request->function_all;
            $document = $this->userDocumentInterface->updateAllByUserIdAndStatus($userId,$request->status_all,$param);
            if($document){
                return $this->successfulUpdate();
            }
            return $this->unsuccessfulUpdate();
        }
        foreach ($request->all() as $key => $value){
            if($key == "status_all" || $key == "function_all") continue;
            $document = $this->userDocumentInterface->get($key);
            if($document->status === $value) {
                return response()->json([
                    'status' => 1,
                    'message' => _lang("No changes were made"),
                ]);
            } else {
                $document = $this->userDocumentInterface->updateStatus($key,$value);
                if(!$document){
                    return $this->unsuccessfulUpdate();
                }
            }
        }
        return $this->successfulUpdate();
    }

    public function uploadFile($file,$id,$kind = "identification")
    {
        $fileName = null;
        if($file) {
            $fileName = $kind == "identification" ? 'Identification_Document_'.time().'.'.$file->getClientOriginalExtension() : 'Address_Verification_'.time().'.'.$file->getClientOriginalExtension();
            //$file->move(public_path()."/uploads/documents/", $fileName);
            Storage::disk('s3')->put('/uploads/documents/'.$fileName, file_get_contents($file),'public');
            $document['document_name'] = $kind == "identification" ? _lang("Identification Document") : _lang("Address Verification Document");
            $document['document'] = $fileName;
            $document['user_id'] = $id;
            $document['status'] = "unreviewed";
            $this->userDocumentInterface->create($document);
        }
        return $fileName;
    }
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    private function successfulUpdate(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 1,
            'message' => _lang("Successfully updated the documents"),
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    private function unsuccessfulUpdate(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 0,
            'message' => _lang("Unable to update the documents"),
        ]);
    }

    /**
     * @param $id
     */
    private function newKyc($id)
    {
        $user = array();
        $user['document_submitted_at'] = Carbon::now();
        $user['kyc_status'] = "unreviewed";
        return $this->userInterface->update($id, $user);
    }

    private function _validateFileSize($sizeInBytes)
    {
        $denominator = 1048576; //MB
        $sizeInMb = round(($sizeInBytes / $denominator ), 2);

        if ($sizeInMb > self::FILE_SIZE_LIMIT) {
            return false;
        }

        return true;

    }
}
