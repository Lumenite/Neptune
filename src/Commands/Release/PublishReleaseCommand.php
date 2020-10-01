<?php

namespace Lumenite\Neptune\Commands\Release;

use Illuminate\Console\Command;
use Lumenite\Neptune\Exceptions\NotFoundException;
use Lumenite\Neptune\Exceptions\ResourceDeploymentException;
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
     * @param \Lumenite\Neptune\Release $release
     */
    public function __construct(Release $release)
    {
        $this->release = $release;

        parent::__construct();
    }

    /**
     * @throws \Lumenite\Neptune\Exceptions\ResourceDeploymentException
     * @throws \Exception
     */
    public function handle()
    {
        tap($this->release, function () {
            if ($this->option('production')) {
                $this->release->onProduction();
            }
        })->setFileNotFoundFailure(function (NotFoundException $exception) {
            $this->warn($exception->getMessage());
        })->load($this->argument('app'), $this->argument('version'));

        try {
            $this->release->getConfig()->apply();
            $this->info("ConfigMap deployed successfully.");
        } catch (ResourceDeploymentException $exception) {
            $this->warn($exception->getMessage());
        }

        try {
            $this->release->getSecret()->apply();
            $this->info("Secret deployed successfully.");
        } catch (ResourceDeploymentException $exception) {
            $this->warn($exception->getMessage());
        }

        try {
            $this->deployPersistentVolumeClaim($this->release->getDisk());
        } catch (ResourceDeploymentException $exception) {
            $this->warn($exception->getMessage());
        }

        try {
            $this->deployJob($this->release->getArtifact());
        } catch (ResourceDeploymentException $exception) {
            $this->warn($exception->getMessage());
        }

        $this->release->getService()->apply();
        $this->info("Service deployed successfully.");

        $this->release->getApp()->apply();
        $this->info("Deployment deployed successfully.");

        $this->call('config:sync', ['app' => $this->argument('app')]);
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
        tap($this->output->createProgressBar(10), function ($bar) use ($pvc) {
            $bar->start();
            $pvc->wait(function () use ($bar) {
                $bar->advance();
            });
            $bar->finish();
        });

        return $this;
    }
}
