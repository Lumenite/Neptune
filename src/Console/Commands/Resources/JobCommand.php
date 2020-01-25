<?php

namespace Lumenite\Neptune\Commands\Resources;

use Lumenite\Neptune\Commands\Command;
use Lumenite\Neptune\Resources\Job;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class JobCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resource:job 
    {app} 
    {version?} 
    {--delete : Delete the job resource}';

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
     * @param Job $job
     */
    public function apply(Job $job)
    {
        $response = $job->apply();

        $this->info("Job resource {$response->name()} deployed successfully.");
    }

    /**
     * @param Job $job
     */
    public function delete(Job $job)
    {
        $job->delete(function ($stdout) {
            $this->info($stdout);
        });
    }
}
