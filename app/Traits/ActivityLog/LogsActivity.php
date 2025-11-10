<?php

namespace App\Traits\ActivityLog;

use Illuminate\Support\Arr;
use Spatie\Activitylog\Traits\LogsActivity as Activity;
use Auth;

trait LogsActivity
{
    use Activity, DetectsChanges {
        DetectsChanges::attributeValuesToBeLogged insteadof Activity;
    }

    protected function shouldLogEvent(string $eventName) //: bool
    {
        if (isset(Auth::user()->user_type) && Auth::user()->user_type != "admin") {
            return false;
        }

        if (! $this->enableLoggingModelsEvents) {
            return false;
        }

        if (! in_array($eventName, ['created', 'updated'])) {
            return true;
        }

        if (Arr::has($this->getDirty(), 'deleted_at')) {
            if ($this->getDirty()['deleted_at'] === null) {
                return false;
            }
        }

        //do not log update event if only ignored attributes are changed
        return (bool) count(Arr::except($this->getDirty(), $this->attributesToBeIgnored()));
    }
}
