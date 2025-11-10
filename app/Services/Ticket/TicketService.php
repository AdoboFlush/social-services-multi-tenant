<?php

namespace App\Services\Ticket;

use App\Mail\Ticket\CreatedTicketAdminCopyMailer;
use App\Mail\Ticket\CreatedTicketAdminMailer;
use App\Mail\Ticket\CreatedTicketMailer;
use App\Mail\Ticket\CreatedTicketUserCopyMailer;
use App\Repositories\CannedMessage\CannedMessageInterface;
use App\Repositories\Ticket\TicketInterface;
use App\Repositories\TicketConversation\TicketConversationInterface;
use App\Repositories\User\UserInterface;
use App\Services\BaseService;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class TicketService extends BaseService
{
    private const LOGS_CREATING = 'CREATING TICKET:';
    private const LOGS_UPDATING = 'UPDATING TICKET:';

    private const LOGS_UPDATING_TEMPLATE = 'CREATING TICKET:';
    private const LOGS_DELETING_TEMPLATE = 'DELETING TICKET:';

    private const LOGS_ARCHIVE_TICKETS = 'ARCHIVING TICKET:';

    private const OTHERS = "others";
    private const SOLVED = "solved";
    private const DEFAULT_STATUS = "new";

    private const INTERNAL_MESSAGE_SOLVED = "--INTERNAL_MESSAGE_SOLVED--";
    private const FILE_SIZE_LIMIT = 2; //MB
    private const ADMIN_FILE_SIZE_LIMIT = 3;

    protected $cannedMessageInterface;
    protected $ticketInterface;
    protected $ticketConversationInterface;

    public function __construct(
        CannedMessageInterface $cannedMessageInterface,
        TicketInterface $ticketInterface,
        TicketConversationInterface $ticketConversationInterface,
        UserInterface $userInterface
    ) {
        $this->cannedMessageInterface = $cannedMessageInterface;
        $this->ticketInterface = $ticketInterface;
        $this->ticketConversationInterface = $ticketConversationInterface;
        $this->userInterface = $userInterface;
        $this->subjects = array(
            "deposit",
            "wire_transfer_deposit_request",
            "withdrawal",
            "internal_transfer",
            "payment_request",
            "currency_exchange",
            "kyc_verification",
            "update_user_information_request"
        );
    }

    public function index() : View
    {
        if ($this->isAdmin()) {
            $templates = $this->cannedMessageInterface->getAll(array("language" => "en"));
            $users = $this->userInterface->getAllUsers();
            return view('backend.tickets.create', compact('users', 'templates'));
        }
        return view('backend.user_panel.tickets.create');
    }


    /**
     * @return Illuminate\View\View | json
     */
    public function create(Request $request)
    {
        try {
            DB::beginTransaction();
            $param = $request->all();
            if ($request->hasfile('attachment')) {
                $attachments = [];
                $allowed_mime_type = "image/*,application/pdf";
                $validator = Validator::make($request->all(), [
                    'attachment.*' => "mimeTypes:${allowed_mime_type}",
                ]);

                if ($validator->fails()) {
                    throw new Exception(_lang('Uploaded a file with an invalid file type'));
                }

                $sizeLimit = Auth::user()->is_admin ? self::ADMIN_FILE_SIZE_LIMIT : self::FILE_SIZE_LIMIT;
                foreach ($request->file('attachment') as $key => $file) {
                    if (!$this->validateFileSize($file->getSize(), $sizeLimit)) {
                        throw new Exception(_lang('File Size Exceeded, limit of ' . $sizeLimit . ' MB.'));
                    }
                    $attachment = 'attachment_' . $key . time() . '.' . $file->getClientOriginalExtension();
                    Storage::disk('s3')
                        ->put('/uploads/tickets/attachments/' . $attachment, file_get_contents($file), 'public');
                    array_push($attachments, $attachment);
                }
                $param['attachment'] = json_encode($attachments);
            }

            if ($request->subject == self::OTHERS) {
                $param['subject'] = $request->others;
            }
            if (!$request->has("status")) {
                $param['status'] = self::DEFAULT_STATUS;
            }

            $user = $this->userInterface->getByAccountNumber($request->account_number);

            $param['user_id'] = $request->has("account_number") ? $user->id : Auth::user()->id;
            $param['operator_id'] = $this->isAdmin() ? Auth::user()->id : "";


            $validator = Validator::make($param, [
                'department' => 'required',
                'subject' => 'required',
                'user_id' => 'required',
                'status' => 'required'
            ]);

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json([
                        'result' => 'error',
                        'data' => $validator->errors(),
                        'message' => _lang("Error Occurred, Please try again !"),
                    ]);
                }
                return back()->withErrors($validator, 'ticket');
            }

            $ticket = $this->ticketInterface->create($param);
            if ($ticket) {
                $param["ticket_id"] = $ticket->id;
                $param["sender_id"] = Auth::user()->id;
                $param["is_seen"] = $this->isAdmin() ? 0 : 1;
                $conversation = $this->ticketConversationInterface->create($param);
                $mail = $this->createMail($ticket, $conversation);
                if ($this->isAdmin()) {
                    session(['forcedLanguage' => $ticket->user->user_information->language]);
                    Mail::to($mail->email)->send(new CreatedTicketUserCopyMailer($mail));
                    session()->forget('forcedLanguage');
                    Mail::to(env('MAIL_CONTACT'))->send(
                        new CreatedTicketAdminMailer($mail)
                    );
                    $message = _lang("Ticket has been successfully sent");
                } else {
                    Mail::to($mail->email)->send(
                        new CreatedTicketMailer($mail)
                    );
                    Mail::to(env('MAIL_CONTACT'))->send(
                        new CreatedTicketAdminCopyMailer($mail)
                    );
                    $message = _lang("Your ticket has been successfully created. Your reference ticket is {id}", [
                        'id' => $ticket->id
                    ]);
                }
                DB::commit();
                if ($request->ajax()) {
                    return response()->json([
                            'result' => 'success',
                            'message' => _lang("Request to change information has been sent to our customer support via ticket, you will be notified once your request has been completed. Thank You!"),
                    ]);
                }
                return back()->with('ticket_response', $message);
            }
        } catch (Exception $e) {
            DB::rollBack();
            $message = $this->getErrorMessage($e);
            Log::error(self::LOGS_CREATING . ' - ' . $message);
            if ($request->ajax()) {
                return response()->json([
                    'result' => 'error',
                    'message' => _lang("Error Occurred, Please try again !"),
                ]);
            }
            return back()->with('ticket_error_response', $e->getMessage());
        }
    }

    public function get(string $status, Request $request) : JsonResponse
    {
        $request['status'] = $status;
        $tickets = $this->ticketInterface->getAll($request);
        return response()->json($tickets);
    }

    public function show(string $status = null, Request $request) : View
    {
        if ($this->isAdmin()) {
            $uan = $request->has('uan') ? $request->uan : null;
            return view('backend.tickets.list', compact('status', 'uan'));
        }
        $request->request->add(['user_id' => Auth::user()->id]);
        $tickets = $this->ticketInterface->getAll($request);
        return view('backend.user_panel.tickets.list', compact('tickets'));
    }

    public function showConversation(string $id)
    {
        $subjects = $this->subjects;
        if ($this->isAdmin()) {
            $ticket = $this->ticketInterface->get($id);
            if ($ticket && $ticket->status == "new") {
                $ticket = $this->ticketInterface->update($id, ["status" => "open"]);
            }
            $templates = $this->cannedMessageInterface->getAll(array("language" => "en"));
            return view('backend.tickets.show', compact('ticket', 'subjects', 'templates'));
        }
        $ticket = $this->ticketInterface->getByTicketAndUserId($id, Auth::user()->id);
        if ($ticket) {
            $this->ticketConversationInterface->update($ticket->conversations->first()->id, ['is_seen' => 1]);
            return view('backend.user_panel.tickets.show', compact('ticket', 'subjects'));
        }
        return redirect('user/ticket/show');
    }

    public function addMessage(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            $param = $request->all();
            if ($request->hasfile('attachment')) {
                $attachments = [];
                $validator = Validator::make($request->all(), [
                    'attachment.*' => "mimeTypes:image/*,application/pdf",
                ]);

                if ($validator->fails()) {
                    throw new Exception(_lang('Uploaded a file with an invalid file type'));
                }

                $sizeLimit = Auth::user()->is_admin ? self::ADMIN_FILE_SIZE_LIMIT : self::FILE_SIZE_LIMIT;
                foreach ($request->file('attachment') as $key => $file) {
                    if (!$this->validateFileSize($file->getSize(), $sizeLimit)) {
                        throw new Exception(_lang('File Size Exceeded, limit of ' . $sizeLimit . ' MB.'));
                    }
                    $attachment = 'attachment_' . $key . time() . '.' . $file->getClientOriginalExtension();
                    Storage::disk('s3')
                        ->put('/uploads/tickets/attachments/' . $attachment, file_get_contents($file), 'public');
                    array_push($attachments, $attachment);
                }
                $param['attachment'] = json_encode($attachments);
            }

            $param["status"] = isset($param["status"]) && $param["status"] ? $param["status"] : self::DEFAULT_STATUS;
            $param["updated_at"] = Carbon::now();
            if ($this->isAdmin()) {
                $param["operator_id"] = Auth::user()->id;
                if ($request->subject == self::OTHERS) {
                    $param['subject'] = $request->others;
                }
            }
            $ticket = $this->ticketInterface->update($id, $param);

            if ($ticket) {
                $param["ticket_id"] = $ticket->id;
                $param["sender_id"] = Auth::user()->id;
                if ($request->status != self::SOLVED || $request->message) {
                    $conversation = $this->ticketConversationInterface->create($param);
                    $mail = $this->createMail($ticket, $conversation);
                    if ($this->isAdmin()) {
                        session(['forcedLanguage' => $ticket->user->user_information->language]);
                        Mail::to($mail->email)->send(new CreatedTicketUserCopyMailer($mail));
                        session()->forget('forcedLanguage');
                        Mail::to(env('MAIL_CONTACT'))->send(new CreatedTicketAdminMailer($mail));
                    } else {
                        Mail::to($mail->email)->send(
                            new CreatedTicketMailer($mail)
                        );
                        Mail::to(env('MAIL_CONTACT'))->send(
                            new CreatedTicketAdminCopyMailer($mail)
                        );
                    }
                } elseif ($request->status == self::SOLVED && !$request->message) {
                    unset($param["attachment"]);
                    $param["ticket_id"] = $ticket->id;
                    $param["sender_id"] = Auth::user()->id;
                    $param["message"] = self::INTERNAL_MESSAGE_SOLVED;
                    $this->ticketConversationInterface->create($param);
                }
                DB::commit();
                return back()->with('ticket_response', _lang("Ticket has been successfully updated."));
            }
        } catch (Exception $e) {
            DB::rollBack();
            $message = $this->getErrorMessage($e);
            Log::error(self::LOGS_UPDATING . ' - ' . $message);
            return back()->with('ticket_error_response', $e->getMessage());
        }
    }

    public function updateTicket(Request $request, string $id) : RedirectResponse
    {
        try {
            DB::beginTransaction();
            $param = $request->all();
            if ($this->isAdmin()) {
                $param["operator"] = Auth::user()->id;
                if ($request->subject == self::OTHERS) {
                    $param['subject'] = $request->others;
                }
            }
            $ticket = $this->ticketInterface->update($id, $param, $request->status == "solved");
            if ($ticket) {
                $dirty = $this->ticketInterface->getDirty();
                if ($dirty) {
                    if (isset($dirty["status"]) && $dirty["status"] == "solved") {
                        $param["ticket_id"] = $ticket->id;
                        $param["sender_id"] = Auth::user()->id;
                        $param["message"] = self::INTERNAL_MESSAGE_SOLVED;
                        unset($param["attachment"]);
                        $this->ticketConversationInterface->create($param);
                    }
                    DB::commit();
                    return back()->with('ticket_response', _lang("Ticket has been successfully updated."));
                }
                return back()->with('ticket_response', _lang("No changes in the ticket."));
            }
            DB::rollBack();
            return back()->with('ticket_response', _lang("Error Occurred, Please try again !"));
        } catch (Exception $e) {
            DB::rollBack();
            $message = $this->getErrorMessage($e);
            Log::error(self::LOGS_UPDATING . ' - ' . $message);
            return back()->with('ticket_response', _lang("Error Occurred, Please try again !"));
        }
    }

    public function viewCannedMessages() : View
    {
        $cannedMessages = $this->cannedMessageInterface->getAll();
        return view('backend.tickets.canned_messages.list', compact('cannedMessages'));
    }

    public function viewCannedMessage(int $id) : JsonResponse
    {
        $cannedMessage = $this->cannedMessageInterface->get($id);
        if ($cannedMessage) {
            $cannedMessage->creator;
            $cannedMessage->editor;
            return response()->json(['result' => 'success','message' => $cannedMessage]);
        }
        return response()->json(['result' => 'error','message' => _lang('Unable to find message.')]);
    }

    public function updateOrCreateCannedMessage(Request $request, int $id = null) : RedirectResponse
    {
        try {
            DB::beginTransaction();
            $param = $request->all();
            $param["updated_by"] = Auth::user()->id;
            if ($id) {
                $cannedMessage = $this->cannedMessageInterface->update($id, $param);
            } else {
                $param["created_by"] = Auth::user()->id;
                $cannedMessage = $this->cannedMessageInterface->create($param);
            }
            if ($cannedMessage) {
                DB::commit();
                return back()->with([
                    'canned_response' => _lang("You have successfully saved a message."),
                    'language' => $request->language
                ]);
            }
            return back()->with([
                'canned_error_response' => _lang("Unable to save a message."),
                'language' => $request->language
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            $message = $this->getErrorMessage($e);
            Log::error(self::LOGS_UPDATING_TEMPLATE . ' - ' . $message);
            return back()->with([
                'canned_error_response' => _lang("Unable to save a message."),
                'language' => $request->language
            ]);
        }
    }

    public function deleteCannedMessage(int $id) : RedirectResponse
    {
        try {
            DB::beginTransaction();
            $cannedMessage = $this->cannedMessageInterface->delete($id);
            if ($cannedMessage) {
                DB::commit();
                return back()->with([
                    'canned_response' => _lang("You have successfully deleted a message."),
                    'language' => $cannedMessage->language
                ]);
            }
            return back()->with([
                'canned_error_response' => _lang("Unable to delete a canned message."),
                'language' => $cannedMessage->language
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            $message = $this->getErrorMessage($e);
            Log::error(self::LOGS_DELETING_TEMPLATE . ' - ' . $message);
            return back()->with([
                'canned_error_response' => _lang("Unable to delete a canned message.")
            ]);
        }
    }

    public function getCannedMessages(string $lang)
    {
        $templates = $this->cannedMessageInterface->getAll(array("language" => $lang));
        if ($templates) {
            return view('backend.tickets.canned_messages.options', compact('templates'));
        }
        return response()->json(['error_response' => _lang("An Error Occurred")]);
    }

    public function archiveTicketsPast($days = 90)
    {
        Log::info(self::LOGS_ARCHIVE_TICKETS);
        $this->ticketInterface->archiveTicketsPast($days);
        return $this->sendResponse([], 'Tickets are successfully sent to archives');
    }

    private function validateFileSize(int $sizeInBytes, int $sizeLimit): bool
    {
        $denominator = 1048576; //MB
        $sizeInMb = round(($sizeInBytes / $denominator ), 2);
        return $sizeInMb <= $sizeLimit;
    }

    private function isAdmin()
    {
        return lcfirst(Auth::user()->user_type) == "admin";
    }

    private function createMail($ticket, $conversation)
    {
        $mail = new \stdClass();
        $mail->email = $ticket->user->email;
        $mail->first_name = $ticket->user->first_name;
        $mail->last_name = $ticket->user->last_name;
        $mail->account_number = $ticket->user->account_number;
        $mail->subject = toWords($ticket->subject);
        $mail->message = $conversation->message;
        $mail->ticket_number = $ticket->id;
        $attachments = [];
        if (isset($conversation->attachment) && $conversation->attachment) {
            foreach (json_decode($conversation->attachment) as $attachment) {
                $attachments[$attachment] = asset('uploads/tickets/' . $attachment);
            }
        }
        $mail->attachments = $attachments;
        return $mail;
    }
}
