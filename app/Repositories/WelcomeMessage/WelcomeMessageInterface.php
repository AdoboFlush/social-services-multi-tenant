<?php

namespace App\Repositories\WelcomeMessage;

use App\WelcomeMessage;
use Illuminate\Http\Request;

interface WelcomeMessageInterface
{
    public function getCurrentMessage(): string;
    public function getWelcomeMessage(): WelcomeMessage;
    public function updateContent(Request $content): bool;
}
