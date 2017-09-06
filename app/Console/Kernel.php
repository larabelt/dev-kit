<?php

namespace App\Console;

use App;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        App\Console\Commands\ComposeCommand::class,
        App\Console\Commands\BranchCommand::class,
        App\Console\Commands\InstallCommand::class,
        App\Console\Commands\PostComposeCommand::class,
        App\Console\Commands\PullCommand::class,
        App\Console\Commands\PushCommand::class,
        App\Console\Commands\RefreshCommand::class,
        App\Console\Commands\StatusCommand::class,
        App\Console\Commands\TagCommand::class,
        App\Console\Commands\TestCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
