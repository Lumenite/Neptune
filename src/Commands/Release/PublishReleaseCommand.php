<?php

namespace Lumenite\Neptune\Commands\Release;

use Illuminate\Console\Command;
use Lumenite\Neptune\Release;
use Lumenite\Neptune\ResourceResponse\JobResponse;
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
    protected $signature = 'release:publish
    {app}
    {version?}
    {--production : Overwrite namespace to production from values.yml}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish a release to kubernetes cluster.';

    /** @var Release $release */
    protected $release;

    /**
     * @param Release $release
     * @throws \Lumenite\Neptune\Exceptions\ResourceDeploymentException|\Exception
     */
    public function handle(Release $release)
    {
        if ($this->option('production')) {
            $release->onProduction();
        }

        $this->release = $release = $release->load($this->argument('app'), $this->argument('version'));

        $release->getConfig()->apply();
        $this->info("ConfigMap deployed successfully.");

        $release->getSecret()->apply();
        $this->info("Secret deployed successfully.");

        $this->deployPersistentVolumeClaim($release->getDisk())->deployJob($release->getArtifact());

        $release->getService()->apply();
        $this->info("Service deployed successfully.");

        $release->getApp()->apply();
        $this->info("Deployment deployed successfully.");
    }

    /**
     * @param \Lumenite\Neptune\Resources\PersistentVolumeClaim $pvc
     * @return $this
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
        $pvc->wait(function () use ($bar) {
            $bar->advance();
        });
        $bar->finish();

        return $this;
    }

    /**
     * @param \Lumenite\Neptune\Resources\Job $job
     * @return \Lumenite\Neptune\ResourceResponse\ClusterResponse|mixed
     * @throws \Lumenite\Neptune\Exceptions\ResourceDeploymentException
     */
    protected function deployJob(Job $job)
    {
        return $job->apply(function ($stdout, JobResponse $response) use ($job) {
            $this->info("\n\nJob '{$response->name()}' initialized.");

            $job->wait()->follow(function ($stdout) {
                $this->line(trim($stdout));
            });
        });
    }
}
