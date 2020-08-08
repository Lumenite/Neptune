<?php

namespace Lumenite\Neptune\Commands\Resources;

use Lumenite\Neptune\Resources\ResourceContract;
use Lumenite\Neptune\Resources\Service;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class ServiceResourceCommand extends ResourceCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resource:service %s';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy/Delete Service resource from the kubernetes cluster.';

    /**
     * @param service $service
     * @throws \Exception
     */
    public function handle(service $service)
    {
        $service = $service->load(
            $this->getResourceLoader()->getServicePath(),
            $this->getResourceLoader()
        );

        $this->{$this->option('delete') ? 'delete' : 'apply'}($service);
    }

    /**
     * @param \Lumenite\Neptune\Resources\ResourceContract|Service $service
     * @return mixed|void
     */
    protected function apply(ResourceContract $service)
    {
        $response = $service->apply();

        $this->info("Service resource {$response->name()} deployed successfully.");
    }

    /**
     * @param \Lumenite\Neptune\Resources\ResourceContract|Service $service
     * @return mixed|void
     */
    protected function delete(ResourceContract $service)
    {
        $service->delete(function ($stdout) {
            $this->info($stdout);
        });
    }
}
