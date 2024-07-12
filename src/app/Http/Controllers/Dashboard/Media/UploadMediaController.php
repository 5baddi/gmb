<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Media;

use Throwable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use BADDIServices\ClnkGO\Models\ScheduledMedia;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class UploadMediaController extends DashboardController
{
    public function __invoke(Request $request): void
    {
        try {
            $files = $request->file('file', []);
            abort_if(empty($files), Response::HTTP_UNPROCESSABLE_ENTITY);

            foreach ($files as $file) {
                if (! $file instanceof UploadedFile) {
                    continue;
                }

                $fileName = sprintf('%d%d_%s', time(), rand(1,99), $file->getClientOriginalName());
                $file->move(public_path('uploads'), $fileName);

                $scheduledAt = Carbon::createFromFormat(
                    'Y-M-d H:i',
                        sprintf(
                            '%s %s',
                            $request->input('scheduled_date', date('Y-M-d')),
                            $request->input('scheduled_time', '00:00')
                        )
                    )
                    ->toISOString();

                ScheduledMedia::query()
                    ->create([
                        ScheduledMedia::USER_ID_COLUMN  => $this->user->getId(),
                        ScheduledMedia::TYPE_COLUMN     => ScheduledMedia::PHOTO_TYPE,
                        ScheduledMedia::PATH_COLUMN     => sprintf('uploads/%s', $fileName),
                        ScheduledMedia::STATE_COLUMN    => ScheduledMedia::UNSPECIFIED_STATE,
                        ScheduledMedia::SCHEDULED_AT_COLUMN => $scheduledAt,
                    ]);
            }
        } catch (Throwable){
            abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}