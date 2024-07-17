<?php

namespace App\Console\Commands;

use Throwable;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Console\Command;
use BADDIServices\ClnkGO\AppLogger;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Collection;
use BADDIServices\ClnkGO\Services\UserService;
use BADDIServices\ClnkGO\Models\ScheduledMedia;
use BADDIServices\ClnkGO\Models\UserGoogleCredentials;
use BADDIServices\ClnkGO\Domains\GoogleMyBusinessService;

class AutoPostScheduledMediaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto-post:scheduled-media';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto post scheduled media';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(private readonly UserService $userService) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("Start posting scheduled media");
        $startTime = microtime(true);

        try {
            ScheduledMedia::query()
                ->where(ScheduledMedia::SCHEDULED_AT_COLUMN, '<', Carbon::now())
                ->where(ScheduledMedia::STATE_COLUMN, '!=', ScheduledMedia::REJECTED_STATE)
                ->chunkById(10, function (Collection $scheduledMedias) {
                    $scheduledMedias->each(function (ScheduledMedia $scheduledMedia) {
                        try {
                            $user = $this->userService->findById(
                                $scheduledMedia->getAttribute(ScheduledMedia::USER_ID_COLUMN)
                            );

                            if (
                                ! $user instanceof User
                                || ! $user->googleCredentials instanceof UserGoogleCredentials
                                || empty($user->googleCredentials->getAccessToken())
                                || empty($user->googleCredentials->getAccountId())
                                || empty($user->googleCredentials->getMainLocationId())
                            ) {
                                throw new Exception();
                            }

                            $googleMyBusinessService = new GoogleMyBusinessService(
                                $user->googleCredentials->getAccessToken(),
                                $user->googleCredentials->getAccountId(),
                                $user->googleCredentials->getMainLocationId()
                            );

                            if (
                                ! $googleMyBusinessService->createBusinessLocationMedia([
                                    'locationAssociation'   => [
                                        'category'          => 'ADDITIONAL',
                                    ],
                                    'mediaFormat'           => ScheduledMedia::PHOTO_TYPE,
                                    'sourceUrl'             => URL::asset(
                                        $scheduledMedia->getAttribute(ScheduledMedia::PATH_COLUMN)
                                    ),
                                ])
                            ) {
                                throw new Exception();
                            }

                            $scheduledMedia->forceDelete();
                        } catch (Throwable) {
                            $scheduledMedia->update([ScheduledMedia::STATE_COLUMN => ScheduledMedia::REJECTED_STATE]);
                        }
                    });
                });
        } catch (Throwable $e) {
            AppLogger::error(
                $e,
                sprintf('command:%s', $this->signature),
                ['execution_time' => (microtime(true) - $startTime)]
            );

            $this->error(sprintf("Error while posting scheduled media: %s", $e->getMessage()));

            return 0;
        }

        $this->info("Done posting scheduled media");

        return 0;
    }
}
