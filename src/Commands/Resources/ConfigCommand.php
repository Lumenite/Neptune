<?php

namespace Lumenite\Neptune\Commands\Resources;

use Lumenite\Neptune\Commands\Command;
use Lumenite\Neptune\Resources\Deployment;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class ConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resource:config 
    {app} 
    {version?}
    {--delete : Delete a config resource from the kubernetes cluster.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create/Delete ConfigMap resource the application in kubernetes cluster.';

    /**
     * @param Deployment $deployment
     * @throws \Exception
     */
    public function handle(Deployment $deployment)
    {
        $deployment = $deployment->load(
            $this->getResourceLoader()->getConfigPath(),
            $this->getResourceLoader()->getValues()
        );

        $this->{$this->option('delete') ? 'delete' : 'apply'}($deployment);
    }

    /**
     * @param Deployment $deployment
     * @throws \Exception
     */
    protected function apply(Deployment $deployment)
    {
        $response = $deployment->apply();

        $this->info("ConfigMap resource {$response->name()} deployed successfully.");
    }

    /**
     * @param Deployment $deployment
     */
    public function delete(Deployment $deployment)
    {
        $deployment->delete(function ($stdout) {
            $this->info("$stdout");
        });
    }
}
