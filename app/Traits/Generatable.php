<?php

namespace App\Traits;

use App\MemberCode;
use Illuminate\Support\Str;

trait Generatable
{
	public function createMemberCode()
	{
		$code = Str::random(6);
		if(MemberCode::where('code', $code)->exists()) {
			 $this->createMemberCode();
		} else {
            return $code;
        }
	}
}
