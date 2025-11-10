<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WelcomeMessage extends Model
{
    protected $fillable = ['content', 'jp_content'];

    private const MEMBER_NAME = '{MEMBER_NAME}';
    private const UNSEEN_USER_TICKETS = '{UNSEEN_USER_TICKETS}';

    public static function getCurrentMessage(): string
    {
        return self::firstOrFail()->renderPlaceholders();
    }

    /**
     * will save the welcome message for all users for now,
     * its has a limitation where members cant have a custom welcome message.
     */
    public static function updateWelcomeMessage(Request $request): bool
    {
        $welcome_message = self::firstOrFail();
        $welcome_message->content = $request->content;
        $welcome_message->jp_content = $request->jp_content;

        return $welcome_message->save();
    }

    public function renderPlaceholders(): string
    {
        $user = Auth::user();
        $language = $user->user_information->language;

        /**
         * @TODO add language constants to have a single source of truth
         */
        switch ($language) {
            case 'English':
                $content = $this->content;
                break;

            case 'Japanese':
                $content = $this->jp_content;
                break;

            default:
                /**
                 *  default this to english
                 */
                $content = $this->content;
        }
        if(strpos($content, self::UNSEEN_USER_TICKETS) !== FALSE){
            $content = str_replace(self::UNSEEN_USER_TICKETS, unseenUserTickets(), $content);
        }
        return Str::replaceArray(self::MEMBER_NAME, [$user->full_name], $content);
    }
}
