<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssistanceQueue extends Model
{ 
    public const STATUS_ON_QUEUE = "on_queue";
    public const STATUS_CANCELED = "canceled";
    public const STATUS_PROCESSING = "processing";
    public const STATUS_COMPLETED = "completed";

    public const QUEUE_TYPE_REGULAR = "regular";
    public const QUEUE_TYPE_PRIORITY = "priority";

    public const QUEUE_TYPES = [
        self::QUEUE_TYPE_REGULAR,
        self::QUEUE_TYPE_PRIORITY,
    ];

    public const REQUEST_TYPE_DSWD = "DSWD";
    public const REQUEST_TYPE_DSWD_PRIORITY = "DSWD_PRIORITY";
    public const REQUEST_TYPE_GUARANTEE_LETTER = "GUARANTEE_LETTER";
    public const REQUEST_TYPE_GUARANTEE_LETTER_PRIORITY = "GUARANTEE_LETTER_PRIORITY";
    public const REQUEST_TYPE_MEDICAL = "MEDICAL";
    public const REQUEST_TYPE_OTHERS = "OTHERS";

    public const REQUEST_TYPES = [
        self::REQUEST_TYPE_DSWD,
        self::REQUEST_TYPE_DSWD_PRIORITY,
        self::REQUEST_TYPE_GUARANTEE_LETTER,
        self::REQUEST_TYPE_GUARANTEE_LETTER_PRIORITY,
        self::REQUEST_TYPE_MEDICAL,
        self::REQUEST_TYPE_OTHERS,
    ];

    public const REQUEST_TYPE_LABELS = [
        self::REQUEST_TYPE_DSWD => "DSWD",
        self::REQUEST_TYPE_DSWD_PRIORITY => "DSWD (Priority)",
        self::REQUEST_TYPE_GUARANTEE_LETTER => "Guarantee Letter",
        self::REQUEST_TYPE_GUARANTEE_LETTER_PRIORITY => "Guarantee Letter (Priority)",
        self::REQUEST_TYPE_MEDICAL => "Medical",
        self::REQUEST_TYPE_OTHERS => "Others",
    ];

    protected $fillable = [
        'name',
        'type',
        'status',
        'sequence_number',
        'remarks',
        'served_by_id',
        'served_at',
        'completed_at',
        'is_active',
    ];

    public function served_by() : BelongsTo
    {
        return $this->belongsTo(User::class, "served_by_id");
    }
}
