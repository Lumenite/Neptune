<?php

namespace Lumenite\Neptune\Commands;

use Lumenite\Neptune\Release;
use Lumenite\Neptune\Resources\Job;
use Lumenite\Neptune\Resources\PersistentVolumeClaim;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class ReleaseCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'release:create {app} {version?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release application to kubernetes cluster.';

    /**
     * @param Release $release
     * @throws \Lumenite\Neptune\Exceptions\ResourceDeploymentException|\Exception
     */
    public function handle(Release $release)
    {
        $release = $release->load($this->argument('app'), $this->argument('version'));

        $release->getConfig()->apply();
        $this->info("ConfigMap release successfully.");

        $release->getSecret()->apply();
        $this->info("Secret release successfully.");

        $this->deployPersistentVolumeClaim($release->getDisk());

        # Give sometime for pvc to initialize
        sleep(3);

        $this->deployJob($release->getArtifact());

        $release->getApp()->apply();
        $this->info("Deployment release successfully.");
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
}
