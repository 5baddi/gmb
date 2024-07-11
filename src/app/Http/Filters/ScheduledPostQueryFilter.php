<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Filters;

use BADDIServices\ClnkGO\Models\ScheduledPost;

class ScheduledPostQueryFilter extends QueryFilter
{
    public function user(?string $filter = null)
    {
        if (empty($filter)) {
            return;
        }

        $this->builder->where(ScheduledPost::USER_ID_COLUMN, $filter);
    }
}