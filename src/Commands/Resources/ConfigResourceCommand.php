<?php

namespace Lumenite\Neptune\Commands\Resources;

use Lumenite\Neptune\Resources\ConfigMap;
use Lumenite\Neptune\Resources\ResourceContract;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class ConfigResourceCommand extends ResourceCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resource:config %s';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy/Delete ConfigMap resource the application in kubernetes cluster.';

    /**
     * @param \Lumenite\Neptune\Resources\ConfigMap $configMap
     * @throws \Lumenite\Neptune\Exceptions\DeploymentTerminatedException
     * @throws \Lumenite\Neptune\Exceptions\NotFoundException
     */
    public function handle(ConfigMap $configMap)
    {
        $configMap = $configMap->load(
            $this->getResourceLoader()->getConfigPath(),
            $this->getResourceLoader()
        );

        $this->{$this->option('delete') ? 'delete' : 'apply'}($configMap);
    }

    /**
     * @param \Lumenite\Neptune\Resources\ResourceContract $configMap
     * @return mixed|void
     */
    protected function apply(ResourceContract $configMap)
    {
        $response = $configMap->apply();

        $this->info("ConfigMap resource {$response->name()} deployed successfully.");
    }

    /**
     * @param \Lumenite\Neptune\Resources\ResourceContract $configMap
     * @return mixed|void
     */
    public function delete(ResourceContract $configMap)
    {
        $configMap->delete(function ($stdout) {
            $this->info("$stdout");
        });
    }
}
