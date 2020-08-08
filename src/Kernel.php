<?php

namespace Lumenite\Neptune;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as LumenKernel;
use Lumenite\Neptune\Commands\DockerPublishCommand;
use Lumenite\Neptune\Commands\Release\DeleteReleaseCommand;
use Lumenite\Neptune\Commands\Release\MakeReleaseCommand;
use Lumenite\Neptune\Commands\Release\PublishReleaseCommand;
use Lumenite\Neptune\Commands\Resources\AutoScaleResourceCommand;
use Lumenite\Neptune\Commands\Resources\ConfigResourceCommand;
use Lumenite\Neptune\Commands\Resources\DeploymentResourceCommand;
use Lumenite\Neptune\Commands\Resources\JobResourceCommand;
use Lumenite\Neptune\Commands\Resources\PersistenceVolumeClaimResourceCommand;
use Lumenite\Neptune\Commands\Resources\SecretResourceCommand;
use Lumenite\Neptune\Commands\Resources\ServiceResourceCommand;
use Lumenite\Neptune\Commands\SyncConfigCommand;

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
        DeploymentResourceCommand::class,
        ConfigResourceCommand::class,
        SecretResourceCommand::class,
        ServiceResourceCommand::class,
        AutoScaleResourceCommand::class,
        JobResourceCommand::class,
        PersistenceVolumeClaimResourceCommand::class,
        SyncConfigCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
