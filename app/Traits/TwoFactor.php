<?php namespace App\Traits;

use Carbon\Carbon;
use App\TwoFaCode;

trait TwoFactor
{

    public function createCode($user_id, $type) 
    {
        $expiry = 15; //minutes

        $code = sprintf("%06d", mt_rand(0, 999999));

        $exist = \App\TwoFaCode::where('user_id', $user_id)
            ->where('type', $type)
            ->where('active', 1)
            ->first();

        if ($exist) {
            $exist->active = 0;
            $exist->save();
        }

        $two_factor = new TwoFaCode();
        $two_factor->user_id = $user_id;
        $two_factor->code = $code;
        $two_factor->type = $type;
        $two_factor->active = 1;
        $two_factor->expiry_date = Carbon::now()->addMinutes($expiry);
        $two_factor->save();

        return $code;
    }

    public function validateCode($user_id, $code) 
    {
        $two_factor = TwoFaCode::where('user_id', $user_id)
            ->where('code', $code)
            ->where('active', 1)
            ->first();           

        if ($two_factor) {
            if (!$this->checkExpired($two_factor->expiry_date)) {
                return true;
            }
        }

        return false;
    }

    private function checkExpired($expired_date) 
    {
        $startTime = Carbon::now();
        $endTime = Carbon::parse($expired_date);
        $timeRemaining = $startTime->diffInSeconds($endTime, false);
        if ($timeRemaining <= 0) {
            return true;
        }

        return false;
    }
}
