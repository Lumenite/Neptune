<?php

namespace Lumenite\Neptune\Commands\Resources;

use Lumenite\Neptune\Commands\Command;
use Lumenite\Neptune\Resources\Secret;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class SecretCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resource:secret
    {app} 
    {version?}
    {--delete : Delete the job resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy/Delete Secret resource from the kubernetes cluster.';

    /**
     * @param Secret $secret
     * @throws \Exception
     */
    public function handle(Secret $secret)
    {
        $secret = $secret->load(
            $this->getResourceLoader()->getArtifactPath(),
            $this->getResourceLoader()->getValues()
        );

        $this->{$this->option('delete') ? 'delete' : 'apply'}($secret);
    }

    /**
     * @param Secret $secret
     */
    protected function apply(Secret $secret)
    {
        $response = $secret->apply();

        $this->info("Secret resource {$response->name()} deployed successfully.");
    }

    /**
     * @param Secret $secret
     */
    protected function delete(Secret $secret)
    {
        $secret->delete(function ($stdout) {
            $this->info($stdout);
        });
    }
}
