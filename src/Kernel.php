<?php

namespace Lumenite\Neptune;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as LumenKernel;
use Lumenite\Neptune\Commands\DockerPublishCommand;
use Lumenite\Neptune\Commands\Release\MakeReleaseCommand;
use Lumenite\Neptune\Commands\Release\PublishReleaseCommand;
use Lumenite\Neptune\Commands\Release\DeleteReleaseCommand;
use Lumenite\Neptune\Commands\Resources\AutoScaleCommand;
use Lumenite\Neptune\Commands\Resources\ConfigCommand;
use Lumenite\Neptune\Commands\Resources\DeploymentCommand;
use Lumenite\Neptune\Commands\Resources\JobCommand;
use Lumenite\Neptune\Commands\Resources\PersistenceVolumeClaimCommand;
use Lumenite\Neptune\Commands\Resources\SecretCommand;
use Lumenite\Neptune\Commands\Resources\ServiceCommand;

class Kernel extends LumenKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        DockerPublishCommand::class,
        MakeReleaseCommand::class,
        PublishReleaseCommand::class,
        DeleteReleaseCommand::class,
        DeploymentCommand::class,
        ConfigCommand::class,
        SecretCommand::class,
        ServiceCommand::class,
        AutoScaleCommand::class,
        JobCommand::class,
        PersistenceVolumeClaimCommand::class,
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
