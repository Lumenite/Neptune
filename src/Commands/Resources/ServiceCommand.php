<?php

namespace Lumenite\Neptune\Commands\Resources;

use Lumenite\Neptune\Commands\Command;
use Lumenite\Neptune\Resources\Service;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class ServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resource:service
    {app} 
    {version?}
    {--delete : Delete the job resource}';

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
            $this->getResourceLoader()->getArtifactPath(),
            $this->getResourceLoader()->getValues()
        );

        $this->{$this->option('delete') ? 'delete' : 'apply'}($service);
    }

    /**
     * @param Service $service
     */
    protected function apply(Service $service)
    {
        $response = $service->apply();

        $this->info("Service resource {$response->name()} deployed successfully.");
    }

    /**
     * @param Service $service
     */
    protected function delete(Service $service)
    {
        $service->delete(function ($stdout) {
            $this->info($stdout);
        });
    }
}
