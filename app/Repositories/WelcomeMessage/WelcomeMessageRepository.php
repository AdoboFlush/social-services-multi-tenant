<?php

namespace App\Repositories\WelcomeMessage;

use App\WelcomeMessage;
use Illuminate\Http\Request;

class WelcomeMessageRepository implements WelcomeMessageInterface
{
    private $model;

    public function __construct(WelcomeMessage $model)
    {
        $this->model = $model;
    }

    public function getCurrentMessage(): string
    {
        return $this->model::getCurrentMessage();
    }

    public function getWelcomeMessage(): WelcomeMessage
    {
        return $this->model::firstOrFail();
    }

    public function updateContent(Request $request): bool
    {
        return $this->model::updateWelcomeMessage($request);
    }
}