<?php

namespace Lumenite\Neptune\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as LumenKernel;
use Lumenite\Neptune\Commands\ReleaseBuildCommand;
use Lumenite\Neptune\Commands\ReleaseDeleteCommand;
use Lumenite\Neptune\Commands\ReleaseCreateCommand;
use Lumenite\Neptune\Commands\Resources\ConfigCommand;
use Lumenite\Neptune\Commands\Resources\DeploymentCommand;
use Lumenite\Neptune\Commands\Resources\JobCommand;
use Lumenite\Neptune\Commands\Resources\SecretCommand;

class Kernel extends LumenKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ReleaseBuildCommand::class,
        ReleaseCreateCommand::class,
        ReleaseDeleteCommand::class,
        DeploymentCommand::class,
        ConfigCommand::class,
        SecretCommand::class,
        JobCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
