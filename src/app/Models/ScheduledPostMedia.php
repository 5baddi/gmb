<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Models;

use BADDIServices\ClnkGO\Entities\ModelEntity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledPostMedia extends ModelEntity
{
    public const string SCHEDULED_POST_ID_COLUMN = 'scheduled_post_id';
    public const string PATH_COLUMN = 'path';
    public const string TYPE_COLUMN = 'type';

    public const string PHOTO_TYPE = 'photo';
    public const string VIDEO_TYPE = 'video';

    public const array TYPES = [
        self::PHOTO_TYPE    => 'PHOTO',
        self::VIDEO_TYPE    => 'VIDEO',
    ];

    public function scheduledPost(): BelongsTo
    {
        return $this->belongsTo(ScheduledPost::class, ScheduledPost::ID_COLUMN);
    }
}