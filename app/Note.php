<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Note extends Model
{
    protected $fillable = [
        'content',
        'jp_content',
    ];

    public const DEPOSIT_JP = 'deposit_jp';
    public const DEPOSIT_WIRE_TRANSFER = 'deposit_wire_transfer';
    public const DEPOSIT_CARD = 'deposit_card';
    public const DEPOSIT_SEA = 'deposit_sea';

    public static function getNote(string $note_slug): ?string
    {
        return self::firstOrFail()->renderPlaceholders($note_slug);
    }

    public function renderPlaceholders(string $note_slug): ?string
    {
        $language = Auth::user()->user_information->language;

        switch ($language) {
            case 'English':
                $content = $this->where('slug', $note_slug)->firstOrFail()->content;
                break;

            case 'Japanese':
                $content = $this->where('slug', $note_slug)->firstOrFail()->jp_content;
                break;

            default:
                /**
                 *  default this to english
                 */
                $content = $this->where('slug', $note_slug)->firstOrFail()->content;
        }
        
        return $content;
    }
}
