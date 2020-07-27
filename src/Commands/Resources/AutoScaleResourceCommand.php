<?php

namespace Lumenite\Neptune\Commands\Resources;

use Lumenite\Neptune\ResourceResponse\Response;
use Lumenite\Neptune\Resources\AutoScale;
use Lumenite\Neptune\Resources\ResourceContract;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class AutoScaleResourceCommand extends ResourceCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resource:hpa %s';

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
     * @param \Lumenite\Neptune\Resources\ResourceContract|AutoScale $autoScale
     * @return mixed|void
     */
    protected function apply(ResourceContract $autoScale)
    {
        $autoScale->apply(function ($stdout, Response $response) {
            $this->info("{$response->kind()}: {$response->name()} is been deployed.");
        });
    }

    /**
     * @param \Lumenite\Neptune\Resources\ResourceContract|AutoScale $autoScale
     * @return mixed|void
     */
    protected function delete(ResourceContract $autoScale)
    {
        $autoScale->delete(function ($stdout) {
            $this->info($stdout);
        });
    }
}
