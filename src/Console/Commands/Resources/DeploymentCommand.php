<?php

namespace Lumenite\Neptune\Commands\Resources;

use Lumenite\Neptune\Commands\Command;
use Lumenite\Neptune\Resources\Deployment;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class DeploymentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resource:deployment 
    {app} 
    {version?}
    {--delete : Delete the job resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy/Delete Deployment resource from kubernetes cluster.';

    /**
     * @param Deployment $deployment
     * @throws \Exception
     */
    public function handle(Deployment $deployment)
    {
        $deployment = $deployment->load(
            $this->getResourceLoader()->getAppPath(),
            $this->getResourceLoader()->getValues()
        );

        $this->{$this->option('delete') ? 'delete' : 'apply'}($deployment);
    }

    /**
     * @param Deployment $deployment
     */
    protected function apply(Deployment $deployment)
    {
        $response = $deployment->apply();

        $this->info("Deployment resource {$response->name()} deployed successfully.");
    }

    /**
     * @param Deployment $deployment
     */
    protected function delete(Deployment $deployment)
    {
        $deployment->delete(function ($stdout) {
            $this->info($stdout);
        });
    }
}
