<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\InternalTransfer\InternalTransferFacade;

use Illuminate\Support\Facades\Log;

class TrackInternalTransferJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payload;
    protected $response;

    /**
     * Create a new job instance.
     *
     * @return void
     */
     public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = InternalTransferFacade::create($this->payload);
        Log::info(json_encode($response));
        return $response;
    }
}
