<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Bugsnag\BugsnagLaravel\OomBootstrapper;
use App\Console\Commands\AutoPostScheduledPostsCommand;
use App\Console\Commands\AutoPostScheduledMediaCommand;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        AutoPostScheduledPostsCommand::class,
        AutoPostScheduledMediaCommand::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule
            ->command('queue:work --queue=default,tweets,cpa --timeout=2000 --sleep=3 --tries=3 --daemon')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();

        $schedule
            ->command('auto-post:scheduled-posts')
            ->everyMinute()
            ->withoutOverlapping();

        $schedule
            ->command('auto-post:scheduled-media')
            ->everyMinute()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    protected function bootstrappers(): array
    {
        return array_merge([OomBootstrapper::class], parent::bootstrappers());
    }
}
