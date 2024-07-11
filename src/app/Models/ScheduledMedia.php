<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Models;

use App\Models\User;
use BADDIServices\ClnkGO\Entities\ModelEntity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledMedia extends ModelEntity
{
    public const string USER_ID_COLUMN = 'user_id';
    public const string PATH_COLUMN = 'path';
    public const string TYPE_COLUMN = 'type';
    public const string STATE_COLUMN = 'state';
    public const string SCHEDULED_AT_COLUMN = 'scheduled_at';

    public const string PHOTO_TYPE = 'photo';
    public const string VIDEO_TYPE = 'video';

    public const array TYPES = [
        self::PHOTO_TYPE    => 'PHOTO',
        self::VIDEO_TYPE    => 'VIDEO',
    ];

    public const string UNSPECIFIED_STATE = 'unspecified';
    public const string REJECTED_STATE = 'rejected';

    public const array STATES = [
        self::UNSPECIFIED_STATE => 'UNSPECIFIED',
        self::REJECTED_STATE    => 'REJECTED',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, User::ID_COLUMN);
    }
}