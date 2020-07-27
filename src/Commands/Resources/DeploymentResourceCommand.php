<?php

namespace Lumenite\Neptune\Commands\Resources;

use Lumenite\Neptune\Resources\Deployment;
use Lumenite\Neptune\Resources\ResourceContract;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class DeploymentResourceCommand extends ResourceCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resource:deployment %s';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy/Delete Deployment resource from kubernetes cluster.';

    /**
     * @param \Lumenite\Neptune\Resources\Deployment $deployment
     * @throws \Lumenite\Neptune\Exceptions\DeploymentTerminatedException
     * @throws \Lumenite\Neptune\Exceptions\NotFoundException
     */
    public function handle(Deployment $deployment)
    {
        parent::defaultHandle($deployment);
    }

    /**
     * @param \Lumenite\Neptune\Resources\ResourceContract|Deployment $configMap
     * @return mixed|void
     * @throws \Lumenite\Neptune\Exceptions\NotFoundException
     */
    protected function apply(ResourceContract $configMap)
    {
        $response = $configMap->apply();
        $message = "Deployment %s is deployed successfully on %s.";

        $this->info(sprintf($message, $response->name(), $this->resourceLoader->get('namespace')));
    }

    /**
     * @param \Lumenite\Neptune\Resources\ResourceContract|Deployment $configMap
     */
    protected function delete(ResourceContract $configMap)
    {
        $configMap->delete(function ($stdout) {
            $this->info($stdout);
        });
    }
}
