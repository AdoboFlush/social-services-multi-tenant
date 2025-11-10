<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use NumberFormatter;

class Advertisement extends Model
{
    protected $fillable = [
        'banner',
        'link',
        'owner',
        'sequence',
        'title',
        'language',
    ];

    protected $appends = ['ordinal', 'banner_url', 'owner_full_name'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner', 'id');
    }

    public function getOrdinalAttribute(): string
    {
        return self::toOrdinal($this->sequence);
    }

    public function getOwnerFullNameAttribute(): string
    {
        return $this->user->full_name;
    }

    public function getBannerUrlAttribute(): string
    {
        return Storage::disk('s3')->url($this->banner);
    }

    public static function toOrdinal(int $number): string
    {
        $numberFormatter = new NumberFormatter('en_US', NumberFormatter::ORDINAL);

        return $numberFormatter->format($number);
    }

    public function swapSequence(int $sequence, string $language): bool
    {
        $current_sequence = $this->sequence;
        $complement = self::where('sequence', $sequence)
            ->where('language', $language)
            ->first();

        $complement->sequence = $current_sequence;
        $complement->save();

        $this->sequence = $sequence;

        return $this->save() && $complement->save();
    }

    public function shiftSequence(bool $shift_left = true): void
    {
        $succeeding_ordenance = self::where('sequence', $shift_left ? '>' : '>=', $this->sequence)
            ->where('language', $this->language);

        $succeeding_advertisments = $succeeding_ordenance->get();
        $total_succeeding = $succeeding_ordenance->count();

        if ($total_succeeding) {
            /** if there is a succeeding ordenance, shift the sequence */
            $succeeding_advertisments->each(function (Advertisement $advertisement) use ($shift_left) {

                /**
                 *  Shift the sequence of all succeding advertisements
                 *
                 *  ex. sequence [2, 3, 4, 5, 6, 7]
                 *
                 *  shift_left = true
                 *      [1, 2, 3, 4, 5, 6]
                 *  shift_left = false
                 *      [3, 4, 5, 6, 7, 8]
                 */
                $sequence = $advertisement->sequence;

                if ($shift_left) {
                    $sequence--;
                } else {
                    $sequence++;
                }

                $advertisement->sequence = $sequence;
                $advertisement->save();
            });
        }
    }
}
