<?php

namespace Lumenite\Neptune\Commands\Resources;

use Lumenite\Neptune\Resources\Job;
use Lumenite\Neptune\Resources\ResourceContract;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class JobResourceCommand extends ResourceCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resource:job %s';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy/Delete Job resource from the kubernetes cluster.';

    /**
     * @param Job $job
     * @throws \Exception
     */
    public function handle(Job $job)
    {
        $job = $job->load($this->getResourceLoader()->getArtifactPath(), $this->getResourceLoader()->getValues());

        $this->{$this->option('delete') ? 'delete' : 'apply'}($job);
    }

    /**
     * @param \Lumenite\Neptune\Resources\ResourceContract $job
     * @return mixed|void
     */
    public function apply(ResourceContract $job)
    {
        $response = $job->apply(function ($stdout) {
            $this->line(trim($stdout));
        });

        $this->info("Job resource {$response->name()} deployed successfully.");
    }

    /**
     * @param \Lumenite\Neptune\Resources\ResourceContract $job
     * @return mixed|void
     */
    public function delete(ResourceContract $job)
    {
        $job->delete(function ($stdout) {
            $this->info($stdout);
        });
    }
}
