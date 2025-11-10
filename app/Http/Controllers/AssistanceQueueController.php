<?php

namespace App\Http\Controllers;

use App\AssistanceQueue;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class AssistanceQueueController extends Controller
{
    private const QUEUE_DISPLAY_TYPE_VIDEO = "video";
    private const QUEUE_DISPLAY_TYPE_IMAGE = "image";
    private const QUEUE_DISPLAY_TYPE_LINK = "link";

    public function __construct()
    {

    }

    public function index()
    {
        $news_message = $this->getNewsMessage();
        $queue_video = $this->getQueueVideo();
        $queue_image = $this->getQueueImage();
        $queue_video_link = $this->getQueueVideoLink();
        $queue_display_type = $this->getQueueDisplayType();
        return view("backend.assistance_queue.index", compact('news_message', 'queue_video', 'queue_image', 'queue_video_link', 'queue_display_type'));
    }

    public function guestIndex()
    {
        $news_message = $this->getNewsMessage();
        $queue_video = $this->getQueueVideo();
        $queue_image = $this->getQueueImage();
        $queue_video_link = $this->getQueueVideoLink();
        $queue_display_type = $this->getQueueDisplayType();
        return view("guest.queue.index", compact('news_message', 'queue_video', 'queue_image', 'queue_video_link', 'queue_display_type'));
    }

    public function queueTable()
    {
         return view("backend.assistance_queue.table");
    }

    public function store(Request $request)
    {
        $queue = new AssistanceQueue;
        $queue->name = $request->name;
        $queue->type = $request->type;
        $queue->status = AssistanceQueue::STATUS_ON_QUEUE;
        $queue->sequence_number = $this->generateSequenceNumber($request->type);
        $queue->remarks = $request->remarks;
        $queue->save();

        return response()->json(["status" => 1, "message" => "success"]);
    }
    
    public function updateStatus(Request $request, AssistanceQueue $assistance_queue)
    {
        $response = ["status" => 0, "message" => "unknown error"];
        
        try {
            if($request->status === AssistanceQueue::STATUS_PROCESSING) {
                if($assistance_queue->status !== AssistanceQueue::STATUS_ON_QUEUE) {
                    throw new Exception("This queue is already on processing or completed.");
                }
                $assistance_queue->served_at = Carbon::now();
            }
            if($request->status === AssistanceQueue::STATUS_COMPLETED) {
                if($assistance_queue->status !== AssistanceQueue::STATUS_PROCESSING) {
                    throw new Exception("Cannot complete this queue.");
                }
                $assistance_queue->completed_at = Carbon::now();
            }

            $assistance_queue->status = $request->status;
            $assistance_queue->served_by_id = Auth::user()->id;
            $assistance_queue->save();

            $response["status"] = 1;
            $response["message"] = "success";

            return $response;
            
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::info($msg);
            $response["status"] = 0;
            $response["message"] = $msg;
        } 

        return $response;
        
    }

    public function get(Request $request)
    {
        $data = AssistanceQueue::with("served_by")
            ->when($request->type, fn($q) => $q->where("status", $request->type))
            ->when($request->status, fn($q) => $q->where("status", $request->status))
            ->when($request->served_by_id, fn($q) => $q->where("served_by_id", $request->served_by_id))
            ->when($request->has('is_active'), fn($q) => $q->where("is_active", $request->is_active))
            ->when(!$request->for_report, fn($q) => $q->where("is_active", 1))
            ->when(!$request->for_report, fn($q) => $q->orderBy("sequence_number", "ASC"));
        if($request->for_report) {
            $total = $data->count();
            $data = $data->offset($request->start)
                ->limit($request->length)
                ->get();
            return response()->json([
                'data' => $data,
                'recordsTotal' =>  $total,
                'recordsFiltered' =>  $total,
                'start' => $request->start,
                'length' => $request->length,
                'draw' => $request->draw,
            ]);
        }
        $data = $data->get();
        return response()->json(["status" => 1, "message" => "success", "data" => $data]);
    }

    public function resetQueue(Request $request)
    {
        $updated = AssistanceQueue::where("is_active", 1)->update(["is_active" => 0]);
        return response()->json(["status" => 1, "message" => "success"]);
    }

    public function cancelQueue(AssistanceQueue $assistance_queue)
    {
        $assistance_queue->is_active = 0;
        $assistance_queue->status = AssistanceQueue::STATUS_CANCELED;
        $assistance_queue->save();
        return response()->json(["status" => 1, "message" => "success"]);
    }

    /**
     * API for TV queue display
     */
    public function guestQueueData(Request $request)
    {
        $onQueue = AssistanceQueue::where('status', AssistanceQueue::STATUS_ON_QUEUE)
            ->where('is_active', 1)
            ->orderBy('id', 'asc')
            ->get(['id', 'name', 'type', 'status', 'sequence_number']);

        $nowServing = AssistanceQueue::with("served_by")
            ->where('status', AssistanceQueue::STATUS_PROCESSING)
            ->where('is_active', 1)
            ->where(function($q) {
                $q->whereNull('type')
                  ->orWhere(function($q2) {
                      foreach (AssistanceQueue::REQUEST_TYPES as $type) {
                          if (strpos($type, '_PRIORITY') === false) {
                              $q2->orWhere('type', $type);
                          }
                      }
                  });
            })
            ->where(function($q) {
                $q->whereNull('type')
                  ->orWhere('type', 'not like', '%_PRIORITY');
            })
            ->orderBy('id', 'asc')
            ->get();

        $nowServingPriority = AssistanceQueue::with("served_by")
            ->where('status', AssistanceQueue::STATUS_PROCESSING)
            ->where('is_active', 1)
            ->where('type', 'like', '%_PRIORITY')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'status' => 1,
            'on_queue' => $onQueue,
            'now_serving' => $nowServing,
            'now_serving_priority' => $nowServingPriority,
        ]);
    }

    /**
     * API for TV queue display (Display section only)
     */
    public function guestQueueDisplayData(Request $request)
    {
        return response()->json([
            'status' => 1,
            'queue_display_type' => $this->getQueueDisplayType(),
            'queue_video' => $this->getQueueVideo(),
            'queue_image' => $this->getQueueImage(),
            'queue_video_link' => $this->getQueueVideoLink(),
        ]);
    }

    public function updateQueueSettings(Request $request)
    {
        Log::info("Updating queue settings: " . $request->news_message);

        if($request->has('display_type')) {
            Setting::updateOrCreate(
                ['name' => 'queue_display_type'],
                ['value' => $request->display_type]
            );
        }

        // Handle image upload
        if ($request->hasFile('news_image') && $request->file('news_image')->isValid()) {
            $image = $request->file('news_image');
            $imageName = 'queue_news_' . time() . '.' . $image->getClientOriginalExtension();
            $imagePath = public_path('uploads/images');
            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0777, true);
            }
            // Delete old image if exists
            $oldImageSetting = Setting::where('name', 'queue_news_image')->first();
            if ($oldImageSetting && !empty($oldImageSetting->value)) {
                $oldImageFile = $imagePath . '/' . $oldImageSetting->value;
                if (file_exists($oldImageFile)) {
                    @unlink($oldImageFile);
                }
            }
            $image->move($imagePath, $imageName);
            Setting::updateOrCreate(
                ['name' => 'queue_news_image'],
                ['value' => $imageName]
            );
        }

        // Handle video upload
        if ($request->hasFile('news_video') && $request->file('news_video')->isValid()) {
            $video = $request->file('news_video');
            $videoName = 'queue_news_' . time() . '.' . $video->getClientOriginalExtension();
            $videoPath = public_path('uploads/videos');
            // Ensure directory exists
            if (!file_exists($videoPath)) {
                mkdir($videoPath, 0777, true);
            }
            // Delete old video if exists
            $oldSetting = Setting::where('name', 'queue_news_video')->first();
            if ($oldSetting && !empty($oldSetting->value)) {
                $oldVideoFile = $videoPath . '/' . $oldSetting->value;
                if (file_exists($oldVideoFile)) {
                    @unlink($oldVideoFile);
                }
            }
            // Move new video
            $video->move($videoPath, $videoName);
            // Save new video filename to settings
            Setting::updateOrCreate(
                ['name' => 'queue_news_video'],
                ['value' => $videoName]
            );
        }

        // Save video link
        if ($request->has('news_video_link')) {
            Setting::updateOrCreate(
                ['name' => 'queue_news_video_link'],
                ['value' => $request->news_video_link ?? ""]
            );
        }

        // Save news message
        Setting::updateOrCreate(
            ['name' => 'queue_news_message'],
            ['value' => $request->news_message ?? ""]
        );
        return response()->json(["status" => 1, "message" => "Queue settings updated successfully."]);
    }

    private function generateSequenceNumber($type)
    {
        $queue = AssistanceQueue::where("is_active", 1)
            ->where("type",  $type)
            //->whereNotIn("status", [AssistanceQueue::STATUS_CANCELED])
            ->orderBy("sequence_number", "DESC")
            ->first();

        return $queue ? $queue->sequence_number + 1 : 1;
    }

    private function getNewsMessage()
    {
        $news_message = Setting::where('name', 'queue_news_message')->first();
        return $news_message ? $news_message->value : '';
    }

    private function getQueueVideo()
    {
        $queue_video = Setting::where('name', 'queue_news_video')->first();
        return $queue_video ? $queue_video->value : '';
    }

    private function getQueueImage()
    {
        $queue_image = Setting::where('name', 'queue_news_image')->first();
        return $queue_image ? $queue_image->value : '';
    }

    private function getQueueVideoLink()
    {
        $queue_video_link = Setting::where('name', 'queue_news_video_link')->first();
        return $queue_video_link ? $queue_video_link->value : '';
    }

    private function getQueueDisplayType()
    {
        $queue_display_type = Setting::where('name', 'queue_display_type')->first();
        return $queue_display_type ? $queue_display_type->value : '';
    }
}