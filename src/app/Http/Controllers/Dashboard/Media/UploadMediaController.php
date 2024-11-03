<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Media;

use Throwable;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use BADDIServices\ClnkGO\AppLogger;
use Illuminate\Support\Facades\Session;
use BADDIServices\ClnkGO\Models\ScheduledMedia;
use BADDIServices\ClnkGO\Http\Requests\ScheduledMediaRequest;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class UploadMediaController extends DashboardController
{
    public function __invoke(ScheduledMediaRequest $request): void
    {
        try {
            DB::beginTransaction();

            if (Str::isUuid($request->input('id'))) {
                ScheduledMedia::query()
                    ->find($request->input('id'))
                    ->forceDelete();
            }

            $files = $request->file('file', []);
            
            abort_if(empty($files), Response::HTTP_UNPROCESSABLE_ENTITY);

            abort_if(
                empty($this->user->googleCredentials?->getAccountId())
                || empty($this->user->googleCredentials?->getMainLocationId()),
                Response::HTTP_BAD_REQUEST
            );

            $isInstantly = empty($request->input('scheduled_date'));

            $scheduledAt = Carbon::parse(
                    sprintf(
                        '%s %s',
                        $request->input('scheduled_date', date('Y-M-d')),
                        $request->input('scheduled_time', '00:00')
                    ),
                    Session::get('timezone', 'UTC')
                )
                ->setTimezone('UTC');

            $paths = [];

            foreach ($files as $file) {
                if (! $file instanceof UploadedFile) {
                    continue;
                }

                $fileName = sprintf('%d%d_%s', time(), rand(1,99), $file->getClientOriginalName());
                $file->move(public_path('uploads'), $fileName);

                if (! $isInstantly) {
                    switch ($request->input(ScheduledMedia::SCHEDULED_FREQUENCY_COLUMN)) {
                        case ScheduledMedia::DAILY_SCHEDULED_FREQUENCY:
                            $scheduledAt = $scheduledAt->addDay();

                            break;
                        case ScheduledMedia::EVERY_3_DAYS_SCHEDULED_FREQUENCY:
                            $scheduledAt = $scheduledAt->addDays(3);

                            break;
                        case ScheduledMedia::WEEKLY_SCHEDULED_FREQUENCY:
                            $scheduledAt = $scheduledAt->addWeek();

                            break;
                    }
                }

                $paths[] = [
                    'path'  => sprintf('uploads/%s', $fileName),
                    'type'  => explode('/', $file->getClientMimeType())[0] ?? null,
                ];
            }

            ScheduledMedia::query()
                ->create([
                    ScheduledMedia::USER_ID_COLUMN      => $this->user->getId(),
                    ScheduledMedia::ACCOUNT_ID_COLUMN   => $this->user->googleCredentials->getAccountId(),
                    ScheduledMedia::LOCATION_ID_COLUMN  => $this->user->googleCredentials->getMainLocationId(),
                    ScheduledMedia::TYPE_COLUMN         => ScheduledMedia::PHOTO_TYPE,
                    ScheduledMedia::FILES_COLUMN        => $paths,
                    ScheduledMedia::STATE_COLUMN        => ScheduledMedia::UNSPECIFIED_STATE,
                    ScheduledMedia::SCHEDULED_AT_COLUMN => $scheduledAt->toISOString(),
                    ScheduledMedia::SCHEDULED_FREQUENCY_COLUMN
                    => $isInstantly ? null : $request->input(ScheduledMedia::SCHEDULED_FREQUENCY_COLUMN),
                ]);

            DB::commit();
        } catch (Throwable $e){
            DB::rollBack();
dd($e);
            AppLogger::error(
                $e,
                'scheduled-media:upload-new-media',
                ['payload' => $request->toArray()]
            );

            abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}