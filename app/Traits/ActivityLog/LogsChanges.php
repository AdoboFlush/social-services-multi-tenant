<?php

namespace App\Traits\ActivityLog;

use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity as Activity;

trait LogsChanges
{
    use Activity;

    protected function logUpdate($log_name = 'Update', $description = 'Update', $before, $after)
    {
        activity($log_name)
        ->causedBy(Auth::user())
        ->withProperties($this->getModelChanges($before, $after))
        ->log($description);
    }

    
    function getModelChanges($before, $after): array {
        $before = is_array($before) ? (object) $before : $before;
        $after = is_array($after) ? (object) $after : $after;
    
        $changes = ['from' => [], 'to' => []];
    
        foreach ($after as $key => $afterValue) {
            $beforeValue = $before->$key ?? null; 
    
            if ($beforeValue !== $afterValue) {
                $changes['from'][$key] = $beforeValue;
                $changes['to'][$key] = $afterValue;             
            }
        }
    
        return $changes;
    }
}
