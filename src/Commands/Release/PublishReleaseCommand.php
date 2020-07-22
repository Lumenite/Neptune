<?php

namespace Lumenite\Neptune\Commands\Release;

use Illuminate\Console\Command;
use Lumenite\Neptune\Release;
use Lumenite\Neptune\Resources\Job;
use Lumenite\Neptune\Resources\PersistentVolumeClaim;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class PublishReleaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'release:publish {app} {version?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish a release to kubernetes cluster.';

    /**
     * @param Release $release
     * @throws \Lumenite\Neptune\Exceptions\ResourceDeploymentException|\Exception
     */
    public function handle(Release $release)
    {
        $release = $release->load($this->argument('app'), $this->argument('version'));

        $release->getConfig()->apply();
        $this->info("ConfigMap deployed successfully.");

        $release->getSecret()->apply();
        $this->info("Secret deployed successfully.");

        $this->deployPersistentVolumeClaim($release->getDisk());

        # Give sometime for pvc to initialize
        sleep(3);

        $this->deployJob($release->getArtifact());

//        $this->followJob($release->getArtifact());

        $release->getService()->apply();
        $this->info("Service deployed successfully.");

        $release->getApp()->apply();
        $this->info("Deployment deployed successfully.");
    }

    /**
     * @param PersistentVolumeClaim $pvc
     * @return \Lumenite\Neptune\Kubectl|\Lumenite\Neptune\Resources\ResourceContract
     * @throws \Lumenite\Neptune\Exceptions\ResourceDeploymentException
     */
    protected function deployPersistentVolumeClaim(PersistentVolumeClaim $pvc)
    {
        $response = $pvc->apply();
        $this->info("PVC '{$response->name()}' created.");

        # Waiting for PVC to get initialized
        $this->info("Waiting for PVC '{$response->name()}' to get initialize.");
        $bar = $this->output->createProgressBar(10);
        $bar->start();
        $kubectl = $pvc->wait(function () use ($bar) {
            $bar->advance();
        });
        $bar->finish();

        return $kubectl;
    }

    /**
     * @param Job $job
     * @return \Lumenite\Neptune\Kubectl|\Lumenite\Neptune\Resources\ResourceContract
     * @throws \Lumenite\Neptune\Exceptions\ResourceDeploymentException
     */
    protected function deployJob(Job $job)
    {
        $response = $job->apply();
        $this->info("\n\nJob '{$response->name()}' initialized.");

        return $job->wait();
    }

    /**
     * @param Job $job
     * @throws \Lumenite\Neptune\Exceptions\ResourceDeploymentException
     */
    protected function followJob(Job $job)
    {
        $job->follow(function ($stdout) {
            $this->getOutput()->write($stdout);
        });
    }
}
