<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Media;

use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;
use BADDIServices\ClnkGO\Models\ScheduledMedia;

class EditScheduledMediaController extends DashboardController
{
    public function __invoke(string $id = null): View|Factory
    {
        $scheduledMedia = ScheduledMedia::query()
            ->find($id);

        abort_unless($scheduledMedia instanceof ScheduledMedia, Response::HTTP_NOT_FOUND);

        return $this->render(
            'dashboard.media.scheduled.edit',
            [
                'title'         => trans('dashboard.scheduled_media'),
                'scheduledMedia'=> $scheduledMedia,
            ]
        );
    }
}