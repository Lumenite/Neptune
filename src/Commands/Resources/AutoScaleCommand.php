<?php

namespace Lumenite\Neptune\Commands\Resources;

use Lumenite\Neptune\Commands\Command;
use Lumenite\Neptune\ResourceResponse\Response;
use Lumenite\Neptune\Resources\AutoScale;
use Lumenite\Neptune\Resources\Service;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class AutoScaleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resource:hpa
    {app}
    {version?}
    {--delete : Delete the job resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy/Delete horizontal pod autoscale resource from the kubernetes cluster.';

    /**
     * @param AutoScale $autoScale
     * @throws \Exception
     */
    public function handle(AutoScale $autoScale)
    {
        $autoScale = $autoScale->load(
            $this->getResourceLoader()->getHpaPath(),
            $this->getResourceLoader()->getValues()
        );

        $this->{$this->option('delete') ? 'delete' : 'apply'}($autoScale);
    }

    /**
     * @param AutoScale $service
     */
    protected function apply(AutoScale $autoScale)
    {
        $autoScale->apply(function ($stdout, Response $response) {
            $this->info("{$response->kind()}: {$response->name()} is been deployed.");
        });
    }

    /**
     * @param AutoScale $autoScale
     */
    protected function delete(AutoScale $autoScale)
    {
        $autoScale->delete(function ($stdout) {
            $this->info($stdout);
        });
    }
}
