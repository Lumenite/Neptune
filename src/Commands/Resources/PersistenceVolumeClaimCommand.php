<?php

namespace Lumenite\Neptune\Commands\Resources;

use Lumenite\Neptune\Commands\Command;
use Lumenite\Neptune\Resources\PersistentVolumeClaim;
use Lumenite\Neptune\Resources\ResourceContract;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class PersistenceVolumeClaimCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resource:pvc %s';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy/Delete PVC resource from the kubernetes cluster.';

    /**
     * @param PersistentVolumeClaim $pvc
     * @throws \Exception
     */
    public function handle(PersistentVolumeClaim $pvc)
    {
        $pvc = $pvc->load(
            $this->getResourceLoader()->getDiskPath(),
            $this->getResourceLoader()->getValues()
        );

        $this->{$this->option('delete') ? 'delete' : 'apply'}($pvc);
    }

    /**
     * @param \Lumenite\Neptune\Resources\ResourceContract|PersistentVolumeClaim $pvc
     * @return mixed|void
     */
    protected function apply(ResourceContract $pvc)
    {
        $response = $pvc->apply();

        $this->info("PVC resource {$response->name()} deployed successfully.");
    }

    /**
     * @param \Lumenite\Neptune\Resources\ResourceContract|PersistentVolumeClaim $pvc
     * @return mixed|void
     */
    protected function delete(ResourceContract $pvc)
    {
        $pvc->delete(function ($stdout) {
            $this->info($stdout);
        });
    }
}
