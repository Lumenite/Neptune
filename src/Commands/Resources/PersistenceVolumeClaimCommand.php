<?php

namespace Lumenite\Neptune\Commands\Resources;

use Lumenite\Neptune\Commands\Command;
use Lumenite\Neptune\Resources\PersistentVolumeClaim;

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
    protected $signature = 'resource:pvc
    {app} 
    {version?}
    {--delete : Delete the job resource}';

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
     * @param PersistentVolumeClaim $pvc
     */
    protected function apply(PersistentVolumeClaim $pvc)
    {
        $response = $pvc->apply();

        $this->info("PVC resource {$response->name()} deployed successfully.");
    }

    /**
     * @param PersistentVolumeClaim $pvc
     */
    protected function delete(PersistentVolumeClaim $pvc)
    {
        $pvc->delete(function ($stdout) {
            $this->info($stdout);
        });
    }
}
